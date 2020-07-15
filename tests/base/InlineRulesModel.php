<?php

namespace vjik\rulesValidatorTests\base;

use vjik\rulesValidatorTests\base\InlineRulesValidator;
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
