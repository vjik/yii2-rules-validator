# Yii2 validator with nested rules

## Installation

The preferred way to install this extension is through [composer](https://getcomposer.org/download/):

```
composer require vjik/yii2-rules-validator
```

## Examples

### Use in model

```php
class MyModel extends Model
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
                    ['string', 'max' => 191],
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
```

### Rule Inheritance 

Rule class:

```php
class MyRulesValidator extends RulesValidator
{

    protected function rules(): array
    {
        return [
            ['trim'],
            ['string', 'max' => 191],
            ['validateCountry'],
        ];
    }

    public function validateCountry($model, $attribute, $params, $validator)
    {
        if (!in_array($model->$attribute, ['Russia', 'USA'])) {
            $model->addError($attribute, 'The country must be either "Russia" or "USA".');
        }
    }
}
```

Model:

```php
class MyModel extends Model
{

    public $country;

    public function rules()
    {
        return [
            ['country', MyRulesValidator::class],
        ];
    }
}
```