<?php

namespace app\components;

use app\models\Contrato;
use app\models\Firma;
use app\models\User;
use yii\base\BaseObject;
use yii\web\NotFoundHttpException;
use yii\web\UrlRuleInterface;

/** Public paths resolve to internal IDs without changing controller authorization. */
class PublicRecordUrlRule extends BaseObject implements UrlRuleInterface
{
    private const ROUTES = [
        'contrato/view' => ['contratos', Contrato::class, 'codigo', ''],
        'contrato/update' => ['contratos', Contrato::class, 'codigo', '/editar'],
        'contrato/delete' => ['contratos', Contrato::class, 'codigo', '/eliminar'],
        'contrato/add-nota' => ['contratos', Contrato::class, 'codigo', '/notas'],
        'contrato/marcar-firmado' => ['firmas', Firma::class, 'referencia_publica', '/firmar'],
        'firma/view' => ['firmas', Firma::class, 'referencia_publica', ''],
        'firma/update' => ['firmas', Firma::class, 'referencia_publica', '/editar'],
        'firma/delete' => ['firmas', Firma::class, 'referencia_publica', '/eliminar'],
        'user/view' => ['usuarios', User::class, 'referencia_publica', ''],
        'user/update' => ['usuarios', User::class, 'referencia_publica', '/editar'],
        'user/delete' => ['usuarios', User::class, 'referencia_publica', '/eliminar'],
        'user/reset-password' => ['usuarios', User::class, 'referencia_publica', '/restablecer-contrasena'],
    ];

    private array $references = [];

    public function createUrl($manager, $route, $params)
    {
        if (!isset(self::ROUTES[$route]) || !isset($params['id']) || !is_scalar($params['id'])) {
            return false;
        }
        [$prefix, $class, $attribute, $suffix] = self::ROUTES[$route];
        $key = $class . ':' . $params['id'];
        if (!array_key_exists($key, $this->references)) {
            $this->references[$key] = $class::find()->select($attribute)->where(['id' => $params['id']])->scalar();
        }
        $reference = $this->references[$key];
        unset($params['id']);
        $path = $prefix . '/' . ($reference ? rawurlencode($reference) : 'no-disponible') . $suffix;
        return $path . ($params ? '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986) : '');
    }

    public function parseRequest($manager, $request)
    {
        $path = $request->getPathInfo();
        foreach (self::ROUTES as $route => [$prefix, $class, $attribute, $suffix]) {
            $pattern = '~\A' . preg_quote($prefix, '~') . '/([^/]+)' . preg_quote($suffix, '~') . '\z~u';
            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }
            $reference = $matches[1];
            if ($attribute === 'referencia_publica' && !preg_match('/\A[a-f0-9]{24}\z/', $reference)) {
                throw new NotFoundHttpException('Registro no encontrado.');
            }
            $id = $class::find()->select('id')->where([$attribute => $reference])->scalar();
            if ($id === false) {
                throw new NotFoundHttpException('Registro no encontrado.');
            }
            // Route parameters take precedence over query parameters in Yii Request::resolve().
            return [$route, ['id' => $id]];
        }
        return false;
    }
}
