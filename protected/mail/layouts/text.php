<?php
/**
 *@copyright : Amusoftech  < www.amusoftech.com >
 *@author	 :Ram Mohamad Singh <  er.amudeep@gmail.com >
 */
use yii\helpers\Html;


/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */
?>
<?php $this->beginPage()?>
<?php $this->beginBody()?>
<?= $content?>
<?php $this->endBody()?>
<?php $this->endPage()?>
