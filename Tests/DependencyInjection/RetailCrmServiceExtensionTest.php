<?php

namespace RetailCrm\ServiceBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use RetailCrm\ServiceBundle\ArgumentResolver\CallbackValueResolver;
use RetailCrm\ServiceBundle\ArgumentResolver\ClientValueResolver;
use RetailCrm\ServiceBundle\DependencyInjection\RetailCrmServiceExtension;
use RetailCrm\ServiceBundle\Response\ErrorJsonResponseFactory;
use RetailCrm\ServiceBundle\Security\CallbackClientAuthenticator;
use RetailCrm\ServiceBundle\Security\FrontApiClientAuthenticator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\EnvPlaceholderParameterBag;

/**
 * Class RetailCrmServiceExtensionTest
 *
 * @package RetailCrm\ServiceBundle\Tests\DependencyInjection
 */
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
        static::assertTrue($this->container->hasDefinition(CallbackClientAuthenticator::class));
        static::assertTrue($this->container->hasDefinition(FrontApiClientAuthenticator::class));
    }
}
