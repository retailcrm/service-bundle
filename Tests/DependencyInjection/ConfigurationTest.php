<?php

namespace RetailCrm\ServiceBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use RetailCrm\ServiceBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public function testConfig(): void
    {
        $processor = new Processor();

        $configs = [
            [
                'request_schema' => [
                    'callback' => [
                        [
                            'type' => 'type',
                            'params' => ['param']
                        ]
                    ],
                    'client' => [
                        'type1',
                        'type2'
                    ]
                ]
            ]
        ];

        $config = $processor->processConfiguration(new Configuration(), $configs);

        static::assertArrayHasKey('request_schema', $config);
        static::assertArrayHasKey('callback', $config['request_schema']);
        static::assertArrayHasKey('client', $config['request_schema']);
    }
}
