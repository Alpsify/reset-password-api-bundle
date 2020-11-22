<?php


namespace Alpsify\ResetPasswordAPIBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Nathan De Pachtere
 */
class AlpsifyResetPasswordExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('reset_password_config.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $resetPasswordHelperDefinition = $container->getDefinition('alpsify.reset_password_api.reset_password_helper');
        $resetPasswordHelperDefinition->replaceArgument(1, $config['token']['lifetime']);
        $resetPasswordHelperDefinition->replaceArgument(2, $config['throttle_time']);

        $tokenGeneratorDefinition = $container->getDefinition('alpsify.reset_password_api.generator.token_generator');
        $tokenGeneratorDefinition->replaceArgument(1, $config['token']['hash_algo']);

        $randomGeneratorDefinition = $container->getDefinition('alpsify.reset_password_api.generator.random_generator');
        $randomGeneratorDefinition->replaceArgument(0, $config['token']['selector_size']);
    }

    public function getAlias()
    {
        return 'alpsify_reset_password_api';
    }
}
