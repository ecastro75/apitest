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
 * This is the model class for table "personas".
 *
 * @property int $resulid
 * @property int $testid
 * @property string $testname 
 * @property int $partnumber
 * @property string $serialno
 * @property string $datetime
 * @property int $duration
 * @property string $defaults
 * @property string $results
 *
 */
class Test extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'test';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partnumber', 'testid', 'testname'], 'required', 'message' => 'Campo requerido', 'except' => ['search']],
            [['serialno', 'datetime', 'duration'], 'string', 'max' => 20, 'tooLong' => 'No debe exceder los 20 caracteres'],
            [['defaults', 'results'], 'string', 'max' => 2000, 'tooLong' => 'No debe exceder los 2000 caracteres']
        ];
    }

    /**
     *
     * @return int|string current person ID
     */
    public function getId()
    {
        return $this->resulid;
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return Model|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

     /**
     * search method for provide DetailView|ListView|GridView
     * @param  array $params
     * @return ActiveDataProvider result of search
     */
    public function search($params) {
        $jsondata = JSON::decode($params);
        $this->attributes = $jsondata["test"];

        $query = Persona::find()->select([
            'resulid',
            'testid', 
            'testname', 
            'partnumber', 
            'serialno', 
            'datetime', 
            'duration']);

        // adjust the query by adding the filters
        $query->orFilterWhere(['ilike', 'testid', $this->testid])
              ->orFilterWhere(['ilike', 'testname', $this->testname])
              ->orFilterWhere(['ilike', 'partnumber', $this->partnumber])
              ->orFilterWhere(['=', 'serialno', $this->serialno]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['resulid' => SORT_DESC]],
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
    	// the email can't be updated
		if (StringUtils::isEquals("update", $this->scenario) 
			&& !StringUtils::isEquals($this->resulid, $this->resulid_actual)) {
		    throw new Exception("Error: el id no debe ser modificado");
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
        
        // for create record add "per_fecha_creacion" && "per_fecha_actualizacion"
        // for update record change "per_fecha_actualizacion"
        /*if ($insert) {
            $this->per_fecha_creacion = date("YmdHis");
            $this->per_fecha_actualizacion = date("YmdHis");
        } else if (StringUtils::isEquals("update", $this->scenario)) {
            $this->per_fecha_actualizacion = date("YmdHis");
        }*/

        return true;
    }


}