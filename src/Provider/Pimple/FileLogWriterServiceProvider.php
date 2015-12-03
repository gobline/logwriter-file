<?php

/*
 * Gobline
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gobline\Logger\Writer\Provider\Pimple;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Gobline\Logger\Writer\FileLogWriter;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class FileLogWriterServiceProvider implements ServiceProviderInterface
{
    private $reference;

    public function __construct($reference = 'logwriter.file')
    {
        $this->reference = $reference;
    }

    public function register(Container $container)
    {
        $container[$this->reference.'.roll.time'] = FileLogWriter::ROLL_HOURLY;
        $container[$this->reference.'.roll.size'] = '10MB';
        $container[$this->reference.'.filename'] = 'logfile';

        $container[$this->reference] = function ($c) {
            if (empty($c[$this->reference.'.dir'])) {
                throw new \Exception('Directory not specified');
            }

            return new FileLogWriter(
                $c[$this->reference.'.dir'],
                $c[$this->reference.'.filename'],
                $c[$this->reference.'.roll.time'],
                $c[$this->reference.'.roll.size']
            );
        };
    }
}
