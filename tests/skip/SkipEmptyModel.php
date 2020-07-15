<?php

namespace vjik\rulesValidatorTests\skip;

use vjik\rulesValidator\RulesValidator;
use yii\base\Model;

class SkipEmptyModel extends Model
{

    public $attr;

    public function rules()
    {
        return [
            [
                'attr',
                RulesValidator::class,
                'rules' => [
                    [
                        function ($attribute) {
                            $this->$attribute = 1;
                        },
                        'skipOnEmpty' => true,
                    ],
                ],
            ],
        ];
    }
}
