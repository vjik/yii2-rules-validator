<?php

namespace vjik\rulesValidatorTests;

use yii\base\Model;

class InlineRulesModel extends Model
{

    public $country;

    public function rules()
    {
        return [
            ['country', InlineRulesValidator::class],
        ];
    }
}
