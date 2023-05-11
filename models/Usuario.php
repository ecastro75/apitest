<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\JSON;
use yii\base\Exception;
use app\components\DuiValidator;
use app\components\NitValidator;
use app\components\PasswordStrengthValidator;
use app\components\StringUtils;


/**
 * This is the model class for table "usuarios".
 *
 * @property int $usu_id
 * @property string $usu_codigo El codigo representa el nombre de usuario
 * @property string $usu_clave
 * @property string $usu_estado
 * @property string $usu_nombre_1
 * @property string $usu_apellido_1
 * @property string $usu_conteo_fallo_login
 * @property string $usu_auth_key Yii2 utiliza la columna para su sistema de autenticacion
 * @property string $usu_auth_key_time Yii2 utiliza la columna para determinar el tiempo de inactividad del token
 * @property string $usu_fecha_cambio_clave Formato de fecha YYYYMMDDHHiiss
 * @property string $usu_fecha_ultimo_ingreso Formato de fecha YYYYMMDDHHiiss
 * @property string $usu_fecha_creacion Formato de fecha YYYYMMDDHHiiss
 * @property string $usu_fecha_actualizacion Formato de fecha YYYYMMDDHHiiss
 *
 * @property int $cat_rol_id Representacion del ROL para el usuarios (Ej.: SYSTEM, ADMIN), este se obtiene de tabla catalogos
 *
 * @property Catalogo $catalogoRol
 */
class Usuario extends ActiveRecord implements IdentityInterface
{
    const STATUS_INACTIVE = "0";
    const STATUS_ACTIVE = "1";

    // for search
    public $cli_id;

    public $usu_codigo_confirmar;
    public $usu_codigo_actual;
    public $usu_clave_confirmar;
    public $usu_clave_actual;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cat_rol_id', 'usu_nombre_1', 'usu_apellido_1'], 'required', 'message' => 'Campo requerido', 'except' => ['search']],
            [['usu_nombre_1', 'usu_apellido_1'], 'string', 'max' => 20, 'tooLong' => 'No debe exceder los 20 caracteres'],
            [['usu_conteo_fallo_login'], 'string', 'max' => 1],
            [['usu_fecha_cambio_clave', 'usu_fecha_ultimo_ingreso', 'usu_fecha_creacion', 'usu_fecha_actualizacion'], 'string', 'max' => 14, 'tooLong' => 'Fecha incorrecta, formato: YYYYMMDDHHiiss'],
            
            ['usu_clave', PasswordStrengthValidator::className(), 'message' => Html::encode('La clave ingresada es muy débil')],
            [['usu_clave', 'usu_clave_confirmar'], 'string', 'max' => 200, 'tooLong' => 'No debe exceder los 200 caracteres'],
            [['usu_clave'], 'compare', 'compareAttribute' => 'usu_clave_confirmar', 'message' => 'La clave a confirmar no coincide', 'except' => ['default']],
            [['usu_clave_confirmar'], 'compare', 'compareAttribute' => 'usu_clave', 'message' => 'La clave a confirmar no coincide'],

            ['usu_codigo', 'string', 'max' => 20, 'tooLong' => 'No debe exceder los 20 caracteres'],
            ['usu_codigo', 'unique', 'message' => 'El codigo ya ha sido utilizado'],
            [['usu_codigo', 'usu_codigo_confirmar', 'usu_clave', 'usu_clave_confirmar'], 'required', 'message' => 'Campo requerido', 'except' => ['search', 'update']],
            [['usu_codigo_confirmar'], 'compare', 'compareAttribute' => 'usu_codigo', 'message' => 'El codigo a confirmar no coincide', 'except' => ['search', 'update']],

