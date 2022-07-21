<?php

namespace RetailCrm\ServiceBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use RetailCrm\ServiceBundle\ArgumentResolver\CallbackValueResolver;
use RetailCrm\ServiceBundle\ArgumentResolver\ClientValueResolver;
use RetailCrm\ServiceBundle\DependencyInjection\RetailCrmServiceExtension;
use RetailCrm\ServiceBundle\Response\ErrorJsonResponseFactory;
use RetailCrm\ServiceBundle\Security\ApiClientAuthenticator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\EnvPlaceholderParameterBag;

class RetailCrmServiceExtensionTest extends TestCase
{
    private $container;

    protected function setUp(): void
    {
        $container = new ContainerBuilder(new EnvPlaceholderParameterBag());
        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);

        $extension = new RetailCrmServiceExtension();
        $extension->load(
            [
                [
                    'request_schema' => [
                        'callback' => [],
                        'client' => []
                    ],
                    'messenger' => [
                        'message_handler' => 'simple_console_runner'
                    ]
                ]
            ],
            $container
        );

        $container->compile();

        $this->container = $container;
    }

    public function testLoad(): void
    {
        static::assertTrue($this->container->hasParameter('retail_crm_service.request_schema.callback.supports'));
        static::assertTrue($this->container->hasParameter('retail_crm_service.request_schema.callback.serializer'));
        static::assertTrue($this->container->hasParameter('retail_crm_service.request_schema.client.supports'));
        static::assertTrue($this->container->hasParameter('retail_crm_service.request_schema.client.serializer'));
        static::assertTrue($this->container->hasDefinition(CallbackValueResolver::class));
        static::assertTrue($this->container->hasDefinition(ClientValueResolver::class));
        static::assertTrue($this->container->hasDefinition(ErrorJsonResponseFactory::class));
        static::assertTrue($this->container->hasDefinition(ApiClientAuthenticator::class));
    }
}
