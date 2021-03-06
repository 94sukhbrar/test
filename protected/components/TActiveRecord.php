<?php
namespace app\components;

use app\models\Comment;
use app\models\File;
use app\models\User;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;

/**
 * This is the generic model class
 */
class TActiveRecord extends ActiveRecord
{

    public static function findActive($state_id = 1)
    {
        return Yii::createObject(ActiveQuery::className(), [
            get_called_class()
        ])->andWhere([
            'state_id' => $state_id
        ]);
    }

    public static function label($n = 1)
    {
        $className = Inflector::camel2words(StringHelper::basename(self::className()));
        if ($n == 2)
            return Inflector::pluralize($className);
        return $className;
    }

    public function __toString()
    {
        return $this->label(1);
    }

    public function getStateBadge()
    {
        return '';
    }

    public static function getStateOptions()
    {
        return [];
    }

    public function isAllowed()
    {
        if (User::isAdmin())
            return true;
        if ($this instanceof User) {
            return ($this->id == Yii::$app->user->id);
        }
        if ($this instanceof self) {
            if ($this->created_by_id == Yii::$app->user->id)
                return ($this->created_by_id == Yii::$app->user->id);
        }
        return false;
    }

    public function displayImage($file, $options = [], $defaultImg = 'default.png')
    {
        $opt = [
            'class' => 'img-responsive',
            'id' => 'profile_file'
        ];
        
        $arr = array_merge($opt, $options);
        
        if (! empty($file) && file_exists(UPLOAD_PATH . '/' . $file)) {
            return Html::img([
                '/user/download',
                'profile_file' => $file
            ], $arr);
        } else {
            return Html::img(\Yii::$app->view->theme->getUrl('/img/') . $defaultImg, $arr);
        }
    }

    public function saveUploadedFile($model, $attribute = 'image_file', $old = null)
    {
        $uploaded_file = UploadedFile::getInstance($model, $attribute);
        if ($uploaded_file != null) {
            $path = UPLOAD_PATH;
            $filename = $path . \yii::$app->controller->id . '-' . time() . '-' . $attribute . 'user_id_' . Yii::$app->user->id . '.' . $uploaded_file->extension;
            if (file_exists($filename))
                unlink($filename);
            if (! empty($old) && file_exists(UPLOAD_PATH . $old))
                unlink(UPLOAD_PATH . $old);
            $uploaded_file->saveAs($filename);
            $model->$attribute = basename($filename);
            return true;
        }
        return false;
    }

    public function beforeDelete()
    {
        if ($this->hasAttribute('id')) {
            Comment::deleteRelatedAll(array(
                'model_id' => $this->id,
                'model_type' => get_class($this)
            ));
            File::deleteRelatedAll(array(
                'model_id' => $this->id,
                'model_type' => get_class($this)
            ));
        }
        return parent::beforeDelete();
    }

    public function updateHistory($comment)
    {
        $model = new Comment();
        $model->model_type = get_class($this);
        $model->model_id = $this->id;
        $model->comment = $comment;
        if (! $model->save()) {
            VarDumper::dump($model->errors);
            return false;
        }
        return true;
    }

    public function getControllerID()
    {
        $modelClass = get_class($this);
        $pos = strrpos($modelClass, '\\');
        $class = substr($modelClass, $pos + 1);
        return Inflector::camel2id($class);
    }

    public function getUrl($action = 'view', $id = null)
    {
        $params = [
            $this->getControllerID() . '/' . $action
        ];
        if ($id != null) {
            if (is_array($id))
                array_merge($params, $id);
            else
                $params['id'] = $id;
        } else {
            $params['id'] = $this->id;
        }
        $params['title'] = (string) $this;
        
        return Yii::$app->getUrlManager()->createAbsoluteUrl($params, true);
    }

    public function linkify($title = null, $controller = null, $action = 'view')
    {
        if ($title == null)
            $title = (string) $this;
        return Html::a($title, $this->getUrl($action, $controller));
    }

    public function getErrorsString()
    {
        $out = '';
        if ($this->errors != null)
            foreach ($this->errors as $err) {
                $out .= implode(',', $err);
            }
        return $out;
    }

    public static function getHasOneRelations()
    {
        $relations = [];
        return $relations;
    }

    public function getRelatedDataLink($key)
    {
        $hasOneRelations = $this->getHasOneRelations();
        if (isset($hasOneRelations[$key])) {
            $relation = $hasOneRelations[$key][0];
            if (isset($this->$relation))
                return $this->$relation->linkify();
        }
        return $this->$key;
    }

    public static function deleteRelatedAll($query = [])
    {
        $models = self::find()->where($query)->all();
        
        foreach ($models as $model) {
            // print_r($model->delete());die();
            $model->delete();
        }
    }

    public static function my($attribute = 'created_by_id')
    {
        return Yii::createObject(ActiveQuery::className(), [
            get_called_class()
        ])->andWhere([
            $attribute => \Yii::$app->user->id
        ]);
    }

    public function isActive()
    {
        return ($this->state_id == User::STATE_ACTIVE);
    }
    public static function truncate()
    {
        $table = self::tableName();
        
        \Yii::$app->db->createCommand()
            ->checkIntegrity(false)
            ->execute();
        
        echo "Cleaning " . $table . PHP_EOL;
        \Yii::$app->db->createCommand()
            ->truncateTable($table)
            ->execute();
        
        \Yii::$app->db->createCommand()
            ->checkIntegrity(true)
            ->execute();
    }
}
