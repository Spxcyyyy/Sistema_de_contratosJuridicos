<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\searchs\UserSearchs;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class UserController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => \app\components\AccessPolicy::accessBehavior(),
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $searchModel = new UserSearchs();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCreate()
    {
        $model = new User();
        $model->scenario = User::SCENARIO_CREATE;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->setPassword($model->password);
            $model->generateAuthKey();
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Usuario creado correctamente.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = User::SCENARIO_UPDATE;
        $model->password = null;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (!empty($model->password)) {
                $model->setPassword($model->password);
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Usuario actualizado correctamente.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $model->password = null;
        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ((string) $model->id === (string) Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'No puedes eliminar tu propia cuenta.');
            return $this->redirect(['index']);
        }

        if ($model->delete() === false) {
            throw new \yii\web\ServerErrorHttpException('No se pudo eliminar el usuario.');
        }
        return $this->redirect(['index']);
    }


    public function actionResetPassword($id)
    {
        $user = $this->findModel($id);
        $request = \app\models\PasswordResetRequest::find()
            ->where(['user_id' => $id, 'status' => 0])
            ->one();

        $model = new \app\models\ResetPasswordDirectForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user->setPassword($model->newPassword);
            $user->removePasswordResetToken();
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($user->save() && (!$request || $request->markResolved(Yii::$app->user->id))) {
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Contraseña restablecida. Comunícasela al usuario.');
                    return $this->redirect(['index']);
                }
                $transaction->rollBack();
                $model->addError('newPassword', 'No se pudo guardar el cambio: revisa los datos del usuario y su solicitud.');
            } catch (\Throwable $error) {
                $transaction->rollBack();
                throw $error;
            }
        }

        return $this->render('reset-password', [
            'model' => $model,
            'user' => $user,
        ]);
    }
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Usuario no encontrado.');
    }
}