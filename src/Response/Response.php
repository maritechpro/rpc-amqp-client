<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 12.12.18
 */

namespace GepurIt\RemoteProcedureCallBundle\Response;

/**
 * Class Response
 * @package GepurIt\SiteRpcBundle\Response
 */
class Response implements \JsonSerializable
{
    const STATUS__OK     = 'OK';
    const STATUS__FALIED = 'Failed';

    /** @var string */
    private $status;

    /** @var mixed */
    private $payload;

    /** @var string */
    private $text;

    /**
     * Response constructor.
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        $body = json_decode($message, true);
        $this->status  = $body['status'];
        $this->payload = $body['payload'];
        $this->text = $message;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'status' => $this->getStatus(),
            'payload' => $this->getPayload(),
            'message' => $this->text,
        ];
    }
}
