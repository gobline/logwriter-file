<?php

/*
 * Gobline
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gobline\Logger\Writer;

use Psr\Log\AbstractLogger;

/**
 * Writes log information to a file.
 *
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class FileLogWriter extends AbstractLogger
{
    use GetStackTraceTrait;

    const ROLL_HOURLY = 'Y-m-d-H';
    const ROLL_DAILY = 'Y-m-d';
    const ROLL_WEEKLY = 'Y-m-W';
    const ROLL_MONTHLY = 'Y-m';

    private $filename;
    private $dir;
    private $rollTime;
    private $rollSize;

    /**
     * @param string $dir
     * @param string $filename
     * @param string $rollTime
     * @param string $rollSize
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($dir, $filename = 'logfile', $rollTime = self::ROLL_DAILY, $rollSize = '10MB')
    {
        $dir = (string) $dir;
        if ($dir === '') {
            throw new \InvalidArgumentException('$dir cannot be empty');
        }

        $filename = (string) $filename;
        if ($filename === '') {
            throw new \InvalidArgumentException('$filename cannot be empty');
        }

        $this->dir = $this->trailingSlashIt($dir);

        $this->filename = $filename;

        $this->rollTime = $rollTime;

        if ($rollSize) {
            $this->rollSize = (string) (1 * $rollSize);

            if ($this->rollSize != $rollSize) {
                $size = preg_replace('/[0-9]/', '', $rollSize);
                switch (strtolower($size)) {
                    case 'kb':
                        $this->rollSize *= 1024;
                        break;
                    case 'mb':
                        $this->rollSize *= 1024 * 1024;
                        break;
                    case 'gb':
                        $this->rollSize *= 1024 * 1024 * 1024;
                        break;
                }
            }
        }
    }

    /**
     * @param string $level
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = [])
    {
        $path = $this->dir.$this->filename;

        if ($this->rollTime) {
            $path .= '-'.date($this->rollTime);
        }

        if ($this->rollSize) {
            $path = $this->checkSize($path);
        }

        $path .= '.log';

        $fileHandler = fopen($path, 'a');

        if ($fileHandler === false) {
            throw new \Exception('fopen() error on file: '.$path);
        }

        $message = '['.strtoupper($level).'] '.$message;

        if (isset($context['exception']) && $context['exception'] instanceof \Exception) {
            $message .= $this->getExceptionStackTrace($context['exception'], false);
        }

        fwrite($fileHandler, $message."\n");

        fclose($fileHandler);
    }

    /**
     * @param string $path
     * @param int    $nb
     *
     * @return string
     */
    private function checkSize($path, $nb = 0)
    {
        $pathToCheck = $path.(!$nb ? '' : '-'.$nb);

        if (!is_file($pathToCheck.'.log')) {
            return $pathToCheck;
        }

        clearstatcache(true, $pathToCheck.'.log');

        if (filesize($pathToCheck.'.log') >= $this->rollSize) {
            return $this->checkSize($path, ++$nb);
        }

        return $pathToCheck;
    }

    /**
     * @param string $s
     *
     * @return string
     */
    private function trailingSlashIt($s)
    {
        return strlen($s) <= 0 ? '/' : (substr($s, -1) !== '/' ? $s.'/' : $s);
    }
}
