<?php

namespace RetailCrm\ServiceBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use RetailCrm\ServiceBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class ConfigurationTest
 *
 * @package RetailCrm\ServiceBundle\Tests\DependencyInjection
 */
class ConfigurationTest extends TestCase
{
    public function testConfig(): void
    {
        $processor = new Processor();

        $configs = [
            [
                'request_schema' => [
                    'callback' => [
                        'supports' => [
                            [
                                'type' => 'type',
                                'params' => ['param']
                            ]
                        ]
                    ],
                    'client' => [
                        'supports' => [
                            'type1',
                            'type2'
                        ]
                    ]
                ],
                'messenger' => [
                    'message_handler' => 'simple_console_runner',
                    'process_timeout' => 60
                ]
            ]
        ];

        $config = $processor->processConfiguration(new Configuration(), $configs);

        static::assertArrayHasKey('request_schema', $config);
        static::assertArrayHasKey('callback', $config['request_schema']);
        static::assertArrayHasKey('client', $config['request_schema']);
        static::assertEquals(
            [
                'type' => 'type',
                'params' => ['param']
            ],
            $config['request_schema']['callback']['supports'][0]
        );
        static::assertEquals(
            [
                'type1',
                'type2'
            ],
            $config['request_schema']['client']['supports']
        );
    }

    public function testPartConfig(): void
    {
        $processor = new Processor();

        $configs = [
            [
                'request_schema' => [
                    'client' => [
                        'supports' => [
                            'type',
                        ]
                    ]
                ],
                'messenger' => [
                    'message_handler' => 'simple_console_runner'
                ]
            ]
        ];

        $config = $processor->processConfiguration(new Configuration(), $configs);

        static::assertArrayHasKey('client', $config['request_schema']);
        static::assertEquals(['type'], $config['request_schema']['client']['supports']);
    }
}
