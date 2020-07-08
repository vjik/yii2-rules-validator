<?php

namespace vjik\rulesValidator;

use Closure;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\validators\InlineValidator;
use yii\validators\Validator;

class RulesValidator extends Validator
{

    /**
     * @inheritDoc
     */
    public $skipOnEmpty = false;

    /**
     * @inheritDoc
     */
    public $enableClientValidation = false;

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

    protected $_rules = [];

    /**
     * @param array $rules
     */
    public function setRules(array $rules)
    {
        $this->_rules = $rules;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->_rules;
    }

    /**
     * @return array
     */
    protected function rules(): array
    {
        return $this->getRules();
    }

    /**
     * @inheritDoc
     */
    public function validateAttribute($model, $attribute)
    {
        foreach ($this->getValidators($model) as $validator) {
            $validator->validateAttribute($model, $attribute);
        }
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
            foreach ($this->rules() as $rule) {
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

        if (!$isModelRule &&
            (
                (is_string($type) && !isset(static::$builtInValidators[$type]) && $this->hasMethod($type)) ||
                $type instanceof Closure
            )
        ) {
            $params['class'] = InlineValidator::class;
            if (!isset($params['params']) || !is_array($params['params'])) {
                $params['params'] = [];
            }
            $params['params']['_ruleCallable'] = is_string($type) ? [$this, $type] : $type;
            $params['params']['_ruleModel'] = $model;
            $type = function ($attribute, $params, $validator) {
                call_user_func(ArrayHelper::remove($params, '_ruleCallable'), ArrayHelper::remove($params, '_ruleModel'), $attribute, $params, $validator);
            };
        }

        return Validator::createValidator($type, $model, $this->attributes, $params);
    }
}
