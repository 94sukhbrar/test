<?php
use app\assets\AppAsset;
use app\models\User;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\FlashMessage;

/* @var $this \yii\web\View */
/* @var $content string */

$this->title = yii::$app->name;
AppAsset::register ( $this );
?>
<?php $this->beginPage()?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport"
	content="width=device-width,initial-scale=1,maximum-scale=1">
<meta charset="<?= Yii::$app->charset ?>" />
    <?= Html::csrfMetaTags()?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head()?>

<link href="<?php echo $this->theme->getUrl('css/style.css')?>" rel="stylesheet">
<link href="<?php echo $this->theme->getUrl('css/style-responsive.css')?>" rel="stylesheet">

<!--theme color layout-->
<link href="<?php echo $this->theme->getUrl('css/font-awesome.css')?>" rel="stylesheet">
</head>
<body class="sticky-header theme-<?= Yii::$app->view->theme->style ?>">
<?php $this->beginBody()?>

 <header class="nav-wrapper">
 <nav class="navbar navbar-inverse">
   <div class="container">
	<div class="navbar-header">
	 <button aria-expanded="false" aria-controls="bs-navbar" data-target="#bs-navbar" data-toggle="collapse" type="button" class="navbar-toggle collapsed">
	  <span class="sr-only">Toggle navigation</span>
	  <span class="icon-bar"></span>
	  <span class="icon-bar"></span>
	  <span class="icon-bar"></span>
	</button>
    <a class="navbar-brand" href="<?php echo Url::home();?>"> &nbsp;
     <span class="brand-name"><?= Yii::$app->name?> </span>
   </a>
  </div>
	<div class="collapse navbar-collapse" id="bs-navbar">
		<ul class=" nav navbar-nav navbar-right mega-menu">
			<!-- <li><a href="<?php //echo Url::to(['site/contact']);?>">Contact</a>
			</li>
			<li>-->

			<?php   if(User::isGuest()){?>
			<li><a href="<?php echo Url::to(['user/signup']);?>">Sign Up</a></li>
			<li><a href="<?php echo Url::to(['user/login']);?>">Login</a></li>

			<?php
			} else {
				?>
				<li><a href="<?php echo Url::to(['user/dashboard']);?>">Dashboard</a></li>
				<li><a href="<?php echo Url::to(['user/view','id'=>Yii::$app->user->identity->id]);?>">My Profile</a></li>
				<li><a href="<?php echo Url::to(['user/logout']);?>">Logout</a></li>


		<?php 	}?>
			</ul>


		</div>
	</div>
	</nav>
</header>

		<!-- body content start-->

		 <div class="main_wrapper well">
				 <?= FlashMessage::widget () ?>
                 <?= $content?>
          </div>
		
		<!--body wrapper end-->
	 <footer>
  <div class="text-center">
    <p>&copy; <?php echo date('Y')?>  <?= Yii::$app->name;?>  | All Rights Reserved	| Powered by <a target="_blank" href="<?= Yii::$app->params['companyUrl'];?>"><?= Yii::$app->params['company']?></a></p>
 </div>
</footer>


		<!-- body content end-->


	
<?php $this->endBody()?>


</body>


<?php $this->endPage()?>

</html>
