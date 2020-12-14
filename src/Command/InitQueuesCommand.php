<?php
/**
 * @author: Mari Mileva <m934222258@gmail.com>
 * @since: 25.09.2020
 */

namespace GepurIt\RemoteProcedureCallBundle\Command;

use GepurIt\RabbitMqBundle\RabbitInterface;
use GepurIt\SiteRpcBundle\RpcClient\RpcClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InitQueuesCommand
 * @package GepurIt\RemoteProcedureCallBundle\Command
 * @codeCoverageIgnore
 */
class InitQueuesCommand extends Command
{
    private OutputInterface $output;
    private InputInterface $input;
    private RabbitInterface $rabbit;

    /**
     * ExecuteSender constructor.
     *
     * @param RabbitInterface $rabbit
     */
    public function __construct(
        RabbitInterface $rabbit
    ) {
        $this->rabbit  = $rabbit;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('rpc:rabbit:init')
            ->setDescription('Init RabbitMQ queues and exchanges')
            ->setHelp('Example usage: site_rpc:rabbit:init');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->output->writeln("Start initializing queues");
        $channel = $this->rabbit->getChannel();


        $this->output->write("declaring exchange: ");
        $exchange = new \AMQPExchange($channel);
        $exchange->setName(RpcClient::RPC_QUEUE_NAME);
        $exchange->setType(AMQP_EX_TYPE_DIRECT);
        $exchange->setFlags(AMQP_DURABLE);
        $exchange->declareExchange();
        $this->output->writeln("DONE");

        $this->output->write("declaring queue: ");
        $queue = new \AMQPQueue($channel);
        $queue->setName(RpcClient::RPC_QUEUE_NAME);
        $queue->setFlags(AMQP_DURABLE);
        $queue->declareQueue();
        $this->output->writeln("DONE");

        $this->output->write("binding exchange: ");
        $queue->bind(RpcClient::RPC_QUEUE_NAME, RpcClient::RPC_QUEUE_NAME);
        $this->output->writeln("DONE");

        $this->output->write("declaring callback exchange: ");
        $exchange = new \AMQPExchange($channel);
        $exchange->setName(RpcClient::RPC_CALLBACK_NAME);
        $exchange->setType(AMQP_EX_TYPE_DIRECT);
        $exchange->setFlags(AMQP_DURABLE);
        $exchange->declareExchange();
        $this->output->writeln("DONE");

        $this->output->write("declaring callback queue: ");
        $callBackQueue = new \AMQPQueue($channel);
        $callBackQueue->setName(RpcClient::RPC_CALLBACK_NAME);
        $callBackQueue->setFlags(AMQP_DURABLE);
        $callBackQueue->setArgument('x-message-ttl', 60000);
        $callBackQueue->declareQueue();
        $this->output->writeln("DONE");

        $this->output->write("binding exchange: ");
        $callBackQueue->bind(RpcClient::RPC_CALLBACK_NAME, RpcClient::RPC_CALLBACK_NAME);
        $this->output->writeln("DONE");
        return 0;
    }
}
