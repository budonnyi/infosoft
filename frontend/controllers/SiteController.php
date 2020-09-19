<?php

namespace frontend\controllers;

use common\models\Deposits;
use common\models\Transactions;
use common\models\User;
use common\models\Wallets;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    // todo uncomment for prod
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup', 'enter'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'enter', 'deposit'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $wallet = Wallets::findOne(['user_id' => Yii::$app->user->id]);

        return $this->render('index', [
            'wallet' => $wallet
        ]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionEnter()
    {
        $model = new \common\models\Transactions();

        if ($model->load(Yii::$app->request->post())) {

            $userId = Yii::$app->user->identity->id;
            $userData = User::find(['id' => $userId])->with(['wallets'])->one();

            $model->user_id = $userId;
            $model->wallet_id = $userData->wallets->id;
            $model->type = 'enter';
            if ($model->save()) {
                $ballance = Wallets::findOne(['user_id' => $userId]);
                $ballance->ballance += $model->amount;
                $ballance->update();

                Yii::$app->session->setFlash('success', 'Баланс пополнен на ' . $model->amount . ' единиц');
            }

            // form inputs are valid, do something here
            return $this->redirect('index');
        }

        return $this->render('enter', [
            'model' => $model,
        ]);
    }

    public function actionDeposit()
    {
        $model = new \common\models\Transactions(['scenario' => 'deposit']);

        if ($model->load(Yii::$app->request->post())) {

            $userId = Yii::$app->user->identity->id;
            $userData = User::find(['id' => $userId])->with(['deposits', 'wallets'])->one();

            if ($model->amount > $userData->wallets->ballance) {
                Yii::$app->session->setFlash('error', 'Недостаточно единиц для депозита.');
                return false;
            }

            $model->user_id = $userId;
            $model->wallet_id = $userData->wallets->id;
            $model->type = 'create_deposit';
            $model->save();

            $deposit = new Deposits();
            $deposit->user_id = $userId;
            $deposit->wallet_id = $userData->wallets->id;
            $deposit->invested += $model->amount;
            $deposit->percent = Deposits::DEPOSIT_PERCENT;
            $deposit->active = Deposits::STATUS_ACTIVE;
            $deposit->duration = 0;
            $deposit->accrue_times = 0;
            $deposit->save(false);

            $wallet = Wallets::findOne($userData->wallets->id);
            $wallet->ballance -= $model->amount;

            if ($wallet->save()) {
                Yii::$app->session->setFlash('success', 'На депозит оплачено ' . $model->amount . ' единиц');
                return $this->redirect('index');
            }
        }

        return $this->render('deposit', [
            'model' => $model,
        ]);
    }

    public function actionIncrease()
    {
        $deposits = Deposits::find()->where(['!=', 'active', Deposits::STATUS_CLOSE])->all();

        foreach ($deposits as $depositItem) {
            $depositItem->accrue_times++;
            $depositItem->duration += 60;
            if ($depositItem->accrue_times == 10) {

                $transaction = new Transactions();
                $transaction->type = 'close_deposit';
                $transaction->user_id = $depositItem->user_id;
                $transaction->wallet_id = $depositItem->wallet_id;
                $transaction->amount = $depositItem->invested;
                $transaction->save(false);

                $depositItem->active = Deposits::STATUS_CLOSE;
            }

            $depositItem->save(false);

            $walletModel = Wallets::findOne(['id' => $depositItem->wallet_id]);
            $walletModel->ballance += round($depositItem->invested * $depositItem->percent / 100, 2);
            $walletModel->save(false);
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Now you can login in system.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @return yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {


            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }
}
