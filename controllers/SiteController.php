<?php

namespace app\controllers;

use yii\web\Controller;
class SiteController extends Controller
{
	/**
	 * [actionIndex description]
	 * @return [type] [description]
	 */
    public function actionIndex()
    {
        return '<!DOCTYPE html>'.
        	'<html>'.
        	'<body>'.
        		'<div>SchoolPortal MicroService!</div>'.
        	'<body>'.
        	'</html>';
    }
}