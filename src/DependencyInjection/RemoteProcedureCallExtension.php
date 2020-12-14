<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 12.12.18
 */

namespace GepurIt\RemoteProcedureCallBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class RemoteProcedureCallExtension
 * @package GepurIt\RemoteProcedureCallBundle\DependencyInjection
 * @codeCoverageIgnore
 */
class RemoteProcedureCallExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $configs   An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }
}
