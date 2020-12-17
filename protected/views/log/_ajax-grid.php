<?php

use yii\helpers\Html;
use app\components\TGridView;
use yii\grid\GridView;
use yii\widgets\Pjax;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\Log $searchModel
 */

?>
<?php Pjax::begin(["enablePushState"=>false,"enableReplaceState"=>false,"id"=>'Log-pjax-ajax-grid']); ?>
    <?php echo TGridView::widget([
    	'id' => 'log-ajax-grid-view',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions'=>['class'=>'table table-bordered'],
        'columns' => [
           // ['class' => 'yii\grid\SerialColumn','header'=>'<a>S.No.<a/>'],

            'id',
            'error',
            /* 'api:html',*/
            /* 'description:html',*/
            [
			'attribute' => 'state_id','format'=>'raw','filter'=>isset($searchModel)?$searchModel->getStateOptions():null,
			'value' => function ($data) { return $data->getStateBadge();  },],
            'link',
            ['attribute' => 'type_id','filter'=>isset($searchModel)?$searchModel->getTypeOptions():null,
			'value' => function ($data) { return $data->getType();  },],
            'created_on:datetime',

            ['class' => 'app\components\TActionColumn','header'=>'<a>Actions</a>'],
        ],
    ]); ?>
<?php Pjax::end(); ?>

