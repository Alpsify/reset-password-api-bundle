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
            ->end();

        return $treeBuilder;
    }

    public function getLifeTime()
    {
        $node = new IntegerNodeDefinition('lifetime');

        $node
            ->defaultValue(3600)
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

        return $node;
    }

    private function getHashAlgo()
    {
        $node = new ScalarNodeDefinition('hash_algo');

        return $node;
    }
}
