<?php
/**
 * Date: 12.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\Library\Type;


use Youshido\GraphQL\Type\Enum\EnumType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Youshido\Tests\DataProvider\TestEnumType;

class EnumTypeTest extends \PHPUnit\Framework\TestCase
{

    public function testInvalidInlineCreation()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        new EnumType([]);
    }

    public function testInvalidEmptyParams()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        $enumField = new EnumType([
            'values' => []
        ]);
        ConfigValidator::getInstance()->assertValidConfig($enumField->getConfig());

    }

    public function testInvalidValueParams()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        $enumField = new EnumType([
            'values' => [
                'test'  => 'asd',
                'value' => 'asdasd'
            ]
        ]);
        ConfigValidator::getInstance()->assertValidConfig($enumField->getConfig());
    }

    public function testExistingNameParams()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        $enumField = new EnumType([
            'values' => [
                [
                    'test'  => 'asd',
                    'value' => 'asdasd'
                ]
            ]
        ]);
        ConfigValidator::getInstance()->assertValidConfig($enumField->getConfig());
    }

    public function testInvalidNameParams()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        $enumField = new EnumType([
            'values' => [
                [
                    'name'  => false,
                    'value' => 'asdasd'
                ]
            ]
        ]);
        ConfigValidator::getInstance()->assertValidConfig($enumField->getConfig());
    }

    public function testWithoutValueParams()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        $enumField = new EnumType([
            'values' => [
                [
                    'name' => 'TEST_ENUM',
                ]
            ]
        ]);
        ConfigValidator::getInstance()->assertValidConfig($enumField->getConfig());
    }

    public function testNormalCreatingParams()
    {
        $valuesData = [
            [
                'name'  => 'ENABLE',
                'value' => true
            ],
            [
                'name'  => 'DISABLE',
                'value' => 'disable'
            ]
        ];
        $enumType   = new EnumType([
            'name'   => 'BoolEnum',
            'values' => $valuesData
        ]);

        $this->assertEquals($enumType->getKind(), TypeMap::KIND_ENUM);
        $this->assertEquals($enumType->getName(), 'BoolEnum');
        $this->assertEquals($enumType->getType(), $enumType);
        $this->assertEquals($enumType->getNamedType(), $enumType);

        $this->assertFalse($enumType->isValidValue($enumType));
        $this->assertTrue($enumType->isValidValue(null));

        $this->assertTrue($enumType->isValidValue(true));
        $this->assertTrue($enumType->isValidValue('disable'));

        $this->assertNull($enumType->serialize('invalid value'));
        $this->assertNull($enumType->parseValue('invalid literal'));
        $this->assertTrue($enumType->parseValue('ENABLE'));

        $this->assertEquals($valuesData, $enumType->getValues());
    }

    public function testExtendedObject()
    {
        $testEnumType = new TestEnumType();
        $this->assertEquals('TestEnum', $testEnumType->getName());
    }

}
