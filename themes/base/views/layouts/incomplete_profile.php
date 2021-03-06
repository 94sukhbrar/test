<?php
use app\assets\AppAsset;
use app\components\FlashMessage;
use app\models\User;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\Menu;

$user = Yii::$app->user->identity;

/* @var $this \yii\web\View */
/* @var $content string */
// $this->title = yii::$app->name;

AppAsset::register ( $this );
?>
<?php

$this->beginPage ()?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language?>">
<head>
<meta charset="<?=Yii::$app->charset?>" />
    <?=Html::csrfMetaTags ()?>
    <title><?=Html::encode ( $this->title )?></title>
    <?php
				
				$this->head ()?>
    <meta name="viewport"
	content="width=device-width, initial-scale=1.0, maximum-scale=1.0">


<!--common style-->
<!--common style-->

<link
	href="<?php echo $this->theme->getUrl ( 'css/style-admin.css' )?>"
	rel="stylesheet">
	
<link
	href="<?php echo $this->theme->getUrl ( 'css/style-responsive.css' )?>"
	rel="stylesheet">
<link
	href="<?php echo $this->theme->getUrl ( 'css/font-awesome.css' )?>"
	rel="stylesheet">
<link
	href="<?php echo $this->theme->getUrl ( 'css/layout-theme-blue.css' )?>"
	rel="stylesheet">

	<link
	href="<?php echo $this->theme->getUrl ( 'css/custom.css' )?>"
	rel="stylesheet">


	<link
	href="<?php echo $this->theme->getUrl ( 'css/loader.css' )?>"
	rel="stylesheet">
	<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
	<link
	href="<?php echo $this->theme->getUrl ( 'css/icons.css' )?>"
	rel="stylesheet">
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
</head>
<body class="sticky-header theme-<?= Yii::$app->view->theme->style ?> sidebar-collapsed">


<?php

$this->beginBody ();

if (! User::isGuest ()) :
	?>

    <section>
		<!-- sidebar left start-->
	 
		<!-- sidebar left end-->

		<!-- body content start-->
		<div class="body-content">

			<!-- header section start-->
			<div class="header-section light-color">
 
				<div class="notification-wrap">



					<!--right notification start-->
					<div class="right-notification">
						<ul class="notification-menu">
						 
 
 
							 
						
							<li>
								<a href="javascript:;"
								class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								

								<?= \Yii::$app->user->identity->displayImage(\Yii::$app->user->identity->profile_file, ['width' => '50', 'height' => '50', 'class' => '']); ?>
								
                                <?php echo Yii::$app->user->identity->full_name; ?>
                                <span class=" fa fa-angle-down"></span>
							</a>
								<ul class="dropdown-menu dropdown-usermenu purple pull-right">
									 
									 
									<li><a
										href="<?php
	
	echo Url::toRoute ( [ 
			'/user/logout' 
	] );
	?>"> <i class="fa fa-sign-out pull-right"></i> Log Out
									</a></li>
								</ul>
								
								
								</li>
								
								


						</ul>

					</div>
					<!--right notification end-->
				</div>


			</div>

			<!-- header section end-->

			<!-- page head start-->
			 <?=Breadcrumbs::widget ( [ 'links' => isset ( $this->params ['breadcrumbs'] ) ? $this->params ['breadcrumbs'] : [ ] ] )?>
			<!--body wrapper start-->
			<section class="main_wrapper">
				
				<?php
	if (yii::$app->hasModule ( 'shadow' )) {
		echo app\modules\shadow\components\ShadowWidget::widget ();
	}
	?>
				
			
			
			   <?= FlashMessage::widget (['type' => 'default', /*'position' => 'bottom-right'*/  ]) ?>
			   <div class="loader hide"></div>		   
               <?=$content; ?>



			</section>

			<footer>

				<div class="text-center">
					<p target="_blank">
					<?php
	
	echo ' &copy; ' . date ( 'Y' ) . ' ' . Yii::$app->name . ' | All Rights Reserved ';
	?></p>

				</div>


			</footer>
			<!--footer section start-->
			<!--footer section end-->
			<!--body wrapper end-->
		</div>


		<!-- body content end-->

	</section>


	<!--common scripts for all pages-->
	<script src="<?php
	
	echo $this->theme->getUrl ( 'js/scripts.js' )?>"></script>

	<script src="<?php
	
	echo $this->theme->getUrl ( 'js/custom-modal.js' )?>"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script src="<?= $this->theme->getUrl ( '/js/pos.js' )?>"></script> 


<script type="text/javascript">
$(document).ready(function(){
	$(".child-list").find('span').contents().unwrap();
});


</script>



<?php
endif;
$this->endBody ();
?>


</body>

<?php

$this->endPage ()?>

</html>

