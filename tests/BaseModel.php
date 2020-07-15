<?php

namespace vjik\rulesValidatorTests;

use vjik\rulesValidator\RulesValidator;
use yii\base\Model;

class BaseModel extends Model
{

    public $attr;

    public function rules()
    {
        return [
            [
                'attr',
                RulesValidator::class,
                'rules' => [
                    ['filter', 'filter' => 'intval'],
                    ['number', 'min' => 9999],
                ],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'attr' => 'Attr',
        ];
    }
}
