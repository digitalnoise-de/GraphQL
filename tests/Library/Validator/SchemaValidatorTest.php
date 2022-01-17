<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/15/16 4:04 PM
*/

namespace Youshido\Tests\Library\Validator;


use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Validator\SchemaValidator\SchemaValidator;
use Youshido\Tests\DataProvider\TestEmptySchema;
use Youshido\Tests\DataProvider\TestInterfaceType;

class SchemaValidatorTest extends \PHPUnit\Framework\TestCase
{
    public function testInvalidSchema()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        $validator = new SchemaValidator();
        $validator->validate(new TestEmptySchema());
    }

    public function testInvalidInterfacesSimpleType()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        $this->expectExceptionMessage('Implementation of TestInterface is invalid for the field name');
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'user' => new ObjectType([
                        'name'       => 'User',
                        'fields'     => [
                            'name' => new IntType(),
                        ],
                        'interfaces' => [new TestInterfaceType()]
                    ])
                ],
            ])
        ]);

        $validator = new SchemaValidator();
        $validator->validate($schema);
    }

    public function testInvalidInterfacesCompositeType()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        $this->expectExceptionMessage('Implementation of TestInterface is invalid for the field name');
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'user' => new ObjectType([
                        'name'       => 'User',
                        'fields'     => [
                            'name' => new NonNullType(new StringType()),
                        ],
                        'interfaces' => [new TestInterfaceType()]
                    ])
                ],
            ])
        ]);

        $validator = new SchemaValidator();
        $validator->validate($schema);
    }

    public function testInvalidInterfaces()
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        $this->expectExceptionMessage('Implementation of TestInterface is invalid for the field name');
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'user' => new ObjectType([
                        'name'       => 'User',
                        'fields'     => [
                            'name' => new IntType(),
                        ],
                        'interfaces' => [new TestInterfaceType()]
                    ])
                ],
            ])
        ]);

        $validator = new SchemaValidator();
        $validator->validate($schema);
    }

    public function testValidSchema()
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'user' => new ObjectType([
                        'name'       => 'User',
                        'fields'     => [
                            'name' => new StringType(),
                        ],
                        'interfaces' => [new TestInterfaceType()]
                    ])
                ],
            ])
        ]);

        $validator = new SchemaValidator();

        try {
            $validator->validate($schema);
            $this->assertTrue(true);
        } catch (\Exception) {
            $this->assertTrue(false);
        }
    }
}
