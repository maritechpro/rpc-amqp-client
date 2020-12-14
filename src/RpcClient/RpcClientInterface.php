<?php
/**
 * @author: Marina Mileva m934222258@gmail.com
 * @since : 25.09.20 19:11
 */

namespace GepurIt\RemoteProcedureCallBundle\RpcClient;

use GepurIt\RemoteProcedureCallBundle\Request\Request;
use GepurIt\RemoteProcedureCallBundle\Response\Response;

/**
 * Class RpcClientInterface
 * @package GepurIt\RemoteProcedureCallBundle\RpcClient
 */
interface RpcClientInterface
{
    /**
     * @param Request       $request
     * @param callable|null $filter
     *
     * @return Response
     */
    public function send(Request $request, ?callable $filter = null): Response;

    /**
     * @param string $messageId
     *
     * @return Response
     */
    public function getResponse(string $messageId): Response;
}