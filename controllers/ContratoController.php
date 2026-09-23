<?php

namespace app\controllers;

use app\models\Contrato;
use app\models\Firma;
use app\models\searchs\ContratoSearchs;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use yii\helpers\Html;

/**
 * ContratoController implements the CRUD actions for Contrato model.
 */
class ContratoController extends Controller
{
    public function actionActividad()
    {
        return $this->render('actividad', [
            'actividadProvider' => \app\components\AccessPolicy::allows('contrato/actividad') ? new \yii\data\ActiveDataProvider([
                'query' => \app\models\ContratoActividad::find()->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC]),
                'pagination' => ['pageSize' => 20, 'pageSizeParam' => false],
                'sort' => false,
            ]) : null,
        ]);
    }

    public function actionIndex()
    {
        $searchModel = new ContratoSearchs();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Contrato model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'actividadProvider' => \app\components\AccessPolicy::allows('contrato/actividad') ? new \yii\data\ActiveDataProvider([
                'query' => \app\models\ContratoActividad::find()->where(['contrato_id' => $id])->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC]),
                'pagination' => ['pageSize' => 20, 'pageParam' => 'actividad-page', 'pageSizeParam' => false],
                'sort' => false,
            ]) : null,
        ]);
    }


    public function actionCreate()
    {
        $model = new Contrato();
        $model->codigo = Contrato::generarCodigo();
        $firmas = [new Firma()];
        if ($model->load(Yii::$app->request->post())) {
            $firmas = $this->cargarFirmas($model);
            if ($guardado = $this->guardarContrato($model, $firmas)) {
                return $this->redirect(['view', 'id' => $guardado->id]);
            }
        }
        return $this->render('create', ['model' => $model, 'firmas' => $firmas]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $firmas = $model->firmas ?: [new Firma()];
        if ($model->load(Yii::$app->request->post())) {
            $firmas = $this->cargarFirmas($model);
            if ($guardado = $this->guardarContrato($model, $firmas)) {
                return $this->redirect(['view', 'id' => $guardado->id]);
            }
        }
        return $this->render('update', ['model' => $model, 'firmas' => $firmas]);
    }

    /** Conserva los datos y errores de cada fila sin aceptar IDs de otros contratos. */
    private function cargarFirmas(Contrato $model): array
    {
        $datos = Yii::$app->request->post('Firma', []);
        if (!is_array($datos)) {
            $firma = new Firma();
            $firma->addError('nombre', 'Los datos de los firmantes no son válidos.');
            return [$firma];
        }
        $existentes = $model->isNewRecord ? [] : $model->getFirmas()->indexBy('id')->all();
        $firmas = [];
        $vistos = [];
        foreach ($datos as $fila) {
            $firma = new Firma();
            if (!is_array($fila)) {
                $firma->addError('nombre', 'Los datos del firmante no son válidos.');
            } else {
                $id = $fila['id'] ?? null;
                if ($id !== null && $id !== '') {
                    if (!is_scalar($id) || !isset($existentes[$id]) || isset($vistos[$id])) {
                        $firma->addError('nombre', 'El firmante no pertenece al contrato o está duplicado.');
                    } else {
                        $firma = clone $existentes[$id];
                        $vistos[$id] = true;
                    }
                }
                $nombre = $fila['nombre'] ?? '';
                if (!is_string($nombre)) {
                    $firma->addError('nombre', 'El nombre del firmante debe ser texto.');
                } else {
                    $firma->nombre = $nombre;
                    // Una fila nueva vacía es opcional; un firmante existente no se borra al vaciarlo.
                    if ($nombre !== '' || !$firma->isNewRecord) {
                        $firma->validate(['nombre'], false);
                    }
                }
            }
            $firmas[] = $firma;
        }
        return $firmas;
    }

    private function guardarContrato(Contrato $model, array $firmas): ?Contrato
    {
        $valido = $model->validate();
        foreach ($firmas as $firma) {
            $valido = !$firma->hasErrors() && $valido;
        }
        if (!$valido) {
            return null;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Guardar copias evita mostrar IDs generados que se revierten ante un fallo.
            $guardado = clone $model;
            if (!$guardado->save()) {
                $model->addErrors($guardado->getErrors());
                $transaction->rollBack();
                return null;
            }
            $this->guardarFirmas($guardado, $firmas);
            $transaction->commit();
            return $guardado;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e, __METHOD__);
            Yii::$app->session->setFlash('error', 'No se pudo guardar el contrato con sus firmas. Revisa los datos e inténtalo de nuevo.');
            return null;
        }
    }

    private function guardarFirmas(Contrato $model, ?array $firmas = null): void
    {
        $firmas ??= $this->cargarFirmas($model);
        foreach ($firmas as $firma) {
            if ($firma->hasErrors()) {
                throw new \InvalidArgumentException('Firmante inválido.');
            }
        }
        $existentes = $model->getFirmas()->indexBy('id')->all();
        $conservar = [];
        foreach ($firmas as $fila) {
            if ($fila->isNewRecord && $fila->nombre === '') {
                continue;
            }
            $firma = clone $fila;
            $firma->contrato_id = $model->id;
            if (!$firma->save()) {
                $fila->addErrors($firma->getErrors());
                throw new \RuntimeException('No se pudo guardar el firmante.');
            }
            $conservar[$firma->id] = true;
        }
        foreach ($existentes as $id => $firma) {
            if (!isset($conservar[$id]) && $firma->delete() === false) {
                throw new \RuntimeException('No se pudo quitar al firmante.');
            }
        }
        $model->refresh();
        $model->actualizarEstadoSiCompleto();
    }

    public function actionDelete($id)
    {
        if ($this->findModel($id)->delete() === false) {
            throw new \yii\web\ServerErrorHttpException('No se pudo eliminar el registro.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Contrato model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Contrato the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Contrato::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => \app\components\AccessPolicy::accessBehavior(),
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                        'marcar-firmado' => ['POST'],
                        'add-nota' => ['POST'],
                    ],
                ],
            ]
        );
    }

    private $columnasDisponibles = \app\models\ReporteContratoForm::COLUMNAS;

    public function actionReporte()
    {
        $reporte = new \app\models\ReporteContratoForm();
        if (Yii::$app->request->isPost) {
            $reporte->load(Yii::$app->request->post(), '');
            if (Yii::$app->request->post('paso') !== 'preparar' && !Yii::$app->request->post('columnas')) {
                $reporte->columnas = [];
            }
            if ($reporte->validate() && Yii::$app->request->post('paso') !== 'preparar') {
                $contratos = $reporte->crearConsulta()->all();
                if ($reporte->alcance === 'seleccion' && count($contratos) !== count($reporte->ids)) {
                    $reporte->addError('ids_json', 'La selección cambió mientras se generaba el reporte. Revisa los contratos seleccionados.');
                } elseif (!$contratos) {
                    $reporte->addError('ids_json', 'No hay contratos que coincidan con el reporte.');
                } else {
                    return match ($reporte->formato) {
                        'xlsx' => $this->generarExcel($contratos, $reporte->columnas),
                        'csv' => $this->generarCsv($contratos, $reporte->columnas),
                        default => $this->generarPdf($contratos, $reporte->columnas),
                    };
                }
            }
        }
        $seleccionados = $reporte->alcance === 'seleccion' && !$reporte->hasErrors('ids_json')
            ? Contrato::find()->select(['id', 'codigo'])->where(['id' => $reporte->ids])->orderBy(['codigo' => SORT_ASC])->asArray()->all() : [];
        return $this->render('reporte', ['reporte' => $reporte, 'seleccionados' => $seleccionados]);
    }

    private function formatearValor($contrato, $columna)
    {
        $valor = $contrato->$columna;
        if (in_array($columna, ['fecha_documento', 'fecha_vencimiento'])) {
            return $valor ? Yii::$app->formatter->asDate($valor, 'php:d/m/Y') : '';
        }
        if ($columna === 'created_at') {
            return $valor ? Yii::$app->formatter->asDatetime($valor) : '';
        }
        if ($columna === 'costo') {
            return $valor !== null ? Yii::$app->formatter->asCurrency($valor, 'MXN') : '';
        }
        return $valor;
    }

    private function generarPdf($contratos, $columnas)
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        $html = '<h2 style="color:#6B1E3C;">Reporte de contratos</h2>';
        $html .= '<table border="1" cellpadding="6" cellspacing="0" style="width:100%; border-collapse: collapse; font-size: 11px;">';
        $html .= '<thead><tr style="background:#6B1E3C; color:#fff;">';
        foreach ($columnas as $col) {
            $html .= '<th>' . Html::encode($this->columnasDisponibles[$col] ?? $col) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($contratos as $contrato) {
            $html .= '<tr>';
            foreach ($columnas as $col) {
                $html .= '<td>' . Html::encode($this->formatearValor($contrato, $col)) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'landscape');
        $dompdf->render();

        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'application/pdf');
        Yii::$app->response->headers->set('Content-Disposition', 'attachment; filename="contratos_' . date('Y-m-d') . '.pdf"');
        return $dompdf->output();
    }

    private function generarExcel($contratos, $columnas)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $col = 1;
        foreach ($columnas as $key) {
            $sheet->setCellValueExplicit([$col, 1], $this->columnasDisponibles[$key], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $col++;
        }

        $row = 2;
        foreach ($contratos as $contrato) {
            $col = 1;
            foreach ($columnas as $key) {
                $sheet->setCellValueExplicit([$col, $row], (string) $this->formatearValor($contrato, $key), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $col++;
            }
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        Yii::$app->response->headers->set('Content-Disposition', 'attachment; filename="contratos_' . date('Y-m-d') . '.xlsx"');

        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }

    private function generarCsv($contratos, $columnas)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $col = 1;
        foreach ($columnas as $key) {
            $sheet->setCellValueExplicit([$col, 1], $this->columnasDisponibles[$key], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $col++;
        }

        $row = 2;
        foreach ($contratos as $contrato) {
            $col = 1;
            foreach ($columnas as $key) {
                $valor = (string) $this->formatearValor($contrato, $key);
                if (preg_match('/^[=+@\x09\x0a\x0d-]/', $valor)) { $valor = "'" . $valor; }
                $sheet->setCellValueExplicit([$col, $row], $valor, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $col++;
            }
            $row++;
        }

        $writer = new Csv($spreadsheet);

        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'text/csv');
        Yii::$app->response->headers->set('Content-Disposition', 'attachment; filename="contratos_' . date('Y-m-d') . '.csv"');

        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }

    public function actionMarcarFirmado($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $firma = \app\models\Firma::findOne($id);

        if (!$firma) {
            return ['success' => false, 'message' => 'Firma no encontrada.'];
        }

        if (!$firma->marcarComoFirmado()) {
            return ['success' => false, 'message' => 'No se pudo actualizar la firma.'];
        }

        $contrato = $firma->contrato;

        return [
            'success' => true,
            'fecha_firma' => Yii::$app->formatter->asDatetime($firma->fecha_firma),
            'contrato_estado' => $contrato->estado,
            'todas_firmadas' => $contrato->estado === 'Todas las firmas recabadas',
        ];
    }


    public function actionAddNota($id)
    {
        $contrato = $this->findModel($id); // ajusta al nombre real de tu método findModel

        $nota = new \app\models\ContratoNota();
        $nota->contrato_id = $contrato->id;
        $nota->user_id = Yii::$app->user->id;
        $nota->contenido = Yii::$app->request->post('contenido');

        if ($nota->validate() && $nota->save()) {
            Yii::$app->session->setFlash('success', 'Nota agregada.');
        } else {
            Yii::$app->session->setFlash('error', 'No se pudo guardar la nota.');
        }

        return $this->redirect(['view', 'id' => $contrato->id]);
    }

}
