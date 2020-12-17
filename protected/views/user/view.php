<?php
/**
 *@copyright : Amusoftech  < www.amusoftech.com >
 *@author	 :Ram Mohamad Singh <  er.amudeep@gmail.com >
 */
use app\components\PageHeader;
use app\components\TDetailView;
use app\components\useraction\UserAction;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;
use app\modules\comment\widgets\CommentsWidget;
/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->params['breadcrumbs'][] = [
    'label' => 'Users',
    'url' => [
        'index'
    ]
];
$this->params['breadcrumbs'][] = [
    'label' => $model->full_name
];

?>
<div class="wrapper">

	<div class=" panel ">
		<?php echo  PageHeader::widget(['model'=>$model]); ?>
	</div>
	<div class="panel">
		<div class=" panel-body">
			<div class="col-md-2">
			<?php if(!empty($model->profile_file)) { ?>
				<?php
    echo Html::img([
        'image/thumbnail',
        'filename' => $model->profile_file
    ])?><br /> <br />
				<p>
        			<?= Html::a('Download image file', ['download','profile_file'=>$model->profile_file], ['class' => 'btn btn-success','name' => 'download-button'])?>
   			 	</p>
   			 	
   			 	<?php }?>
			</div>
			<div class="col-md-10">
			<?php
echo TDetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'full_name',
        'email:email',
        [
            'attribute' => 'role_id',
            'format' => 'raw',
            'value' => $model->getRoleOptions($model->role_id)
        ],
        
        'created_on:datetime'
    ]
])?>
			</div>
		</div>
		<div class="panel-body">
				<?php
    if ((User::isAdmin()) && (\Yii::$app->user->id != $model->id)) {
        $actions = $model->getStateOptions();
        array_shift($actions);
        echo UserAction::widget([
            'model' => $model,
            'attribute' => 'state_id',
            'states' => $model->getStateOptions(),
            'allowed' => $actions
        ]);
    }
    ?>
			</div>



	</div>
	
	<?php if( $model->role_id != User::ROLE_USER ) { ?>
		<div class=" panel ">
		<div class=" panel-body ">
				<?php
    $this->context->startPanel();
    
    $this->context->addPanel('Pages', 'pages', 'Page', $model);
    
    $this->context->endPanel();
    ?>
			</div>
	</div>
	<?php } ?>


</div>

