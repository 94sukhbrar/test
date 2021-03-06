<?php

/**
 *@copyright : Amusoftech  < www.amusoftech.com >
 *@author	 :Ram Mohamad Singh <  er.amudeep@gmail.com >
 */
namespace app\controllers;

use app\components\TActiveForm;
use app\components\TController;
use app\models\LoginForm;
use app\models\User;
use app\models\search\User as UserSearch;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends TController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className()
                ],
                'rules' => [
                    [
                        'actions' => [
                            'confirm-email',
                            'add',
                            'view',
                            'update',
                            'logout',
                            'changepassword',
                            'profile-image',
                            'toggle',
                            'dashboard'
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return User::isUser();
                        }
                    ],
                    [
                        'actions' => [
                            'index',
                            'delete',
                            'add',
                            'view',
                            'update',
                            'delete',
                            'logout',
                            'changepassword',
                            'resetpassword',
                            'dashboard',
                            'profile-image',
                            'toggle',
                            'clear',
                            'recover',
                            'add-admin',
                            'mass'
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return User::isAdmin();
                        }
                    ],
                    
                    [
                        'actions' => [
                            'login',
                            'signup',
                            'resetpassword',
                            'recover',
                            'add-admin',
                            'confirm-email'
                        ],
                        'allow' => true,
                        'roles' => [
                            '?',
                            '*'
                        ]
                    ]
                ]
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'delete' => [
                        'post'
                    ]
                ]
            ]
        ];
    }

    public function actionClear()
    {
        $runtime = Yii::getAlias('@runtime');
        $this->cleanRuntimeDir($runtime);
        
        $this->cleanAssetsDir();
        return $this->goBack();
    }

    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $this->updateMenuItems();
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect($model->getUrl());
        } else {
            $this->updateMenuItems($model);
            return $this->render('view', [
                'model' => $model
            ]);
        }
    }

    public function actionAddAdmin()
    {
        $this->layout = "guest-main";
        $count = User::find()->count();
        if ($count != 0) {
            return $this->redirect([
                '/'
            ]);
        }
        $model = new User();
        $model->scenario = 'add-admin';
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return TActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->role_id = User::ROLE_ADMIN;
            $model->state_id = User::STATE_ACTIVE;
            if ($model->validate()) {
                $model->setPassword($model->password);
                $model->generatePasswordResetToken();
                if ($model->save()) {
                    \Yii::$app->user->login($model);
                    \Yii::$app->getSession()->setFlash('success', \Yii::t('app', "Wellcome $model->full_name"));
                    return $this->goBack([
                        'dashboard/index'
                    ]);
                } else {
                    \Yii::$app->getSession()->setFlash('error', "Error !!" . $model->getErrorsString());
                }
            } else {
                \Yii::$app->getSession()->setFlash('error', "Error !!" . $model->getErrorsString());
            }
        }
        return $this->render('add-admin', [
            'model' => $model
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionAdd()
    {
        $this->layout = 'main';
        $model = new User();
        $model->role_id = User::ROLE_USER;
        $model->state_id = User::STATE_ACTIVE;
        $model->scenario = 'signup';
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return TActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $image = UploadedFile::getInstance($model, 'profile_file');
            if (! empty($image)) {
                $image->saveAs(UPLOAD_PATH . $image->baseName . '.' . $image->extension);
                $model->profile_file = $image->baseName . '.' . $image->extension;
            }
            if ($model->validate()) {
                $model->scenario = 'add';
                $model->generatePasswordResetToken();
                
                if (defined('DISABLED_CONFIRMATION_MAIL') && (DISABLED_CONFIRMATION_MAIL == true)) {
                    $model->sendRegistrationMailtoUser($model);
                } else {
                    $model->state_id = User::STATE_ACTIVE;
                }
                
                $model->setPassword($model->password);
                if ($model->save()) {
                    \Yii::$app->getSession()->setFlash('success', \Yii::t('app', 'User Added Successfully.'));
                    return $this->redirect([
                        'view',
                        'id' => $model->id
                    ]);
                }
            } else {
                \Yii::$app->getSession()->setFlash('error', "Error !!" . $model->getErrorsString());
            }
        }
        $this->updateMenuItems($model);
        return $this->render('add', [
            'model' => $model
        ]);
    }

    public function actionRecover()
    {
        $this->layout = 'guest-main';
        $model = new User();
        $model->scenario = 'token_request';
        if (isset($_POST['User'])) {
            $email = trim($_POST['User']['email']);
            if ($email != null) {
                
                $user = User::findOne([
                    'email' => $email
                ]);
                if ($user) {
                    $user->generatePasswordResetToken();
                    if (! $user->save(false, [
                        'activation_key'
                    ])) {
                        throw new Exception(\Yii::t('app', "Cant Generate Authentication Key"));
                    }
                    $user->sendEmail();
                    \Yii::$app->session->setFlash('success', \Yii::t('app', 'Please check your email to reset your password.'));
                    return $this->redirect([
                        '/user/login'
                    ]);
                } else {
                    \Yii::$app->session->setFlash('error', \Yii::t('app', 'Email is not registered.'));
                }
            } else {
                $model->addError('error', \Yii::t('app', 'Email cannot be blank'));
            }
        }
        $this->updateMenuItems($model);
        return $this->render('requestPasswordResetToken', [
            'model' => $model
        ]);
    }

    public function actionResetpassword($token)
    {
        $this->layout = 'guest-main';
        $model = User::findByPasswordResetToken($token);
        if (! ($model)) {
            \Yii::$app->session->setFlash('error', \Yii::t('app', 'This URL is expired.'));
        }
        $newModel = new User([
            'scenario' => 'resetpassword'
        ]);
        if ($newModel->load(Yii::$app->request->post()) && $newModel->validate() && $model) {
            
            if (defined('ALLOW_PASSWORD_CHANGE')) {
                $model->setPassword($newModel->password);
                $model->removePasswordResetToken();
                if ($model->save()) {
                    \Yii::$app->session->setFlash('success', \Yii::t('app', 'New password is saved successfully.'));
                } else {
                    \Yii::$app->session->setFlash('error', \Yii::t('app', 'Error while saving new password.'));
                }
            } else {
                \Yii::$app->session->setFlash('error', \Yii::t('app', 'Password change disabled.'));
            }
        }
        $this->updateMenuItems($model);
        return $this->render('resetpassword', [
            'model' => $newModel
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $this->layout = 'main';
        $model = $this->findModel($id);
        $model->scenario = 'update';
        $post = \yii::$app->request->post();
        $old_image = $model->profile_file;
        $password = $model->password;
        
        if (Yii::$app->request->isAjax && $model->load($post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return TActiveForm::validate($model);
        }
        
        if ($model->load($post)) {
            if (! empty($post['User']['password']))
                $model->setPassword($post['User']['password']);
            else
                $model->password = $password;
            $model->profile_file = $old_image;
            $model->saveUploadedFile($model, 'profile_file', $old_image);
            if ($model->save()) {
                \Yii::$app->session->setFlash('success', \Yii::t('app', 'User Updated successfully.'));
                return $this->redirect($model->getUrl());
            } else {
                \Yii::$app->getSession()->setFlash('error', "Error !!" . $model->getErrorsString());
            }
        }
        
        $model->password = '';
        $this->updateMenuItems($model);
        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $this->updateMenuItems($model);
        
        if (\Yii::$app->user->id == $model->id || $model->role_id == User::ROLE_ADMIN) {
            \Yii::$app->session->setFlash('warrning', 'You are not allowed to perform this operation.');
            return $this->redirect(\Yii::$app->request->referrer);
        }
        $model->delete();
        if (\Yii::$app->request->isAjax) {
            return true;
        }
        \Yii::$app->session->setFlash('success', \Yii::t('app', 'User Deleted successfully.'));
        return $this->redirect([
            'index'
        ]);
    }

    public function actionConfirmEmail($id)
    {
        $user = User::find()->where([
            'activation_key' => $id
        ])->one();
        if (! empty($user)) {
            $user->email_verified = User::EMAIL_VERIFIED;
            if ($user->save()) {
                if (Yii::$app->user->login($user, 3600 * 24 * 30)) {
                    \Yii::$app->getSession()->setFlash('success', 'Congratulations! your account is verified');
                    return $this->redirect([
                        '/dashboard'
                    ]);
                }
            }
        } else {
            \Yii::$app->getSession()->setFlash('error', 'Token is Expired Please Resend Again');
            return $this->redirect([
                'dashboard'
            ]);
        }
    }

    public function actionSignup()
    {
        $this->layout = "guest-main";
        $model = new User([
            'scenario' => 'signup'
        ]);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->scenario = 'signup';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return TActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->state_id = User::STATE_ACTIVE;
            $model->role_id = User::ROLE_USER;
            $model->email_verified = User::EMAIL_NOT_VERIFIED;
            if ($model->validate()) {
                $model->scenario = 'add';
                $model->setPassword($model->password);
                $model->generatePasswordResetToken();
                if ($model->save()) {
                    \Yii::$app->user->login($model, 3600 * 24 * 30);
                    return $this->redirect([
                        '/dashboard'
                    ]);
                } else {
                    \Yii::$app->getSession()->setFlash('error', "Error !!" . $model->getErrorsString());
                }
            } else {
                \Yii::$app->getSession()->setFlash('error', "Error !!" . $model->getErrorsString());
            }
        }
        return $this->render('signup', [
            'model' => $model
        ]);
    }

    public function actionLogin()
    {
        $this->layout = "guest-main";
        if (! \Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
          
            return $this->goBack([
                'dashboard/index'
            ]);
        }
        return $this->render('login', [
            'model' => $model
        ]);
    }

    public function actionProfileImage()
    {
        return Yii::$app->user->identity->getProfileImage();
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionChangepassword($id)
    {
        $this->layout = 'main';
        $model = $this->findModel($id);
        if (! ($model->isAllowed()))
            throw new \yii\web\HttpException(403, Yii::t('app', 'You are not allowed to access this page.'));
        
        $newModel = new User([
            'scenario' => 'changepassword'
        ]);
        if (Yii::$app->request->isAjax && $newModel->load(Yii::$app->request->post())) {
            Yii::$app->response->format = 'json';
            return TActiveForm::validate($newModel);
        }
        if ($newModel->load(Yii::$app->request->post()) && $newModel->validate()) {
            $model->setPassword($newModel->newPassword);
            if ($model->save(false, [
                'password'
            ])) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Password Changed successfully'));
                return $this->redirect([
                    'dashboard/index'
                ]);
            } else {
                \Yii::$app->getSession()->setFlash('error', "Error !!" . $model->getErrorsString());
            }
        }
        $this->updateMenuItems($model);
        return $this->render('changepassword', [
            'model' => $newModel
        ]);
    }

    public function actionDashboard()
    {
        return $this->redirect([
            'dashboard/index'
        ]);
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            
            if (! ($model->isAllowed()))
                throw new HttpException(403, Yii::t('app', 'You are not allowed to access this page.'));
            
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function updateMenuItems($model = null)
    {
        switch (\Yii::$app->controller->action->id) {
            
            case 'add':
                {
                    $this->menu['add'] = [
                        'label' => '<span class="glyphicon glyphicon-list"></span>',
                        'title' => Yii::t('app', 'Manage'),
                        'url' => [
                            'index'
                        ],
                        'visible' => User::isAdmin()
                    ];
                }
                break;
            case 'index':
                {
                    $this->menu['add'] = [
                        'label' => '<span class="glyphicon glyphicon-plus"></span>',
                        'title' => Yii::t('app', 'Add'),
                        'url' => [
                            'add'
                        ],
                        'visible' => User::isAdmin()
                    ];
                }
                
                break;
            case 'update':
                {
                    $this->menu['add'] = [
                        'label' => '<span class="glyphicon glyphicon-plus"></span>',
                        'title' => Yii::t('app', 'add'),
                        'url' => [
                            'add'
                        ],
                        'visible' => User::isAdmin()
                    ];
                }
                break;
            case 'view':
                {
                    if ($model != null && ($model->role_id != User::ROLE_ADMIN) && \Yii::$app->hasModule('shadow'))
                        $this->menu['shadow'] = [
                            'label' => '<span class="glyphicon glyphicon-refresh ">Shadow</span>',
                            'title' => Yii::t('app', 'Login as ' . $model),
                            'url' => [
                                '/shadow/session/login',
                                'id' => $model->id
                            ],
								/* 'htmlOptions'=>[], */
								'visible' => User::isAdmin()
                        ];
                    $this->menu['add'] = [
                        'label' => '<span class="glyphicon glyphicon-plus"></span>',
                        'title' => Yii::t('app', 'Add'),
                        'url' => [
                            'add'
                        ],
                        'visible' => User::isAdmin()
                    ];
                    if ($model != null)
                        $this->menu['changepassword'] = [
                            'label' => '<span class="glyphicon glyphicon-paste"></span>',
                            'title' => Yii::t('app', 'changepassword'),
                            'url' => $model->getUrl('changepassword'),
                            
                            'visible' => User::isAdmin()
                        ];
                    if ($model != null)
                        $this->menu['update'] = [
                            'label' => '<span class="glyphicon glyphicon-pencil"></span>',
                            'title' => Yii::t('app', 'Update'),
                            'url' => $model->getUrl('update'),
                            
                            'visible' => User::isAdmin()
                        ];
                    $this->menu['manage'] = [
                        'label' => '<span class="glyphicon glyphicon-list"></span>',
                        'title' => Yii::t('app', 'Manage'),
                        'url' => [
                            'index'
                        ],
                        'visible' => User::isAdmin()
                    ];
                    if ($model != null)
                        $this->menu['delete'] = [
                            'label' => '<span class="glyphicon glyphicon-trash"></span>',
                            'title' => Yii::t('app', 'Delete'),
                            'url' => $model->getUrl('delete'),
                            'htmlOptions' => [
                                'data-method' => 'post'
                            ],
                            'visible' => User::isAdmin()
                        ];
                }
                break;
        }
    }
}
