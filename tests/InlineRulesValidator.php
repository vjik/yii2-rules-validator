<?php

namespace vjik\rulesValidatorTests;

use vjik\rulesValidator\RulesValidator;

class InlineRulesValidator extends RulesValidator
{

    protected function rules(): array
    {
        return [
            ['trim'],
            ['validateCountry', 'params' => ['y' => 12]]
        ];
    }

    public function validateCountry($model, $attribute, $params, $validator)
    {
        if (!in_array($model->$attribute, ['Russia', 'USA'])) {
            $model->addError($attribute, 'The country must be either "Russia" or "USA".');
        }
    }
}
