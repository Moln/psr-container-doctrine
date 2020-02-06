<?php
/**
 * container-interop-doctrine
 *
 * @link      http://github.com/DASPRiD/container-interop-doctrine For the canonical source repository
 * @copyright 2016-2017 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace RoaveTest\PsrContainerDoctrine;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\ChainCache;
use Doctrine\Common\Cache\FilesystemCache;
use PHPUnit_Framework_TestCase;
use Psr\Container\ContainerInterface;
use Roave\PsrContainerDoctrine\AbstractFactory;
use Roave\PsrContainerDoctrine\CacheFactory;

/**
 * Class CacheFactoryTest
 *
 * @package ContainerInteropDoctrineTest
 * @coversDefaultClass \Roave\PsrContainerDoctrine\CacheFactory
 */
class CacheFactoryTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers ::__construct
     */
    public function testExtendsAbstractFactory()
    {
        $this->assertInstanceOf(AbstractFactory::class, new CacheFactory());
    }

    /**
     * @covers ::createWithConfig
     */
    public function testFileSystemCacheConstructor()
    {
        $config = [
            'doctrine' => [
                'cache' => [
                    'filesystem' => [
                        'class'     => FilesystemCache::class,
                        'directory' => 'test',
                    ],
                ],
            ],
        ];

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn($config);

        $factory = new CacheFactory('filesystem');
        $cacheInstance = $factory($container->reveal());

        $this->assertInstanceOf(FilesystemCache::class, $cacheInstance);
    }

    public function testCacheChainContainsInitializedProviders()
    {
        $config = [
            'doctrine' => [
                'cache' => [
                    'chain' => [
                        'class'     => ChainCache::class,
                        'providers' => ['array', 'array'],
                    ],
                ],
            ],
        ];

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn($config);
        $container->has(ArrayCache::class)->willReturn(false);

        $factory = new CacheFactory('chain');
        $cacheInstance = $factory($container->reveal());

        $this->assertInstanceOf(ChainCache::class, $cacheInstance);
        $this->assertAttributeCount(2, 'cacheProviders', $cacheInstance);
    }
}
