<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\Exception;
use yii\helpers\Html;
use app\models\Usuario;
use app\components\StringUtils;

class Seguridad extends Model
{
    const STATUS_INACTIVE = "0";
    const STATUS_ACTIVE = "1";

    public $usuario;
    public $clave;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['usuario', 'clave'], 'string', 'max' => 200],
        ];
    }

    /**
     * [login description]
     * @return [type] [description]
     */
    public function login()
    {
    	$usuario = Usuario::find()->where(['usu_codigo' => $this->usuario])->one();
    	$message = "";

    	// control if the user exist on BD
        if ($usuario == null) {
			$message = "Usuario no encontrado22";
        	throw new Exception($message, "01");
		} 
		// control if the user have STATUS different of ACTIVE
        else if (!StringUtils::isEquals(self::STATUS_ACTIVE, $usuario->usu_estado)) {
            $message = "Usuario inactivo";
            throw new Exception($message, "02");
        } 
        // control if the user exceed login try
        else if (StringUtils::isEquals("3", $usuario->usu_conteo_fallo_login)) {
            $message = "Usuario bloqueado por intentos fallidos";
            throw new Exception($message, "03");
        } 
        // control login count failed
        else if (!$usuario->validatePassword($this->clave)) {
            $usuario->scenario = "login";
            $usuario->usu_conteo_fallo_login = (int) $usuario->usu_conteo_fallo_login + 1;
            $usuario->save(false);

            $message = "Usuario o Clave incorrecta";
           	if ($usuario->usu_conteo_fallo_login == "3") {
             	$message = "Usuario bloqueado por intentos fallidos";
           		throw new Exception($message, "03");
            }

           	throw new Exception($message, "04");
        }
        // control password expiration
		else if (StringUtils::getDateDifference(date('Ymd', strtotime($usuario->usu_fecha_cambio_clave)), date("Ymd")) > 30) {
			$message = "Clave expirada";
			throw new Exception($message, "05");
        }

        // if don't have any error proceed with authentication
        $_accessToken = $usuario->generateAuthKey();
        //if (Yii::$app->user->loginByAccessToken($_accessToken)) {
            $usuario->scenario = "login";
            $usuario->usu_fecha_ultimo_ingreso = date("YmdHis");
            $usuario->usu_clave_actual = $usuario->usu_clave;
            $usuario->usu_conteo_fallo_login = "0";
            $usuario->save(false);
            return $usuario;
       // }

        return null;
    }

    /**
     * [login description]
     * @return [type] [description]
     */
    public function logout()
    {
        $usuario = Usuario::findIdentity(Yii::$app->user->getId());
        if ($usuario != null && Yii::$app->user->logout()) {
            $usuario->scenario = "login";
            $usuario->usu_auth_key = null;
            $usuario->usu_auth_key_time = null;
            $usuario->save(false);
            return true;
        }

        return false;
    }
}