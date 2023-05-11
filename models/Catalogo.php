<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\JSON;
use yii\base\Exception;
use app\components\StringUtils;

/**
 * This is the model class for table "catalogos".
 *
 * @property int $cat_id
 * @property string $cat_codigo Codigo identificador del catalogo
 * @property string $cat_codigo_padre Codigo que relaciona catalogos entre si (tabla recursiva)
 * @property string $cat_estado
 * @property string $cat_nombre Nombre de catalogo para uso interno de la aplicacion. Ej: catalogo permisos de usuario
 * @property string $cat_nombre_auxiliar
 * @property string $cat_auxiliar_1 Auxiliar 1, permite almacenar algun dato adicional que dependera del uso del catalogo
 */
class Catalogo extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'catalogos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cat_codigo', 'cat_codigo_padre', 'cat_estado', 'cat_nombre'], 'required'],
            [['cat_codigo', 'cat_codigo_padre'], 'string', 'max' => 10],
            [['cat_estado'], 'string', 'max' => 1],
            [['cat_nombre', 'cat_nombre_auxiliar'], 'string', 'max' => 200],
            [['cat_auxiliar_1'], 'string', 'max' => 100],
            [['cat_codigo'], 'unique'],
        ];
    }

    /**
     *
     * @return int|string current client ID
     */
    public function getId()
    {
        return $this->cat_id;
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
}