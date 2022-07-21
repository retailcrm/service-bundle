<?php

namespace RetailCrm\ServiceBundle\DependencyInjection;

use RetailCrm\ServiceBundle\ArgumentResolver\CallbackValueResolver;
use RetailCrm\ServiceBundle\ArgumentResolver\ClientValueResolver;
use RetailCrm\ServiceBundle\Messenger\MessageHandler;
use RetailCrm\ServiceBundle\Response\ErrorJsonResponseFactory;
use RetailCrm\ServiceBundle\Security\CallbackClientAuthenticator;
use RetailCrm\ServiceBundle\Security\FrontApiClientAuthenticator;
use RetailCrm\ServiceBundle\Serializer\JMSSerializerAdapter;
use RetailCrm\ServiceBundle\Serializer\SymfonySerializerAdapter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

class RetailCrmServiceExtension extends Extension
{
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

        $container->setParameter(
            'retail_crm_service.messenger.message_handler',
            $config['messenger']['message_handler']
        );

        if (isset($config['messenger']['process_timeout'])) {
            $container->setParameter(
                'retail_crm_service.messenger.process_timeout',
                $config['messenger']['process_timeout']
            );
        }

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
                new Reference('validator'),
                new Reference($container->getParameter('retail_crm_service.request_schema.callback.serializer')),
                $container->getParameter('retail_crm_service.request_schema.callback.supports')
            ])
            ->addTag('controller.argument_value_resolver', ['priority' => 50])
            ->setAutowired(true);

        $container
            ->register(ClientValueResolver::class)
            ->setArguments([
                new Reference('validator'),
                new Reference($container->getParameter('retail_crm_service.request_schema.client.serializer')),
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

        $container
            ->register(MessageHandler\SimpleConsoleRunner::class)
            ->setAutowired(true);
        $container->setAlias('simple_console_runner', MessageHandler\SimpleConsoleRunner::class);

        $timeout = $container->hasParameter('retail_crm_service.messenger.process_timeout')
            ? $container->getParameter('retail_crm_service.messenger.process_timeout')
            : null;

        $container
            ->register(MessageHandler\InNewProcessRunner::class)
            ->setArgument('$timeout', $timeout)
            ->setAutowired(true);
        $container->setAlias('in_new_process_runner', MessageHandler\InNewProcessRunner::class);

        $container
            ->register(MessageHandler::class)
            ->addTag('messenger.message_handler')
            ->setArguments([
                new Reference($container->getParameter('retail_crm_service.messenger.message_handler'))
            ])
            ->setAutowired(true);
    }
}
