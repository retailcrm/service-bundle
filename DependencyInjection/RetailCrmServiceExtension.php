<?php

namespace RetailCrm\ServiceBundle\DependencyInjection;

use RetailCrm\ServiceBundle\ArgumentResolver\CallbackValueResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

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
            'retail_crm_service.request_schema.callback',
            $config['request_schema']['callback']
        );

        $container->setParameter(
            'retail_crm_service.request_schema.client',
            $config['request_schema']['client']
        );

        $container
            ->register(CallbackValueResolver::class)
            ->setArgument('$requestSchema', '%retail_crm_service.request_schema.callback%')
            ->addTag('controller.argument_value_resolver', ['priority' => 50])
            ->setAutowired(true);
    }
}
