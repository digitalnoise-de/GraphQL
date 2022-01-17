<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 10:48 PM 5/14/16
 */

namespace Youshido\Tests\DataProvider;


use Youshido\GraphQL\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Scalar\IntType;

class TestSchema extends AbstractSchema
{
    private int $testStatusValue = 0;

    public function build(SchemaConfig $config)
    {
        $config->getQuery()->addFields([
            'me'     => [
                'type'    => new TestObjectType(),
                'resolve' => fn($value, $args, ResolveInfo $info) => $info->getReturnType()->getData()
            ],
            'status' => [
                'type'    => new TestEnumType(),
                'resolve' => fn() => $this->testStatusValue
            ],
        ]);
        $config->getMutation()->addFields([
            'updateStatus' => [
                'type'    => new TestEnumType(),
                'resolve' => fn() => $this->testStatusValue,
                'args'    => [
                    'newStatus' => new TestEnumType(),
                    'list' => new ListType(new IntType())
                ]
            ]
        ]);
    }


}
