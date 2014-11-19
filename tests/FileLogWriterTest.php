<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Mendo\Logger\Writer\FileLogWriter;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class FileLogWriterTest extends PHPUnit_Framework_TestCase
{
    public function testLoggerFile()
    {
        $dir = __DIR__.'/resources/';
        $filename = 'logfile';

        $logger = new FileLogWriter($dir, $filename, FileLogWriter::ROLL_DAILY, '1kb');

        $path = $dir.$filename.'-'.date('Y-m-d').'.log';
        if (is_file($path)) {
            unlink($path);
        }

        $this->assertFalse(is_file($path));

        $logger->debug('hello');
        $logger->info('world');

        $this->assertTrue(is_file($path));

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        $this->assertSame(2, count($lines));
        $this->assertSame('[DEBUG] hello', $lines[0]);
        $this->assertSame('[INFO] world', $lines[1]);

        // write 1024 bytes
        $logger->debug('Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit ve');

        $logger->debug('This log should be written in another file (rolled on size)');

        $path = $dir.$filename.'-'.date('Y-m-d').'-1.log';

        $this->assertTrue(is_file($path));
    }
}
