<?php


namespace app\models;


use yii\base\Model;

class XmlFiller extends Model
{
    public $A = null;
    public $B = null;
    public $C = null;
    public $D = null;
    public $E = null;
    public $F = null;
    public $G = null;
    public $H = null;
    public $I = null;
    public $J = null;
    public $K = null;
    public $L = null;
    public $M = null;
    public $N = null;
    public $O = null;
    public $P = null;
    public $Q = null;
    public $R = null;
    public $S = null;
    public $T = null;
    public $U = null;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['A', 'B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q', 'R', 'S','T','U',], 'safe'],
        ];
    }
}