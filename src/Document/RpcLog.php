<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 12.12.18
 */

namespace GepurIt\RemoteProcedureCallBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class RpcLog
 * @package GepurIt\RemoteProcedureCallBundle\Document
 * @MongoDB\Document(collection="RpcLog")
 * @MongoDB\HasLifecycleCallbacks()
 * @codeCoverageIgnore
 */
class RpcLog
{
    /**
     * @var string
     * @MongoDB\Id()
     */
    private string $logId = '';

    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    private string $queue = '';

    /**
     * @var mixed
     * @MongoDB\Field(type="raw")
     */
    private $request;

    /**
     * @var mixed
     * @MongoDB\Field(type="raw")
     */
    private $response;

    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    private string $status = '';

    /**
     * @var \DateTime
     * @MongoDB\Field(type="date")
     */
    private ?\DateTime $createdAt = null;

    /**
     * SiteRpcLog constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime("now");
    }

    /**
     * @return string
     */
    public function getLogId(): string
    {
        return $this->logId;
    }

    /**
     * @param string $logId
     */
    public function setLogId(string $logId): void
    {
        $this->logId = $logId;
    }

    /**
     * @return string
     */
    public function getQueue(): string
    {
        return $this->queue;
    }

    /**
     * @param string $queue
     */
    public function setQueue(string $queue): void
    {
        $this->queue = $queue;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param $response
     */
    public function setResponse($response): void
    {
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