            ['usu_estado', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE], 'message' => 'Valor no permitido'],
            ['usu_estado', 'string', 'max' => 2, 'tooLong' => 'No debe exceder los 2 carateres'],
            ['usu_estado', 'default', 'value' => self::STATUS_ACTIVE, 'except' => ['update', 'search']],
            ['usu_estado', 'required', 'message' => 'Campo requerido', 'except' => ['search'], 'on' => ['update']],

            [['usu_codigo', 'usu_nombre_1', 'usu_apellido_1', 'usu_estado', 'cli_id'], 'safe', 'on' => ['search']],

            [['cat_rol_id'], 'exist', 'skipOnError' => true, 'targetClass' => Catalogo::className(), 'targetAttribute' => ['cat_rol_id' => 'cat_id'], 'message' => 'El rol a asociar no existe'],
            [['usu_codigo'], 'exist', 'skipOnError' => true, 'targetClass' => Persona::className(), 'targetAttribute' => ['usu_codigo' => 'per_codigo'], 'message' => 'La persona a asociar no existe'],
        ];
    }

    /**
     *
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->usu_id;
    }

    /**
     *
     * @return string current user name "usu_codigo"
     */
    public function getUserName()
    {
        return $this->usu_codigo;
    }

    /**
     *
     * @return string a concatenation of "usu_nombre_1" and "usu_apellido_1"
     */
    public function getUserShortName()
    {
        return $this->usu_nombre_1 .' '. $this->usu_apellido_1;
    }

    /**
     * 
     * @return string current last user login: format d/m/Y H:i:s
     */
    public function getUserLastLogin()
    {
        $dateDD_MM_YYYY_HH_ii_ss = date('d/m/Y H:i:s', strtotime($this->usu_fecha_ultimo_ingreso));
        return $dateDD_MM_YYYY_HH_ii_ss;
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $u = static::findOne([
            'usu_auth_key' => $token, 'usu_estado' => self::STATUS_ACTIVE
        ]);

        // control auth key time
        if ($u != null && StringUtils::isNotBlank($u->usu_auth_key_time)) {
            $minutes = abs(strtotime($u->usu_auth_key_time) - time()) / 60;
            $u->scenario = "auth";

            // if the usu_auth_key_time is less from session timeout, save a new time
            // if not, clean auth_key && auth_key_time and send "null" data access
            if ($minutes < Yii::$app->params['authKeyTimeLife']) {
                $u->usu_auth_key_time = date("YmdHis");
                $u->save(false);   
            } else {
                Yii::warning($u->usu_codigo ." ... auth_key_time expirado", __METHOD__);
                $u->usu_auth_key = null;
                $u->usu_auth_key_time = null;
                $u->save(false);   
                return null;
            }
        }

        return $u;
    }

    /**
     *
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->usu_auth_key;
    }

    /**
     *
     * @param string $authKey
     * @return bool if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->usu_auth_key_time = date("YmdHis");
        $this->usu_auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->usu_clave);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function generatePassword($password)
    {
        $this->usu_clave = Yii::$app->security->generatePasswordHash($password);
    }

     /**
     * search method for provide DetailView|ListView|GridView
     * @param  array $params
     * @return ActiveDataProvider result of search
     */
    public function search($params) {
        $jsondata = JSON::decode($params);
        $this->attributes = $jsondata["usuario"];

        $query = Usuario::find()->select([
            'usu_id',
            'usu_codigo', 
            'usu_nombre_1',  
            'usu_apellido_1',  
            'usu_fecha_creacion', 
            'usu_fecha_actualizacion', 
            'usu_estado']);

        // adjust the query by adding the filters
        $query->innerJoin('personas', 'personas.per_codigo = usuarios.usu_codigo')
            ->orFilterWhere(['ilike', 'usuarios.usu_codigo', $this->usu_codigo])
            ->orFilterWhere(['ilike', 'usuarios.usu_nombre_1', $this->usu_nombre_1])
            ->orFilterWhere(['ilike', 'usuarios.usu_apellido_1', $this->usu_apellido_1])
            ->orFilterWhere(['=', 'usuarios.usu_estado', $this->usu_estado])
            ->orFilterWhere(['=', 'personas.cli_id', $this->cli_id]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['usu_fecha_creacion' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 8,
            ],
        ]);

        // load the search form data and validate
        if (!($this->validate())) {
            return $dataProvider;
        }

        if (isset($jsondata["page"]) && StringUtils::isNotBlank($jsondata["page"])) {
        	$dataProvider->pagination->page = $jsondata["page"];
			$dataProvider->refresh();
		}

        return $dataProvider;
    }

    /**
     * [beforeValidate description]
     * @return [type] [description]
     */
    public function beforeValidate()
    {
    	// the code/user can't be updated
		if (StringUtils::isEquals("update", $this->scenario) 
			&& !StringUtils::isEquals($this->usu_codigo, $this->usu_codigo_actual)) {
		    throw new Exception("Error: el codigo/usuario no debe ser modificado");
		}

    	return parent::beforeValidate();
    }

    /**
     * {@inheritdoc}
     * @param  boolean $insert TRUE for insert, FALSE for update
     * @return booelan indicates if the insertion or updating should continue
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // control password save
        // the password change only if "usu_clave" && "usu_clave_confirmar" is not blank
        if (!StringUtils::isEquals("login", $this->scenario) 
            && !StringUtils::isEquals("auth", $this->scenario))
        {
            if (StringUtils::isNotBlank($this->usu_clave)  && StringUtils::isNotBlank($this->usu_clave_confirmar)){
                $this->generatePassword($this->usu_clave);
                $this->usu_fecha_cambio_clave = date("YmdHis");
            }  else {
            	$this->usu_clave = $this->usu_clave_actual;
            }
        }
        
        // for create record add "usu_fecha_creacion" && "usu_fecha_actualizacion"
        // for update record change "usu_fecha_actualizacion"
        if ($insert) {
            $this->usu_fecha_creacion = date("YmdHis");
            $this->usu_fecha_actualizacion = date("YmdHis");
        } else if (StringUtils::isEquals("update", $this->scenario)) {
            $this->usu_fecha_actualizacion = date("YmdHis");
        }

        return true;
    }


    /* 
     * RELATIONS */

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatalogoRol()
    {
        return $this->hasOne(Catalogo::className(), ['cat_rol_id' => 'cat_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersona()
    {
        return $this->hasOne(Persona::className(), ['usu_codigo' => 'per_codigo']);
    }
}