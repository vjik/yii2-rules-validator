<?php

namespace vjik\rulesValidatorTests\skip;

use vjik\rulesValidator\RulesValidator;
use yii\base\Model;

class SkipErrorModel extends Model
{

    public $attr;

    public function rules()
    {
        return [
            [
                'attr',
                RulesValidator::class,
                'rules' => [
                    ['number', 'max' => 2],
                    [
                        function ($attribute) {
                            $this->$attribute = 1;
                        },
                        'skipOnError' => true,
                    ],
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
