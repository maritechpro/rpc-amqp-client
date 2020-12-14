<?php


namespace App\GepurIt\RemoteProcedureCallBundle\Tests;

use GepurIt\SiteRpcBundle\Response\Response;
use PHPUnit\Framework\TestCase;

/**
 * Class ResponseTest
 * @package App\GepurIt\SiteRpcBundle\Tests
 * @group unit
 */
class ResponseTest extends TestCase
{

    public function testGetStatus()
    {
        $body = json_encode(
            [
                'status'  => 'status1',
                'payload' => 'payload1',
            ]
        );
        $response = new Response($body);
        $this->assertEquals('status1', $response->getStatus());
    }

    public function testGetPayload()
    {
        $body = json_encode(
            [
                'status'  => 'status1',
                'payload' => 'payload1',
            ]
        );
        $response = new Response($body);
        $this->assertEquals('payload1', $response->getPayload());
    }

    public function testGetText()
    {
        $body = json_encode(
            [
                'status'  => 'status1',
                'payload' => 'payload1',
            ]
        );
        $response = new Response($body);
        $this->assertEquals($body, $response->getText());
    }
}