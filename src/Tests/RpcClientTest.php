<?php

namespace App\GepurIt\RemoteProcedureCallBundle\Tests;

use GepurIt\SiteRpcBundle\Helper\RequestResponseLogger;
use GepurIt\SiteRpcBundle\Rabbit\ExchangeProvider;
use GepurIt\SiteRpcBundle\RpcClient\RpcClient;
use GepurIt\SiteRpcBundle\Exception\SiteRpcException;
use GepurIt\SiteRpcBundle\Request\Request;
use GepurIt\SiteRpcBundle\Response\Response;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class RpcClientTest
 * @package App\GepurIt\SiteRpcBundle\Tests
 * @group unit
 */
class RpcClientTest extends TestCase
{
    public function testSendSuccess()
    {
        /**@var ExchangeProvider|\PHPUnit_Framework_MockObject_MockObject $exchangeProvider */
        $exchangeProvider = $this->createMock(ExchangeProvider::class);
        /**@var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        /**@var Request|\PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->createMock(Request::class);
        /**@var \AMQPExchange|\PHPUnit_Framework_MockObject_MockObject $exchange */
        $exchange = $this->createMock(\AMQPExchange::class);
        /**@var RequestResponseLogger|\PHPUnit_Framework_MockObject_MockObject $log */
        $log = $this->createMock(RequestResponseLogger::class);
        /**@var \AMQPQueue|\PHPUnit_Framework_MockObject_MockObject $callBackQueue */
        $callBackQueue = $this->createMock(\AMQPQueue::class);

        $mockData = [
            'key0' => 'data0',
            'key1' => 'data1'
        ];
        $serializationContext = null;
        $message = 'message';

        $exchangeProvider->expects($this->once())
            ->method('getExchange')
            ->willReturn($exchange);

        $request->expects($this->once())
            ->method('toArray')
            ->willReturn($mockData);

        $request->expects($this->once())
            ->method('getSerializationContext')
            ->willReturn($serializationContext);

        $serializer->expects($this->once())
            ->method('serialize')
            ->with($mockData, 'json', $serializationContext)
            ->willReturn($message);

        $exchange->expects($this->once())
            ->method('publish')
            ->willReturn(true);

        $exchangeProvider->expects($this->once())
            ->method('getCallBackQueue')
            ->willReturn($callBackQueue);

        $log->expects($this->once())
            ->method('logRequestResponse');

        $rpcClient = new RpcClient($serializer, $exchangeProvider, $log);
        $result = $rpcClient->send($request);
        $this->assertInstanceOf(Response::class, $result);
    }

    /**
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPQueueException
     */
    public function testGetResponse()
    {
        /**@var ExchangeProvider|\PHPUnit_Framework_MockObject_MockObject $exchangeProvider */
        $exchangeProvider = $this->createMock(ExchangeProvider::class);
        /**@var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        /**@var RequestResponseLogger|\PHPUnit_Framework_MockObject_MockObject $log */
        $log = $this->createMock(RequestResponseLogger::class);
        /**@var \AMQPQueue|\PHPUnit_Framework_MockObject_MockObject $callBackQueue */
        $callBackQueue = $this->createMock(\AMQPQueue::class);

        $messageId = 123456;

        $exchangeProvider->expects($this->once())
            ->method('getCallBackQueue')
            ->willReturn($callBackQueue);

        $rpcClient = new RpcClient($serializer, $exchangeProvider, $log);
        $result = $rpcClient->getResponse($messageId);
        $this->assertInstanceOf(Response::class, $result);
    }

    public function testThrowExceptionGetExchange()
    {
        /**@var ExchangeProvider|\PHPUnit_Framework_MockObject_MockObject $exchangeProvider */
        $exchangeProvider = $this->createMock(ExchangeProvider::class);
        /**@var Request|\PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->createMock(Request::class);
        /**@var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        /**@var RequestResponseLogger|\PHPUnit_Framework_MockObject_MockObject $log */
        $log = $this->createMock(RequestResponseLogger::class);

        $exchangeProvider->expects($this->once())
            ->method('getExchange')
            ->willThrowException(new \AMQPException());

        $rpcClient = new RpcClient($serializer, $exchangeProvider, $log);
        $this->expectException(SiteRpcException::class);
        $rpcClient->send($request);
    }

