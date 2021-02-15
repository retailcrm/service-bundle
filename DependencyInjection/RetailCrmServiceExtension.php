<?php

namespace RetailCrm\ServiceBundle\DependencyInjection;

use RetailCrm\ServiceBundle\ArgumentResolver\CallbackValueResolver;
use RetailCrm\ServiceBundle\ArgumentResolver\ClientValueResolver;
use RetailCrm\ServiceBundle\Response\ErrorJsonResponseFactory;
use RetailCrm\ServiceBundle\Security\CallbackClientAuthenticator;
use RetailCrm\ServiceBundle\Security\FrontApiClientAuthenticator;
use RetailCrm\ServiceBundle\Serializer\JMSSerializerAdapter;
use RetailCrm\ServiceBundle\Serializer\SymfonySerializerAdapter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RetailCrmServiceExtension
 *
 * @package RetailCrm\ServiceBundle\DependencyInjection
 */
class RetailCrmServiceExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'retail_crm_service.request_schema.callback.supports',
            $config['request_schema']['callback']['supports']
        );

        $container->setParameter(
            'retail_crm_service.request_schema.client.supports',
            $config['request_schema']['client']['supports']
        );

        $container->setParameter(
            'retail_crm_service.request_schema.callback.serializer',
            $config['request_schema']['callback']['serializer']
        );

        $container->setParameter(
            'retail_crm_service.request_schema.client.serializer',
            $config['request_schema']['client']['serializer']
        );

        $container
            ->register(SymfonySerializerAdapter::class)
            ->setAutowired(true);
        $container->setAlias('retail_crm_service.symfony_serializer.adapter', SymfonySerializerAdapter::class);

        $container
            ->register(JMSSerializerAdapter::class)
            ->setAutowired(true);
        $container->setAlias('retail_crm_service.jms_serializer.adapter', JMSSerializerAdapter::class);

        $container
            ->register(CallbackValueResolver::class)
            ->setArguments([
                new Reference($container->getParameter('retail_crm_service.request_schema.callback.serializer')),
                new Reference('validator'),
                $container->getParameter('retail_crm_service.request_schema.callback.supports')
            ])
            ->addTag('controller.argument_value_resolver', ['priority' => 50])
            ->setAutowired(true);

        $container
            ->register(ClientValueResolver::class)
            ->setArguments([
                new Reference($container->getParameter('retail_crm_service.request_schema.client.serializer')),
                new Reference('validator'),
                $container->getParameter('retail_crm_service.request_schema.client.supports')
            ])
            ->addTag('controller.argument_value_resolver', ['priority' => 50])
            ->setAutowired(true);

        $container
            ->register(ErrorJsonResponseFactory::class)
            ->setAutowired(true);

        $container
            ->register(CallbackClientAuthenticator::class)
            ->setAutowired(true);

        $container
            ->register(FrontApiClientAuthenticator::class)
            ->setAutowired(true);
    }
}
