<?php


namespace Alpsify\ResetPasswordAPIBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\IntegerNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\ScalarNode;

/**
 * @author Nathan De Pachtere
 */
class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('alpsify_reset_password_api');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('token')
                    ->addDefaultsIfNotSet()
                    ->append($this->getLifeTime())
                    ->append($this->getSelectorSize())
                    ->append($this->getHashAlgo())
                    ->info('')
                ->end()
                ->append($this->getThrottleTime())
                ->append($this->getUserTypesNode())
                ->append($this->getPersistence())
                ->append($this->getMailer())
            ->end();

        return $treeBuilder;
    }

    public function getUserTypesNode()
    {
        $treeBuilder = new TreeBuilder('user_types');

        $node = $treeBuilder->getRootNode()
            ->info('Describe all your user types more')
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->arrayPrototype()
                ->children()
                    ->scalarNode('class')->isRequired()->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    public function getLifeTime()
    {
        $node = new IntegerNodeDefinition('lifetime');

        $node
            ->defaultValue(3600)
            ->info('Life time of the request in seconds. After that the token is invalid and the user need to ask for a new one.')
            ->validate()
                ->ifTrue(function ($v) {
                    return !$v > 3600;
                })
                ->thenInvalid('Lifetime must be at least 3600 (seconds)')
            ->end()
        ;

        return $node;
    }

    private function getSelectorSize()
    {
        $node = new IntegerNodeDefinition('selector_size');

        $node
            ->defaultValue(20)
            ->info('Customize the selector size of the token you send.')
            ->validate()
                ->ifTrue(function ($v) {
                    return !$v > 20;
                })
                ->thenInvalid('Selector must be at least 20 (charactors)')
            ->end()
        ;

        return $node;
    }

    private function getThrottleTime()
    {
        $node = new IntegerNodeDefinition('throttle_time');

        $node->defaultValue(3600)
            ->info('Time between 2 requests.')
            ->end();
        return $node;
    }

    private function getPersistence()
    {
        $node = new ArrayNodeDefinition('persistence');

        $node->addDefaultsIfNotSet()
            ->append($this->getPersistenceClassNode())
            ->append($this->getPersistenceRepositoryNode())
            ->end();

        return $node;
    }

    private function getPersistenceRepositoryNode()
    {
        $node = new ScalarNodeDefinition('repository');

        $node->info('Repository class linked to the entity')
            ->end();

        return $node;
    }

    private function getPersistenceClassNode()
    {
        $node = new ScalarNodeDefinition('class');

        $node->info('Class of the entity used for storing the user reset password request.')
            ->end();

        return $node;
    }

    private function getHashAlgo()
    {
        $node = new ScalarNodeDefinition('hash_algo');

        return $node;
    }

    private function getMailer()
    {
        $node = new ArrayNodeDefinition('mailer');

        $node->addDefaultsIfNotSet()
            ->append($this->getFromEmailNode())
            ->append($this->getFromNameNode())
            ->append($this->getTemplateNode())
            ->end();

        return $node;
    }

    private function getFromEmailNode()
    {
        $node = new ScalarNodeDefinition('from_email');

        $node->info('Your choosen email. The reset email will be send through this one.');

        return $node;
    }
    private function getFromNameNode()
    {
        $node = new ScalarNodeDefinition('from_name');

        $node->info('Your choosen name link to the email.');

        return $node;
    }

    private function getTemplateNode()
    {
        $node = new ScalarNodeDefinition('template');

        $node->info('The template used by the mailer in order the send the reset link.');

        return $node;
    }

}
