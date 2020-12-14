<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 12.12.18
 */

namespace GepurIt\RemoteProcedureCallBundle\Request;

use JMS\Serializer\SerializationContext;

/**
 * Class Request
 * @package GepurIt\RemoteProcedureCallBundle\Request
 */
class Request
{
    const ACTION__CHANGE_USER = 'changeUserType';
    const ACTION__ONE_CLICK = 'ProcessOneClickOrder';
    const ACTION__ABANDONED_CART = 'ProcessAbandonedCart';
    const ACTION__PREDICT_DISCOUNT = 'predictDiscount';
    const ACTION__UPDATE_LOYALTY = 'updateLoyalty';

    private string $action = '';
    /** @var mixed */
    private $payload = [];
    private ?SerializationContext $serializationContext = null;

    /**
     * Request constructor.
     *
     * @param string $action
     */
    public function __construct(string $action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param mixed $payload
     */
    public function setPayload($payload): void
    {
        $this->payload = $payload;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'action' => $this->getAction(),
            'payload' => $this->getPayload()
        ];
    }

    /**
     * @return SerializationContext|null
     */
    public function getSerializationContext(): ?SerializationContext
    {
        return $this->serializationContext;
    }

    /**
     * @param SerializationContext $serializationContext
     */
    public function setSerializationContext(SerializationContext $serializationContext): void
    {
        $this->serializationContext = $serializationContext;
    }
}
