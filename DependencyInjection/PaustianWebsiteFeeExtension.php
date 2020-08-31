<?php
/**
 * Created by PhpStorm.
 * User: paustian
 * Date: 10/21/17
 * Time: 8:18 PM
 */

namespace Paustian\WebsiteFeeModule\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
/**
 * Loads the services.yml file.
 */
class PaustianWebsiteFeeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }
}