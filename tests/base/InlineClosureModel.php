<?php

namespace vjik\rulesValidatorTests\base;

use vjik\rulesValidatorTests\base\InlineClosureRulesValidator;
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
