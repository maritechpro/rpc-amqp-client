<?php


namespace GepurIt\RemoteProcedureCallBundle\Tests;

use PHPUnit\Framework\TestCase;
use GepurIt\SiteRpcBundle\Request\Request;

/**
 * Class RequestTest
 * @package App\GepurIt\SiteRpcBundle\Tests
 * @group unit
 */
class RequestTest extends TestCase
{
    public function testGetAction() {
        $action = new Request('name');
        $this->assertEquals('name', $action->getAction());
    }

    public function testGetPayload() {
        $action = new Request('name');
        $array = [];
        $this->assertEquals($array, $action->getPayload());
    }

    public function testSetPayload() {
        $action = new Request('name');
        $randomData = 12;
        $this->assertNull($action->setPayload($randomData));
        $this->assertEquals($randomData, $action->getPayload());
    }

    public function testToArray() {
        $action = new Request('name');
        $array = [
            'action' => 'name',
            'payload' => []
        ];
        $this->assertEquals($array, $action->toArray());
    }

}

