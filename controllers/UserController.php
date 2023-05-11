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
use app\models\Usuario;
use app\components\StringUtils;

class UserController extends Controller
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
		$models = Usuario::find()
			->select([
				'usu_codigo', 
				'usu_nombre_1', 
				'usu_apellido_1', 
				'usu_fecha_creacion', 
				'usu_fecha_actualizacion', 
				'usu_estado'])
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
		$model = Usuario::find()
	    	->select([
	    		'usu_id',
	    		'usu_codigo', 
	    		'usu_nombre_1',  
	    		'usu_apellido_1',
	    		'cat_rol_id',  
	    		'usu_estado'])
	    	->where(['usu_id' => $id])
	    	->one();

	    return [
	    	'success' => true,
	    	'message' => self::MESSAGE_SUCCESS,
	    	'usuario' => $model,
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
		$model = new Usuario(array('scenario' => 'search'));

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
        $errorMessage = "ddd";
        $errorTraceCode = "";
        //return  $request->isPost;
        

		if ($request->isPost)
        {
			$model = new Usuario();
        	$model->attributes = $jsondata["usuario"];

        	$transaction = Usuario::getDb()->beginTransaction();
        	try {
                if ($model->validate()) {
                    $model->save(false);
                    $transaction->commit();
        			return ['success' => true, 'message' => self::MESSAGE_SUCCESS .'. El usuario se ha agregado a los registros.'];
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
        	// get the "Usuario" from BD
        	$model = Usuario::findIdentity($jsondata["usuario"]["usu_id"]);

        	// if "Usuario" don't exist on BD, return fail status
        	if ($model == null) {
        		return [
                	'success' => false,
                	'message' => self::MESSAGE_ERROR . ". Usuario no Encontrado",
                ];
        	}

        	// "usu_codigo_actual" allow to control validation for no code updating
        	// "usu_clave_actual" allow to control persist "usu_clave" if this don't have change
        	$model->usu_codigo_actual = $model->usu_codigo;
        	$model->usu_clave_actual = $model->usu_clave;
        	$model->usu_clave = "";

        	// set the new data
        	$model->attributes = $jsondata["usuario"];
	        $model->scenario = $jsondata["scenario"];
	        	
	        $transaction = Usuario::getDb()->beginTransaction();
	        try {
	            if ($model->validate()) {
	                $model->save(false);
	                $transaction->commit();
	                return ['success' => true, 'message' => self::MESSAGE_SUCCESS .'. Información de usuario actualizada.'];
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
}