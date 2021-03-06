<?php

namespace vjik\rulesValidatorTests\skip;

use vjik\rulesValidator\RulesValidator;
use yii\base\Model;

class WhenBoolFalseModel extends Model
{

    public $attr;

    public function rules()
    {
        return [
            [
                'attr',
                RulesValidator::class,
                'rules' => [
                    ['trim', 'when' => false],
                ],
            ],
        ];
    }
}
