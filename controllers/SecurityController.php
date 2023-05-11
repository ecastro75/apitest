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
use app\models\Seguridad;
use app\components\StringUtils;

class SecurityController extends Controller
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
        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
        ];
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'only' => ['logout'],
            'except' => ['options'],
        ];
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        //unset($behaviors['rateLimiter']); // remove rateLimiter which requires an authenticated user to work
        return $behaviors;
    }

    /**
     * [actionLogin description]
     * @return [type] [description]
     */
    public function actionLogin()
    {
        $request = Yii::$app->getRequest();
        $jsondata = JSON::decode($request->getRawBody());
        $errorCode = "";
        $errorMessage = "";
        $errorTraceCode = "";

        try {
            $model = new Seguridad();
            $model->attributes = $jsondata;

            /*/return [
                'success' => true,
                'pass' => Yii::$app->security->generatePasswordHash($model->clave)   
            ];*/

            if ($model->validate())
            {
                // return "success" true if LOGIN is successfull or the user has a current SESSION
                $u = $model->login();
                if ($u !== null) {
                    return [
                        'success' => true,
                        'message' => self::MESSAGE_SUCCESS,
                        'auth' => [
                            'token' => $u->authKey,
                            'owner_name' => $u->userShortName,
                            'last_login' => $u->userLastLogin,
                        ]
                    ];
                } else {
                    return ['success' => false, 'message' => self::MESSAGE_SUCCESS];
                }
            } else {
                Yii::warning("Error en validacion...", __METHOD__);
                return ['success' => false, 'message' => self::MESSAGE_ERROR, 'errors' => $model->getErrors()];
            }
        } catch(\Exception $e) {
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();
            $errorTraceCode = StringUtils::generateRandomString(8);
            Yii::error($errorMessage ." :: TRACE CODE: ". $errorTraceCode , __METHOD__);
        } catch(\Throwable $e) {
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();
            $errorTraceCode = StringUtils::generateRandomString(8);
            Yii::error($errorMessage ." :: TRACE CODE: ". $errorTraceCode , __METHOD__);
        }

        return [
            'success' => false,
            'message' => self::MESSAGE_ERROR,
            'errorCode' => $errorCode,
            'errorMessage' => $errorMessage,
            'errorTraceCode' => $errorTraceCode,
        ];
    }

    /**
     * [actionLogout description]
     * @return [type] [description]
     */
    public function actionLogout()
    {
        $model = new Seguridad();
        if ($model->logout()) return ['success' => true, 'message' => self::MESSAGE_SUCCESS];
        else return ['success' => false, 'message' => self::MESSAGE_ERROR];
    }

}