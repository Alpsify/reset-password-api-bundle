<?php


namespace Alpsify\ResetPasswordAPIBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Nathan De Pachtere
 */
class AlpsifyResetPasswordExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('reset_password_api_config.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $resetPasswordHelperDefinition = $container->getDefinition('alpsify.reset_password_api.reset_password_api_helper');
        $resetPasswordHelperDefinition->replaceArgument(1, $config['token']['lifetime']);
        $resetPasswordHelperDefinition->replaceArgument(2, $config['throttle_time']);
        $resetPasswordHelperDefinition->replaceArgument(3, $config['user_types']);
        $resetPasswordHelperDefinition->replaceArgument(5, new Reference($config['persistence']['repository']));
        $resetPasswordHelperDefinition->replaceArgument(6, $config['persistence']['class']);

        $tokenGeneratorDefinition = $container->getDefinition('alpsify.reset_password_api.generator.token_generator');
        $tokenGeneratorDefinition->replaceArgument(1, $config['token']['hash_algo']);
        $tokenGeneratorDefinition->replaceArgument(3, $config['token']['selector_size']);

        $resetPasswordMailerDefinition = $container->getDefinition('alpsify.reset_password_api.util.mailer');
        $resetPasswordMailerDefinition->replaceArgument(1, $config['mailer']['from_email']);
        $resetPasswordMailerDefinition->replaceArgument(2, $config['mailer']['from_name']);
        $resetPasswordMailerDefinition->replaceArgument(3, $config['mailer']['template']);
    }

    public function getAlias()
    {
        return 'alpsify_reset_password_api';
    }
}
