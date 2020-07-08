<?php

namespace vjik\rulesValidatorTests;

use yii\base\Model;

class InlineClosureModel extends Model
{

    public $country;

    public function rules()
    {
        return [
            ['country', InlineClosureRulesValidator::class],
        ];
    }
}
