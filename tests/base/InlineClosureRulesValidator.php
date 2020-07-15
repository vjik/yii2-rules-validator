<?php

namespace vjik\rulesValidatorTests\base;

use vjik\rulesValidator\RulesValidator;

class InlineClosureRulesValidator extends RulesValidator
{

    protected function rules(): array
    {
        return [
            ['trim'],
            [
                function ($model, $attribute, $params, $validator) {
                    if (!in_array($model->$attribute, ['Russia', 'USA'])) {
                        $model->addError($attribute, 'The country must be either "Russia" or "USA".');
                    }
                    $model->$attribute = 'TestCountry';
                }
            ]
        ];
    }
}