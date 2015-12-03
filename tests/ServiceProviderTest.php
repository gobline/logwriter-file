<?php

/*
 * Gobline
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Pimple\Container;
use Gobline\Logger\Writer\Provider\Pimple\FileLogWriterServiceProvider;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class ServiceProviderTest extends PHPUnit_Framework_TestCase
{
    public function testServiceProvider()
    {
        $container = new Container();

        $container['logwriter.file.dir'] = '.';
        $container->register(new FileLogWriterServiceProvider());
        $this->assertInstanceOf('Gobline\Logger\Writer\FileLogWriter', $container['logwriter.file']);
    }
}
