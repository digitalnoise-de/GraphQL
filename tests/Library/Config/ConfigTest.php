<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/11/16 10:41 PM
*/

namespace Youshido\Tests\Library\Config;

use Youshido\GraphQL\Type\Enum\EnumType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\TypeService;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Youshido\Tests\DataProvider\TestConfig;
use Youshido\Tests\DataProvider\TestConfigExtraFields;
use Youshido\Tests\DataProvider\TestConfigInvalidRule;

class ConfigTest extends \PHPUnit\Framework\TestCase
{

    public function testEmptyParams()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        new TestConfig([]);
    }

    public function testInvalidParams()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        ConfigValidator::getInstance()->assertValidConfig(new TestConfig(['id' => 1]));
    }

    public function testInvalidMethod()
    {
        $this->expectException(\Exception::class);
        $config = new TestConfig(['name' => 'test']);
        $config->doSomethingStrange();
    }

    public function testMethods()
    {
        $name  = 'Test';
        $rules = [
            'name'    => ['type' => TypeService::TYPE_ANY, 'required' => true],
            'resolve' => ['type' => TypeService::TYPE_CALLABLE, 'final' => true],
        ];

        $config = new TestConfig(['name' => $name]);
        $this->assertEquals($config->getName(), $name);
        $this->assertEquals($config->get('name'), $name);
        $this->assertEquals($config->get('non existing key'), null);
        $this->assertEquals($config->set('name', 'StrangeName'), $config);
        $this->assertEquals($config->get('name'), 'StrangeName');
        $this->assertEquals($config->get('non existing', 'default'), 'default');
        $this->assertEquals($config->isName(), 'StrangeName');
        $this->assertEquals($config->setName('StrangeName 2'), $config);

        $config->set('var', 'value');
        $this->assertEquals($config->getVar(), 'value');

        $this->assertEquals($config->getRules(), $rules);
        $this->assertEquals($config->getContextRules(), $rules);
        $this->assertNull($config->getResolveFunction());

        $object = new ObjectType([
            'name'   => 'TestObject',
            'fields' => [
                'id' => [
                    'type' => new IntType()
                ]
            ]
        ]);

        $finalConfig = new TestConfig(['name' => $name . 'final', 'resolve' => fn() => []], $object, true);
        $this->assertEquals($finalConfig->getType(), null);

        $rules['resolve']['required'] = true;
        $this->assertEquals($finalConfig->getContextRules(), $rules);

        $this->assertNotNull($finalConfig->getResolveFunction());

        $configExtraFields = new TestConfigExtraFields([
            'name'       => 'Test',
            'extraField' => 'extraValue'
        ]);
        $this->assertEquals('extraValue', $configExtraFields->get('extraField'));
    }

    public function testFinalRule()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        ConfigValidator::getInstance()->assertValidConfig(new TestConfig(['name' => 'Test' . 'final'], null, true));
    }

    public function testInvalidRule()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        ConfigValidator::getInstance()->assertValidConfig(
            new TestConfigInvalidRule(['name' => 'Test', 'invalidRuleField' => 'test'], null, null)
        );
    }

    public function testEnumConfig()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        $enumType = new EnumType([
            'name'   => 'Status',
            'values' => [
                [
                    'name'   => 'ACTIVE',
                    'values' => 1
                ]
            ]
        ]);
        $object   = new ObjectType([
            'name' => 'Project',
            'fields' => [
                'id' => new IdType(),
                'status' => $enumType
            ]
        ]);
        ConfigValidator::getInstance()->assertValidConfig($object->getConfig());
    }

}
