<?php

namespace app\controllers;

use app\models\Firma;
use app\models\searchs\FirmaSearchs;
use app\components\ListReturnUrl;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Consulta de firmas; sus cambios se realizan desde el contrato asociado.
 */
class FirmaController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => \app\components\AccessPolicy::accessBehavior(),
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => ['seleccionar' => ['POST']],
                ],
            ]
        );
    }

    /**
     * Muestra las firmas registradas.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new FirmaSearchs();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Selecciona la firma mediante POST y abre su detalle sin ID en la URL.
     */
    public function actionSeleccionar()
    {
        $firma = $this->findModel(Yii::$app->request->post('id'));
        $listado = $this->request->post(ListReturnUrl::PARAM);
        $this->guardarSeleccion($firma, is_string($listado) ? $listado : '');
        return $this->redirect(['view']);
    }

    /**
     * Muestra el detalle seleccionado. Los enlaces antiguos se redirigen a la URL sin ID.
     */
    public function actionView($id = null)
    {
        if ($id !== null) {
            $this->guardarSeleccion($this->findModel($id));
            return $this->redirect(['view']);
        }

        $seleccion = Yii::$app->session->get('firmaDetalleSeleccionada');
        if (!is_array($seleccion) || ($seleccion['usuario'] ?? null) !== (int) Yii::$app->user->id) {
            throw new NotFoundHttpException('Selecciona una firma desde el listado.');
        }

        return $this->render('view', [
            'model' => $this->findModel($seleccion['firma'] ?? null),
            'listado' => $seleccion['listado'] ?? '',
        ]);
    }

    private function guardarSeleccion(Firma $firma, string $listado = ''): void
    {
        Yii::$app->session->set('firmaDetalleSeleccionada', [
            'usuario' => (int) Yii::$app->user->id,
            'firma' => $firma->id,
            'listado' => $listado,
        ]);
    }

    /**
     * Finds the Firma model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int|string|null $id ID interno recibido por POST o desde un enlace antiguo
     * @return Firma the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Firma::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('La firma solicitada no existe.');
    }
}
