<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 2:11 PM 5/19/16
 */

namespace Youshido\Tests\Library\Relay;


use Youshido\GraphQL\Relay\RelayMutation;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class MutationTest extends \PHPUnit\Framework\TestCase
{

    public function testCreation()
    {
        $mutation = RelayMutation::buildMutation('ship', [
            'name' => new StringType()
        ],[
            'id' => new IdType(),
            'name' => new StringType()
        ], function($source, $args, $info) {

        });
        $this->assertEquals('ship', $mutation->getName());
    }

    public function testInvalidType()
    {
        $this->expectException(\Exception::class);
        RelayMutation::buildMutation('ship', [
            'name' => new StringType()
        ], new IntType(), function($source, $args, $info) {});

    }

}
