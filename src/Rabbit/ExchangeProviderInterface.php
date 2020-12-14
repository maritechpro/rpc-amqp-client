<?php
/**
 * @author: Marina Mileva m934222258@gmail.com
 * @since : 25.09.20 13:38
 */
declare(strict_types=1);

namespace GepurIt\RemoteProcedureCallBundle\Rabbit;

use AMQPExchange;
use AMQPQueue;

/**
 * Interface ExchangeProviderInterface
 * @package GepurIt\RemoteProcedureCallBundle\Rabbit
 */
interface ExchangeProviderInterface
{
    /**
     * @return AMQPExchange
     */
    public function getExchange(): AMQPExchange;

    /**
     * @return AMQPQueue
     */
    public function getCallBackQueue(): AMQPQueue;
}