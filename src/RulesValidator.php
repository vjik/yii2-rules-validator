<?php

namespace vjik\rulesValidator;

use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\validators\InlineValidator;
use yii\validators\Validator;

class RulesValidator extends Validator
{

    /**
     * @var array
     */
    public $rules = [];

    /**
     * @inheritDoc
     */
    public function __construct($config = [])
    {
        if (!empty($config) && isset($config['rules'])) {
            foreach ($config['rules'] as $k => $v) {
                $config['rules'][$k]['_isModelRule'] = true;
            }
        }
        parent::__construct($config);
    }

    /**
     * @inheritDoc
     */
    public function validateAttribute($model, $attribute)
    {
        $dynamicModel = new DynamicModel($model->getAttributes());
        foreach ($this->getValidators($model) as $validator) {
            $dynamicModel->addRule($attribute, $validator);
        }
        $dynamicModel->setAttributeLabels($model->attributeLabels());

        $dynamicModel->defineAttribute($attribute, $model->$attribute);
        $dynamicModel->validate();
        if ($dynamicModel->hasErrors($attribute)) {
            if ($this->message === null) {
                $model->addErrors([$attribute => $dynamicModel->getErrors($attribute)]);
            } else {
                $this->addError($model, $attribute, $this->message, ['value' => $model->$attribute]);
            }
        }

        $model->$attribute = $dynamicModel->$attribute;
    }

    /**
     * {@inheritdoc}
     */
    protected function validateValue($value)
    {
        foreach ($this->getValidators(new Model()) as $validator) {
            if ($validator->skipOnEmpty && $validator->isEmpty($value)) {
                continue;
            }
            $result = $validator->validateValue($value);
            if ($result !== null) {
                if ($this->message === null) {
                    $result[1]['value'] = $value;
                    return $result;
                }
                return [$this->message, ['value' => $value]];
            }
        }
        return null;
    }

    private $_validators;

    /**
     * @param Model $model
     * @return Validator[]
     * @throws InvalidConfigException
     */
    protected function getValidators(Model $model): array
    {
        if ($this->_validators === null) {
            $this->_validators = [];
            foreach ($this->rules as $rule) {
                if ($rule instanceof Validator) {
                    $this->_validators[] = $rule;
                } elseif (is_array($rule) && isset($rule[0])) { // validator type
                    $this->_validators[] = $this->createEmbeddedValidator($rule[0], $model, array_slice($rule, 1));
                } else {
                    throw new InvalidConfigException('Invalid validation rule: a rule must specify validator type.');
                }
            }
        }
        return $this->_validators;
    }

    /**
     * @param mixed $type
     * @param Model $model
     * @param array $params
     * @return Validator
     */
    protected function createEmbeddedValidator($type, Model $model, array $params): Validator
    {
        $isModelRule = ArrayHelper::remove($params, '_isModelRule');

        if (is_string($type) &&
            !$isModelRule &&
            !isset(static::$builtInValidators[$type]) &&
            $this->hasMethod($type)
        ) {
            $params['class'] = InlineValidator::class;
            if (!isset($params['params']) || !is_array($params['params'])) {
                $params['params'] = [];
            }
            $params['params']['_ruleInstance'] = $this;
            $params['params']['_ruleMethod'] = $type;
            $params['params']['_ruleModel'] = $model;
            $type = function ($attribute, $params, $validator) {
                $callable = [ArrayHelper::remove($params, '_ruleInstance'), ArrayHelper::remove($params, '_ruleMethod')];
                call_user_func($callable, ArrayHelper::remove($params, '_ruleModel'), $attribute, $params, $validator);
            };
        }

        return Validator::createValidator($type, $model, $this->attributes, $params);
    }
}
