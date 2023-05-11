<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\rest\ActiveController;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\web\Response;
use yii\base\Exception;
use yii\helpers\JSON;
use app\models\Catalogo;
use app\components\StringUtils;

class CatalogController extends Controller
{
    const MESSAGE_SUCCESS = "Procesamiento Correcto";
    const MESSAGE_ERROR = "Fallo al procesar la solicitud";

	/**
	 * [behaviors description]
	 * @return [type] [description]
	 */
	public function behaviors()
    {
    	$behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        return [
        	'corsFilter' => [
        		'class' => Cors::className(),
        	],
			'authenticator' => [
				'class' => HttpBearerAuth::className(),
            	'except' => ['options'],
			],
			'contentNegotiator' => [
				'class' => ContentNegotiator::className(),
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
				],
			],
		];
    }

	/**
	 * [actionView description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function actionIndex()
	{
		$models = Catalogo::find()
			->select([
				'cat_codigo', 
				'cat_codigo_padre', 
				'cat_nombre',
				'cat_estado'])
			->all();

	    return [
	    	'success' => true,
	    	'message' => self::MESSAGE_SUCCESS,
	    	'items' => $models,
	    ];
	}

	/**
	 * [actionView description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function actionView($id)
	{
		$model = Catalogo::find()
	    	->select([
	    		'cat_id',
	    		'cat_codigo',
	    		'cat_codigo_padre', 
	    		'cat_nombre'])
	    	->where(['cat_id' => $id])
	    	->one();

	    return [
	    	'success' => true,
	    	'message' => self::MESSAGE_SUCCESS,
	    	'catalogo' => $model,
	    ];
	}

	/**
	 * [actionList description]
	 * @param  [type] $cat_codigo_padre [description]
	 * @return [type] [description]
	 */
	public function actionList($cat_codigo_padre)
	{
		$models = Catalogo::find()
			->select([
				'cat_id',
				'cat_codigo', 
				'cat_nombre',
				'cat_nombre_auxiliar',
				'cat_auxiliar_1'])
			->where(['cat_codigo_padre' => $cat_codigo_padre])
			->all();

	    return [
	    	'success' => true,
	    	'message' => self::MESSAGE_SUCCESS,
	    	'items' => $models,
	    ];
	}
}