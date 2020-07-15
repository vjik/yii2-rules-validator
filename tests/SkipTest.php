<?php

namespace vjik\rulesValidatorTests;

use PHPUnit\Framework\TestCase;
use vjik\rulesValidatorTests\skip\SkipEmptyModel;
use vjik\rulesValidatorTests\skip\SkipErrorModel;
use vjik\rulesValidatorTests\skip\WhenBoolFalseModel;
use vjik\rulesValidatorTests\skip\WhenBoolTrueModel;
use vjik\rulesValidatorTests\skip\WhenCallableModel;

class SkipTest extends TestCase
{

    public function testBoolTrue()
    {
        $model = new WhenBoolTrueModel(['attr' => ' x']);
        $model->validate();
        $this->assertEquals('x', $model->attr);
    }

    public function testBoolFalse()
    {
        $model = new WhenBoolFalseModel(['attr' => ' x']);
        $model->validate();
        $this->assertEquals(' x', $model->attr);
    }

    public function testCallable()
    {
        $model = new WhenCallableModel(['attr' => ' x']);
        $model->validate();
        $this->assertEquals(' x', $model->attr);

        $model = new WhenCallableModel(['attr' => 'x ']);
        $model->validate();
        $this->assertEquals('x', $model->attr);
    }

    public function testSkipOnError()
    {
        $model = new SkipErrorModel(['attr' => 3]);
        $model->validate();
        $this->assertEquals(3, $model->attr);

        $model = new SkipErrorModel(['attr' => 0]);
        $model->validate();
        $this->assertEquals(1, $model->attr);
    }

    public function testSkipOnEmpty()
    {
        $model = new SkipEmptyModel();
        $model->validate();
        $this->assertEquals(null, $model->attr);

        $model = new SkipErrorModel(['attr' => 0]);
        $model->validate();
        $this->assertEquals(1, $model->attr);
    }
}
