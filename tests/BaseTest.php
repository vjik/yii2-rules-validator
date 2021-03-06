<?php

namespace vjik\rulesValidatorTests;

use PHPUnit\Framework\TestCase;
use vjik\rulesValidator\RulesValidator;
use vjik\rulesValidatorTests\base\BaseModel;
use vjik\rulesValidatorTests\base\InlineClosureModel;
use vjik\rulesValidatorTests\base\InlineModel;
use vjik\rulesValidatorTests\base\InlineRulesModel;

class BaseTest extends TestCase
{

    public function testValidValue()
    {
        $model = new BaseModel(['attr' => ' 45699b']);
        $this->assertEquals($model->validate(), true);
        $this->assertIsInt($model->attr);
        $this->assertEquals($model->attr, 45699);
    }

    public function testInvalidValue()
    {
        $model = new BaseModel(['attr' => ' 456b']);
        $this->assertEquals($model->validate(), false);
        $this->assertEquals($model->getFirstError('attr'), 'Attr must be no less than 9999.');
        $this->assertIsInt($model->attr);
        $this->assertEquals($model->attr, 456);
    }

    public function testInlineModel()
    {
        $model = new InlineModel();

        $model->country = 'Australia';
        $this->assertEquals($model->validate(), false);

        $model->country = ' Russia ';
        $this->assertEquals($model->validate(), true);
    }

    public function testInlineRulesModel()
    {
        $model = new InlineRulesModel();

        $model->country = 'Australia';
        $this->assertEquals($model->validate(), false);
        $this->assertEquals($model->getFirstError('country'), 'The country must be either "Russia" or "USA".');

        $model->country = ' Russia ';
        $this->assertEquals($model->validate(), true);
    }

    public function testInlineClosureRulesModel()
    {
        $model = new InlineClosureModel();

        $model->country = 'Australia';
        $this->assertEquals($model->validate(), false);
        $this->assertEquals($model->getFirstError('country'), 'The country must be either "Russia" or "USA".');

        $model->country = 'Russia';
        $this->assertEquals($model->validate(), true);
        $this->assertEquals($model->country, 'TestCountry');
    }

    public function testValidateValue()
    {
        $validator = new RulesValidator([
            'rules' => [
                ['email']
            ],
        ]);
        $this->assertTrue($validator->validate('test@example.com'));

        $this->assertFalse($validator->validate('test', $error));
        $this->assertEquals($error, 'the input value is not a valid email address.');
    }
}
