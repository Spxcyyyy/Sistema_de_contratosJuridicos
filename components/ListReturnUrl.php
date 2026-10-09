<?php

namespace app\components;

use Yii;
use yii\helpers\Url;

final class ListReturnUrl
{
    public const PARAM = 'listado';

    public static function query(): ?string
    {
        $query = Yii::$app->request->get(self::PARAM);
        return is_string($query) ? $query : null;
    }

    public static function url(string $route, ?string $query = null): string
    {
        $query ??= self::query();
        $url = Url::to(['/' . ltrim($route, '/')]);
        return $query !== null && $query !== '' ? $url . '?' . $query : $url;
    }

    public static function preserve(array $route): array
    {
        $query = self::query();
        if ($query !== null) {
            $route[self::PARAM] = $query;
        }
        return $route;
    }
}
