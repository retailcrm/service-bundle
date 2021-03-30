<?php

namespace RetailCrm\ServiceBundle\Tests\Fixtures\App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Kernel extends \Symfony\Component\HttpKernel\Kernel
{
    use MicroKernelTrait;

    public function registerBundles()
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle()
        ];
    }

    protected function configureContainer(ContainerBuilder $container/*, LoaderInterface $loader*/): void
    {
        $container
            ->register(TestCommand::class, TestCommand::class)
            ->addTag('console.command', ['command' => TestCommand::getDefaultName()])
        ;

        $container->setParameter('kernel.project_dir', __DIR__ . '/..');
    }

//    public function registerContainerConfiguration(LoaderInterface $loader)
//    {
//    }
}
