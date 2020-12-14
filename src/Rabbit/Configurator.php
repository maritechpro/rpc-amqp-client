<?php
/**
 * @author: Marina Mileva m934222258@gmail.com
 * @since : 25.09.20 13:56
 */
declare(strict_types=1);

namespace GepurIt\RemoteProcedureCallBundle\Rabbit;

use GepurIt\RabbitMqBundle\RabbitInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Configurator
 * @package GepurIt\RemoteProcedureCallBundle\Rabbit
 */
class Configurator
{
    private array $queues = [];
    private $container;

    /**
     * Configurator constructor.
     *
     * @param array $configs
     */
    public function __construct(RabbitInterface $rabbit, string $queue)
    {
        var_dump($queue, $rabbit);
//        foreach ($configs as $config) {
//            var_dump($config);
////            $this->queues[$config['queue']] = $this->container->get($config['rabbit'] === 'default' ? 'rabbit_mq' : 'rabbit_mq.'.$config['rabbit']);;
//        }
    }

    /**
     * @return array
     */
    public function getQueues(): array
    {
        return $this->queues;
    }

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}