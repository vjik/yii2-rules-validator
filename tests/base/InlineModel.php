<?php

namespace vjik\rulesValidatorTests\base;

use vjik\rulesValidator\RulesValidator;
use yii\base\Model;

class InlineModel extends Model
{

    public $country;

    public function rules()
    {
        return [
            [
                'country',
                RulesValidator::class,
                'rules' => [
                    ['trim'],
                    ['validateCountry'],
                ],
            ],
        ];
    }

    public function validateCountry($attribute, $params, $validator)
    {
        if (!in_array($this->$attribute, ['Russia', 'USA'])) {
            $this->addError($attribute, 'The country must be either "Russia" or "USA".');
        }
    }
}