    public function testThrowExceptionPublish()
    {
        /**@var ExchangeProvider|\PHPUnit_Framework_MockObject_MockObject $exchangeProvider */
        $exchangeProvider = $this->createMock(ExchangeProvider::class);
        /**@var Request|\PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->createMock(Request::class);
        /**@var \AMQPExchange|\PHPUnit_Framework_MockObject_MockObject $exchange */
        $exchange = $this->createMock(\AMQPExchange::class);
        /**@var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        /**@var RequestResponseLogger|\PHPUnit_Framework_MockObject_MockObject $log */
        $log = $this->createMock(RequestResponseLogger::class);

        $exchangeProvider->expects($this->once())
            ->method('getExchange')
            ->willReturn($exchange);

        $exchange->expects($this->once())
            ->method('publish')
            ->willReturn(false)
            ->willThrowException(new \AMQPException());


        $rpcClient = new RpcClient($serializer, $exchangeProvider, $log);
        $this->expectException(SiteRpcException::class);
        $rpcClient->send($request);
    }

    public function testThrowExceptionGetResponse()
    {
        /**@var ExchangeProvider|\PHPUnit_Framework_MockObject_MockObject $exchangeProvider */
        $exchangeProvider = $this->createMock(ExchangeProvider::class);
        /**@var Request|\PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->createMock(Request::class);
        /**@var \AMQPExchange|\PHPUnit_Framework_MockObject_MockObject $exchange */
        $exchange = $this->createMock(\AMQPExchange::class);
        /**@var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        /**@var RequestResponseLogger|\PHPUnit_Framework_MockObject_MockObject $log */
        $log = $this->createMock(RequestResponseLogger::class);

        $exchangeProvider->expects($this->once())
            ->method('getExchange')
            ->willReturn($exchange);


        $exchange->expects($this->once())
            ->method('publish')
            ->willReturn(true);

        $exchangeProvider->expects($this->once())
            ->method('getCallBackQueue')
            ->willThrowException(new \AMQPException());

        $rpcClient = new RpcClient($serializer, $exchangeProvider, $log);

        $this->expectException(SiteRpcException::class);
        $rpcClient->send($request);
    }

    public function testThrowExceptionSerialize()
    {
        /**@var ExchangeProvider|\PHPUnit_Framework_MockObject_MockObject $exchangeProvider */
        $exchangeProvider = $this->createMock(ExchangeProvider::class);
        /**@var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        /**@var Request|\PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->createMock(Request::class);
        /**@var \AMQPExchange|\PHPUnit_Framework_MockObject_MockObject $exchange */
        $exchange = $this->createMock(\AMQPExchange::class);
        /**@var RequestResponseLogger|\PHPUnit_Framework_MockObject_MockObject $log */
        $log = $this->createMock(RequestResponseLogger::class);

        $mockData = [
            'key0' => 'data0',
            'key1' => 'data1'
        ];
        $serializationContext = null;

        $exchangeProvider->expects($this->once())
            ->method('getExchange')
            ->willReturn($exchange);

        $request->expects($this->once())
            ->method('toArray')
            ->willReturn($mockData);

        $request->expects($this->once())
            ->method('getSerializationContext')
            ->willReturn($serializationContext);

        $serializer->expects($this->once())
            ->method('serialize')
            ->with($mockData, 'json', $serializationContext)
            ->willThrowException(new \AMQPException());

        $rpcClient = new RpcClient($serializer, $exchangeProvider, $log);
        $this->expectException(SiteRpcException::class);
        $rpcClient->send($request);
    }
}