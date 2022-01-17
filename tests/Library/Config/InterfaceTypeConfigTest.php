<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/12/16 4:17 PM
*/

namespace Youshido\Tests\Library\Config;


use Youshido\GraphQL\Config\Object\InterfaceTypeConfig;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Youshido\Tests\DataProvider\TestInterfaceType;

class InterfaceTypeConfigTest extends \PHPUnit\Framework\TestCase
{

    public function testCreation()
    {
        $config = new InterfaceTypeConfig(['name' => 'Test'], null, false);
        $this->assertEquals($config->getName(), 'Test', 'Normal creation');
    }

    public function testConfigNoFields()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        ConfigValidator::getInstance()->assertValidConfig(
            new InterfaceTypeConfig(['name' => 'Test', 'resolveType' => function () { }], null, true)
        );
    }

    public function testConfigNoResolve()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        ConfigValidator::getInstance()->assertValidConfig(
            new InterfaceTypeConfig(['name' => 'Test', 'fields' => ['id' => new IntType()]], null, true)
        );
    }

    public function testConfigInvalidResolve()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        $config = new InterfaceTypeConfig(['name' => 'Test', 'fields' => ['id' => new IntType()]], null, false);
        $config->resolveType(['invalid object']);
    }

    public function testInterfaces()
    {
        $interfaceConfig = new InterfaceTypeConfig([
            'name'        => 'Test',
            'fields'      => ['id' => new IntType()],
            'resolveType' => fn($object) => $object->getType()
        ], null, true);
        $object          = new ObjectType(['name' => 'User', 'fields' => ['name' => new StringType()]]);

        $this->assertEquals($interfaceConfig->getName(), 'Test');
        $this->assertEquals($interfaceConfig->resolveType($object), $object->getType());

        $testInterface                = new TestInterfaceType();
        $interfaceConfigWithNoResolve = new InterfaceTypeConfig([
            'name'   => 'Test',
            'fields' => ['id' => new IntType()]
        ], $testInterface, false);
        $this->assertEquals($interfaceConfigWithNoResolve->resolveType($object), $object);
    }


}
