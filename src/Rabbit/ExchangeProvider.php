<?php
/**
 * @author: Marina Mileva m934222258@gmail.com
 * @since : 25.09.20 13:45
 */
declare(strict_types=1);

namespace GepurIt\RemoteProcedureCallBundle\Rabbit;

use AMQPExchange;
use AMQPQueue;
use AMQPChannelException;
use AMQPConnectionException;
use AMQPExchangeException;
use AMQPQueueException;
use GepurIt\RabbitMqBundle\RabbitInterface;

/**
 * Class BaseExchangeProvider
 * @package GepurIt\RemoteProcedureCallBundle\Rabbit
 */
class ExchangeProvider implements ExchangeProviderInterface
{
    private RabbitInterface $rabbit;
    private string $queue;

    private ?AMQPExchange $exchange = null;
    private ?AMQPQueue $callbackQueue = null;

    /**
     * ExchangeProvider constructor.
     *
     * @param RabbitInterface $rabbit
     * @param string          $queue
     */
    public function __construct(RabbitInterface $rabbit, string $queue)
    {
        $this->rabbit = $rabbit;
        $this->queue = $queue;
    }

    /**
     * @return AMQPExchange
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     * @throws AMQPChannelException
     * @throws AMQPQueueException
     */
    public function getExchange(): AMQPExchange
    {
        if (null !== $this->exchange) {
            return $this->exchange;
        }

        $channel = $this->rabbit->getChannel();
        $this->exchange = new AMQPExchange($channel);
        $this->exchange->setName($this->queue);
        $this->exchange->setType(AMQP_EX_TYPE_DIRECT);
        $this->exchange->setFlags(AMQP_DURABLE);
        $this->exchange->declareExchange();

        $queue = new AMQPQueue($channel);
        $queue->setName($this->queue);
        $queue->setFlags(AMQP_DURABLE);
        $queue->declareQueue();
        $queue->bind($this->queue, $this->queue);

        return $this->exchange;
    }

    /**
     * @return AMQPQueue
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     * @throws AMQPQueueException
     */
    public function getCallBackQueue(): AMQPQueue
    {
        if ($this->callbackQueue !== null) {
            return $this->callbackQueue;
        }

        $channel = $this->rabbit->getChannel();
        $exchange = new AMQPExchange($channel);
        $callbackName = $this->queue.'_callback';
        $exchange->setName($callbackName);
        $exchange->setType(AMQP_EX_TYPE_DIRECT);
        $exchange->setFlags(AMQP_DURABLE);
        $exchange->declareExchange();

        $this->callbackQueue = new \AMQPQueue($channel);
        $this->callbackQueue->setName($callbackName);
        $this->callbackQueue->setFlags(AMQP_DURABLE);
        $this->callbackQueue->setArgument('x-message-ttl', 60000);
        $this->callbackQueue->declareQueue();

        $this->callbackQueue->bind($callbackName, $callbackName);


        return $this->callbackQueue;
    }
}