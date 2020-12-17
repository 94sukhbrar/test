<?php
namespace app\components;

class WebUser extends \yii\web\User
{

    public $enableAutoLogin = true;

    public $identityClass = 'app\models\User';

    public $loginUrl = [
        '/user/login'
    ];

    public $authTimeout = 86400;

    public $identityCookie = [
        'name' => '_yii2Base',
        'path' => '/yii2-base-admin-panel-api'
    ];

    public function afterLogin($identity, $cookieBased, $duration)
    {
        $identity->last_visit_time = date('Y-m-d H:i:s');
        $identity->save();
        return parent::afterLogin($identity, $cookieBased, $duration);
    }
}