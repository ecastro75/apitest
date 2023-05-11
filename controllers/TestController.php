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
use app\models\Test;
use app\components\StringUtils;

class TestController extends Controller
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

		$models = Test::find()
			->select([
				'resulid', 
				'testname', 
				'partnumber', 
				'serialno', 
				'duration',
				'datetime'])
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
		$model = Test::find()
	    	->select([
				'resulid', 
				'testname', 
				'partnumber', 
				'serialno', 
				'duration'])
	    	->where(['resulid' => $id])
	    	->one();

	    return [
	    	'success' => true,
	    	'message' => self::MESSAGE_SUCCESS,
	    	'test' => $model,
	    ];
	}

	/**
	 * [actionView description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function actionSearch()
	{
		$request = Yii::$app->getRequest();
		$model = new Test(array('scenario' => 'search'));

		if ($request->isPost)
        {
        	$dataProvider = $model->search($request->getRawBody());
	    	return [
	    		'success' => true, 
	    		'message' => self::MESSAGE_SUCCESS, 
	    		'page' => $dataProvider->getPagination()->getPage(),
	    		'pageSize' => $dataProvider->getPagination()->getPageSize(),
	    		'itemsCount' => $dataProvider->getCount(),
	    		'itemsCountTotal' => $dataProvider->getTotalCount(),
	    		'items' => $dataProvider->getModels(),
	    	];
	    }
	}

	/**
	 * [actionCreate description]
	 * @return [type] [description]
	 */
	public function actionCreate()
	{
		$request = Yii::$app->getRequest();

        $jsondata = JSON::decode($request->getRawBody());
        $errorMessage = "";
        $errorTraceCode = "";
		if ($request->isPost)
        {
			$model = new Test();
        	$model->attributes = $jsondata["test"];
        	//$model->attributes = $jsondata;
        	$transaction = Test::getDb()->beginTransaction();
        	try {

                if ($model->validate()) {
                    $model->save(false);
                    $transaction->commit();
        			return ['success' => true, 'message' => self::MESSAGE_SUCCESS .'. El test se ha agregado a los registros.'];
                } else {
                	Yii::warning("Error en validacion...", __METHOD__);
                	return ['success' => false, 'message' => self::MESSAGE_ERROR, 'errors' => $model->getErrors()];
                }
        	} catch(\Exception $e) {
            	$transaction->rollBack();
                $errorMessage = $e->getMessage();
                $errorTraceCode = StringUtils::generateRandomString(8);
                Yii::error($errorMessage ." :: TRACE CODE: ". $errorTraceCode , __METHOD__);
            } catch(\Throwable $e) {
                $transaction->rollBack();
                $errorMessage = $e->getMessage();
                $errorTraceCode = StringUtils::generateRandomString(8);
                Yii::error($errorMessage ." :: TRACE CODE: ". $errorTraceCode , __METHOD__);
            }
        }

        return [
            'success' => false,
            'message' => self::MESSAGE_ERROR,
            'errorMessage' => $errorMessage,
            'errorTraceCode' => $errorTraceCode,
        ];
	}

	/**
	 * [actionCreate description]
	 * @return [type] [description]
	 */
	public function actionUpdate()
	{
		$request = Yii::$app->getRequest();
        $jsondata = JSON::decode($request->getRawBody());
		$errorMessage = "";
		$errorTraceCode = "";
		if ($request->isPut)
        {

        	// get the "Test" from BD
        	$model = Test::findIdentity($jsondata["test"]["resulid"]);

		
        	// if "Test" don't exist on BD, return fail status
        	if ($model == null) {
        		return [
                	'success' => false,
                	'message' => self::MESSAGE_ERROR . ". Test no Encontrado",
                ];
        	}
        	// "per_codigo_actual" allow to control validation for no code updating
        	//$model->resulid_actual = $model->resulid;

        	// set the new data
        	$model->attributes = $jsondata["test"];
	        //$model->scenario = $jsondata["scenario"];
	        	//return $model->attributes;
	        $transaction = Test::getDb()->beginTransaction();
	        try {
	            if ($model->validate(false)) {
	                $model->save(false);
	                $transaction->commit();
	                return ['success' => true, 'message' => self::MESSAGE_SUCCESS .'. Información de test actualizada.'];
	            } else {
                	Yii::warning("Error en validacion...", __METHOD__);
                	return ['success' => false, 'message' => self::MESSAGE_ERROR, 'errors' => $model->getErrors()];
                }
	        } catch(\Exception $e) {
				$transaction->rollBack();
	            $errorMessage = $e->getMessage();
	            $errorTraceCode = StringUtils::generateRandomString(8);
	            Yii::error($errorMessage ." :: TRACE CODE: ". $errorTraceCode , __METHOD__);
	        } catch(\Throwable $e) {
	            $transaction->rollBack();
	            $errorMessage = $e->getMessage();
	            $errorTraceCode = StringUtils::generateRandomString(8);
	            Yii::error($errorMessage ." :: TRACE CODE: ". $errorTraceCode , __METHOD__);
	        }
        }

        return [
            'success' => false,
            'message' => self::MESSAGE_ERROR,
            'errorMessage' => $errorMessage,
            'errorTraceCode' => $errorTraceCode,
        ];
	}

	public function actionDelete($id)
    {
    	//return $id;
        //$this->findModel($id)->delete();
        $resultado =  Test::find()
		  ->where(['resulid'=>$id])->one()->delete();
		  //->delete();
        //$resp = Test::find($id)->delete(); 
	    	//return $resultado;       
	    return ['success' => true, 'message' => self::MESSAGE_SUCCESS .'. Información de test eliminada.' .$resultado];
    }
}