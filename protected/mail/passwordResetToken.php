<?php
/**
 *@copyright : Amusoftech  < www.amusoftech.com >
 *@author	 :Ram Mohamad Singh <  er.amudeep@gmail.com >
 */
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = $user->getResetUrl ();
?>
<?= $this->render ( 'header.php' );?>
<tr>
	<td align="left"
		style="font-family: Lato, sans-serif; padding-top: 30px; padding-bottom: 0; color: #333333;"><h3
			style="margin: 0; font-weight: 500; font-size: 19px;">
			<p>Hello <?php echo  Html::encode($user->full_name) ?>,</p>
		</h3></td>
</tr>
<tr>
	<td align="left">
		<p> <?= \Yii::t('app', 'Follow the link below to reset your password') ?> </p>
		<p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
	</td>
</tr>
<?= $this->render ( 'footer.php' );?>