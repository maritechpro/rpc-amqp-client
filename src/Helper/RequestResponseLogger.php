<?php
declare(strict_types=1);

namespace GepurIt\RemoteProcedureCallBundle\Helper;

use Doctrine\ODM\MongoDB\DocumentManager;
use GepurIt\RemoteProcedureCallBundle\Document\RpcLog;
use GepurIt\RemoteProcedureCallBundle\Response\Response;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * Class RequestResponseLogger
 * @package GepurIt\RemoteProcedureCallBundle\Helper
 */
class RequestResponseLogger implements LoggerAwareInterface
{
    private DocumentManager $documentManager;
    private LoggerInterface $logger;

    /**
     * RequestResponseLogger constructor.
     * @param DocumentManager $documentManager
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * @param string   $jsonRequest
     * @param Response $response
     */
    public function logRequestResponse(string $jsonRequest, Response $response)
    {
        try {
            $request = json_decode($jsonRequest);
            $textLog = new RpcLog();
            $textLog->setStatus($response->getStatus());
            $textLog->setRequest($request);
            $textLog->setResponse(json_encode($response));
            $this->documentManager->persist($textLog);
            $this->documentManager->flush();
        } catch (\Exception $exception) {
            $this->logger->error(
                'Error while store request log',
                [
                    'exception' => $exception,
                    'jsonRequest' => $jsonRequest,
                    'response' => $response
                ]
            );
        }
    }

    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}