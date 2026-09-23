<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\ContactForm;
use app\models\Contrato;
use app\models\LoginForm;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\base\Security;
use yii\mail\MailerInterface;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

class SiteController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly MailerInterface $mailer,
        private readonly Security $security,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'request-password-reset', 'contact', 'about', 'captcha', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => static fn($rule, $action) => \app\components\AccessPolicy::allows($action->uniqueId),
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'transparent' => true,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): Response|string
    {
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isRecabador()) {
            return $this->redirect(['/contrato/index']);
        }
        $stats = null;

        if (!Yii::$app->user->isGuest) {
            return $this->render('dashboard', [
                'stats' => [
                    'total' => Contrato::find()->count(),
                    'proceso' => Contrato::find()->enProceso()->count(),
                    'concluidos' => Contrato::find()->concluidos()->count(),
                    'firmas' => \app\models\Firma::find()->where(['estado' => 'pendiente'])
                        ->andWhere(['contrato_id' => Contrato::find()->select('id')->enProceso()])->count(),
                    'vencidos' => Contrato::find()->vencidos()->count(),
                    'proximos' => Contrato::find()->proximos()->count(),
                ],
                'vencidos' => Contrato::find()->vencidos()->orderBy(['fecha_vencimiento' => SORT_ASC, 'id' => SORT_ASC])->limit(5)->all(),
                'proximos' => Contrato::find()->proximos()->orderBy(['fecha_vencimiento' => SORT_ASC, 'id' => SORT_ASC])->limit(5)->all(),
                'actividades' => \app\models\ContratoActividad::find()->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])->limit(6)->all(),
            ]);
        }

        return $this->render('index', ['stats' => $stats]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm($this->security);

        if ($model->load($this->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', ['model' => $model]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionRequestPasswordReset(): Response|string
    {
        $model = new \app\models\PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->sendEmail();
            Yii::$app->session->setFlash('success', 'Tu solicitud fue registrada. Un administrador se pondrá en contacto contigo.');
            return $this->goHome();
        }

        return $this->render('requestPasswordResetToken', ['model' => $model]);
    }



    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact(): Response|string
    {
        $model = new ContactForm();

        $contact = $model->load($this->request->post()) && $model->contact(
            $this->mailer,
            Yii::$app->params['adminEmail'],
            Yii::$app->params['senderEmail'],
            Yii::$app->params['senderName'],
        );

        if ($contact) {
            Yii::$app->session->setFlash(
                'success',
                'Thank you for contacting us. We will respond to you as soon as possible.',
            );

            return $this->refresh();
        }

        return $this->render('contact', ['model' => $model]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout(): string
    {
        return $this->render('about');
    }
}
