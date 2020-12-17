<?php

/**
 *@copyright : Amusoftech  < www.amusoftech.com >
 *@author	 :Ram Mohamad Singh <  er.amudeep@gmail.com >
 */
namespace app\components;

use Yii;

class TBaseWidget extends \yii\base\Widget {
	public $route;
	public $params;
	public function run() {
		if ($this->route === null && Yii::$app->controller !== null) {
			$this->route = Yii::$app->controller->getRoute ();
		}
		if ($this->params === null) {
			$this->params = Yii::$app->request->getQueryParams ();
		}
		$this->renderHtml ();
	}
	public function renderHtml() {
	}
}