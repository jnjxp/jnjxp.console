<?php
/**
 * Jnjxp Console
 *
 * PHP version 5
 *
 * Copyright (C) 2017 Jake Johns
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 *
 * @category  Logger
 * @package   Jnjxp\Conxole
 * @author    Jake Johns <jake@jakejohns.net>
 * @copyright 2017 Jake Johns
 * @license   http://jnj.mit-license.org/2017 MIT License
 * @link      http://jakejohns.net
 */


namespace Jnjxp\Console;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * Stdlog
 *
 * A basic logger implementation that writes to standard output and error.
 * See: http://paul-m-jones.com/archives/6552
 *
 * @category Logger
 * @package  Jnjxp\Console
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://jnj.mit-license.org/ MIT License
 * @link     http://jakejohns.net
 *
 * @see AbstractLogger
 */
class Stdlog extends AbstractLogger
{
    /**
     * The stdout file handle.
     *
     * @var resource
     */
    protected $stdout;

    /**
     * The stderr file handle.
     *
     * @var resource
     */
    protected $stderr;

    /**
     * Level
     *
     * @var int
     *
     * @access protected
     */
    protected $level = 200; // INFO


    /**
     * Int Levels
     *
     * @var array
     *
     * @access protected
     */
    protected $intLevels = [
        LogLevel::DEBUG     => 100,
        LogLevel::INFO      => 200,
        LogLevel::NOTICE    => 250,
        LogLevel::WARNING   => 300,
        LogLevel::ERROR     => 400,
        LogLevel::CRITICAL  => 500,
        LogLevel::ALERT     => 550,
        LogLevel::EMERGENCY => 600
    ];

    /**
     * Write to stderr for these log levels.
     *
     * @var array
     */
    protected $stderrLevels = [
        LogLevel::EMERGENCY,
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::ERROR,
    ];

    /**
     * Constructor.
     *
     * @param resource $stdout Write to stdout on this handle.
     * @param resource $stderr Write to stderr on this handle.
     * @param string   $level  minimum level to log
     */
    public function __construct(
        $stdout = STDOUT,
        $stderr = STDERR,
        $level  = LogLevel::INFO
    ) {
        $this->stdout = $stdout;
        $this->stderr = $stderr;
        $this->setLevel($level);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level   record level
     * @param string $message record message
     * @param array  $context record context
     *
     * @return null
     */
    public function log($level, $message, array $context = [])
    {
        if (! $this->isHandling($level)) {
            return;
        }

        $replace = [];

        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        $message = strtr($message, $replace) . PHP_EOL;

        $handle = $this->stdout;

        if (in_array($level, $this->stderrLevels)) {
            $handle = $this->stderr;
        }

        fwrite($handle, $message);
    }

    /**
     * Set log level
     *
     * @param string $level above which to log
     *
     * @return null
     *
     * @access public
     */
    public function setLevel($level)
    {
        $this->level = $this->toIntLevel($level);
    }

    /**
     * Convert log level to integer
     *
     * @param string $level to convert
     *
     * @return int
     * @throws InvalidArgumentException if invalid level
     *
     * @access protected
     */
    protected function toIntLevel($level)
    {
        $level_key = strtolower($level);

        if (array_key_exists($level_key, $this->intLevels)) {
            return $this->intLevels[$level_key];
        }

        throw new \InvalidArgumentException("Invalid log level: $level");
    }

    /**
     * Are we logging this level?
     *
     * @param string $level of log message
     *
     * @return bool
     *
     * @access protected
     */
    protected function isHandling($level)
    {
        return $this->toIntLevel($level) >= $this->level;
    }
}
