<?php
/**
 * @author: Mari <m934222258@gmail.com>
 * @since : 25.09.2020
 */
declare(strict_types=1);

namespace GepurIt\RemoteProcedureCallBundle\RpcClient;

use GepurIt\RemoteProcedureCallBundle\Exception\RemoteProcedureCallException;
use GepurIt\RemoteProcedureCallBundle\Helper\RequestResponseLogger;
use GepurIt\RemoteProcedureCallBundle\Rabbit\ExchangeProvider;
use GepurIt\RemoteProcedureCallBundle\Rabbit\ExchangeProviderInterface;
use GepurIt\RemoteProcedureCallBundle\Request\Request;
use GepurIt\RemoteProcedureCallBundle\Response\Response;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class RemoteProcedureCallClient
 * @package GepurIt\RemoteProcedureCallBundle\RpcClient
 */
class RemoteProcedureCallClient implements RpcClientInterface
{
    const TIMEOUT_PAYLOAD = 'TimeOut';

    private SerializerInterface $serializer;
    private ExchangeProviderInterface $exchangeProvider;
    private RequestResponseLogger $log;
    private int $maxIterations;

    /**
     * RemoteProcedureCallClient constructor.
     *
     * @param SerializerInterface       $serializer
     * @param ExchangeProviderInterface $exchangeProvider
     * @param RequestResponseLogger     $log
     * @param int                       $maxIterations
     */
    public function __construct(
        SerializerInterface $serializer,
        ExchangeProviderInterface $exchangeProvider,
        RequestResponseLogger $log,
        int $maxIterations = 100
    ) {
        $this->serializer = $serializer;
        $this->exchangeProvider = $exchangeProvider;
        $this->log = $log;
        $this->maxIterations = $maxIterations;
    }

    /**
     * @param Request $request
     * @param callable|null $filter
     *
     * @return Response
     */
    public function send(Request $request, ?callable $filter = null): Response
    {
        try {
            $exchange = $this->exchangeProvider->getExchange();
            $message = $this->serializer->serialize(
                $request->toArray(),
                'json',
                $request->getSerializationContext()
            );
            $messageId = uniqid();
            $params = [
                'message_id'   => $messageId,
                'reply_to'     => $this->exchangeProvider->getCallBackQueue()->getName(),
                'content_type' => 'application/json',
            ];

            $message = $filter ? $filter($message) : $message;

            $exchange->publish($message, $exchange->getName(), AMQP_NOPARAM, $params);

            $response = $this->getResponse($messageId);
        } catch (\AMQPException $exception) {
            throw new RemoteProcedureCallException("Rpc request failed", 0, $exception);
        }

        $this->log->logRequestResponse($message, $response);

        return $response;
    }

    /**
     * @param string $messageId
     *
     * @return Response
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     */
    public function getResponse(string $messageId): Response
    {
        $callBackQueue = $this->exchangeProvider->getCallBackQueue();
        $iterations = 0;
        while ($iterations < $this->maxIterations) {
            $envelope = $callBackQueue->get();
            if (!empty($envelope)) {
                if ($envelope->getMessageId() === $messageId) {
                    $callBackQueue->ack($envelope->getDeliveryTag());

                    return new Response($envelope->getBody());
                }
                continue;
            }
            $iterations++;
            usleep(50000);
        }

        $params = json_encode([
            'status'  => 'Fail',
            'payload' => self::TIMEOUT_PAYLOAD,
        ]);

        return new Response($params);
    }

    /**
     * Нам нужно создавать заказ только с теми товарами,
     * которые не отправлены в резерв в магазине
     * И дабы не делать случайных изменений в респонсе в монге
     * и не отправлять на сайт лишних данных - фильтранем-ка сообщение перед отправкой
     * Можно еще кучу лишних полей поудалять, кстати
     *
     * @param string $message
     *
     * @return string
     */
    private function filterRequestMessage(string $message): string
    {
        $request = json_decode($message, true);
        if (isset($request['payload']['order']['orderItems'])) {
            $request['payload']['order']['orderItems'] =
                array_filter(
                    $request['payload']['order']['orderItems'],
                    function ($item) {
                        return $item['isReserve'] === false;
                    }
                );
        }
        return json_encode($request, JSON_UNESCAPED_UNICODE);
    }
}
