<?php

namespace Youshido\Tests\Parser;

use Youshido\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Youshido\GraphQL\Parser\Location;

class VariableTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test if variable value equals expected value
     *
     * @dataProvider variableProvider
     */
    public function testGetValue($actual, $expected)
    {
        $var = new Variable('foo', 'bar', false, false, new Location(1, 1), true);
        $var->setValue($actual);
        $this->assertEquals($var->getValue(), $expected);
    }

    public function testGetNullValueException()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Value is not set for variable "foo"');
        $var = new Variable('foo', 'bar', false, false, new Location(1, 1), true);
        $var->getValue();
    }

    public function testGetValueReturnsDefaultValueIfNoValueSet()
    {
        $var = new Variable('foo', 'bar', false, false, new Location(1, 1), true);
        $var->setDefaultValue('default-value');

        $this->assertEquals(
            'default-value',
            $var->getValue()
        );
    }

    public function testGetValueReturnsSetValueEvenWithDefaultValue()
    {
        $var = new Variable('foo', 'bar', false, false, new Location(1, 1), true);
        $var->setValue('real-value');
        $var->setDefaultValue('default-value');

        $this->assertEquals(
            'real-value',
            $var->getValue()
        );
    }

    public function testIndicatesDefaultValuePresent()
    {
        $var = new Variable('foo', 'bar', false, false, new Location(1, 1), true);
        $var->setDefaultValue('default-value');

        $this->assertTrue(
            $var->hasDefaultValue()
        );
    }

    public function testHasNoDefaultValue()
    {
        $var = new Variable('foo', 'bar', false, false, new Location(1, 1), true);

        $this->assertFalse(
            $var->hasDefaultValue()
        );
    }

    /**
     * @return array Array of <mixed: value to set, mixed: expected value>
     */
    public static function variableProvider()
    {
        return [
            [
                0,
                0
            ]
        ];
    }
}
