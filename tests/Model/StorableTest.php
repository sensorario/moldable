<?php

namespace Javanile\Moldable\Tests\Model;

use Javanile\Producer;
use Javanile\Moldable\Database;
use PHPUnit\Framework\TestCase;
use Javanile\Moldable\Tests\Model\Sample\People;
use Javanile\Moldable\Tests\DatabaseTrait;

Producer::addPsr4(['Javanile\\Moldable\\Tests\\' => __DIR__.'/../']);

final class StorableTest extends TestCase
{
    use DatabaseTrait;

    public function testSimpleStorable()
    {
        $db = new Database([
            'host'     => $GLOBALS['DB_HOST'],
            'dbname'   => $GLOBALS['DB_NAME'],
            'username' => $GLOBALS['DB_USER'],
            'password' => $GLOBALS['DB_PASS'],
        ]);

        People::resetClass();

        $people = new People();

        $id = $people->store(['name' => 'Frank']);

        $this->assertEquals($id, 1);

        $row = $db->getRow("SELECT * FROM People WHERE id = 1");

        $this->assertEquals($row['name'], 'Frank');
    }

    public function testSimpleStorableTwo()
    {
        /*
        $db = new Database([
            'host'     => $GLOBALS['DB_HOST'],
            'dbname'   => $GLOBALS['DB_NAME'],
            'username' => $GLOBALS['DB_USER'],
            'password' => $GLOBALS['DB_PASS'],
            'prefix'   => 'prefix_',
        ]);

        People::resetClass();

        $frank = new People();
        $frank->store(['name' => 'Frank']);

        $carol = new People();
        $carol->store(['name' => 'Carol']);

        $names = $db->getValues("SELECT name FROM prefix_People");

        $this->assertEquals($names, ['Frank', 'Carol']);
        */
    }
}