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
 * @category  Console
 * @package   Jnjxp\Console
 * @author    Jake Johns <jake@jakejohns.net>
 * @copyright 2017 Jake Johns
 * @license   http://jnj.mit-license.org/2017 MIT License
 * @link      http://jakejohns.net
 */


namespace Jnjxp\Console;

/**
 * Console
 *
 * @category Console
 * @package  Jnjxp\Console
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://jnj.mit-license.org/ MIT License
 * @link     http://jakejohns.net
 */
class Console
{
    /**
     * Available commands
     *
     * Key is command name, value is callable spec
     *
     * @var array
     *
     * @access protected
     */
    protected $commands = [];

    /**
     * Resolves a spec to a calable
     *
     * @var callable
     *
     * @access protected
     */
    protected $resolver;

    /**
     * Name of executable
     *
     * @var string
     *
     * @access protected
     */
    protected $executable;

    /**
     * Name of command
     *
     * @var string
     *
     * @access protected
     */
    protected $command;

    /**
     * Arguments to pass to command
     *
     * @var array
     *
     * @access protected
     */
    protected $args = [];

    /**
     * Standard Error
     *
     * @var resource
     *
     * @access protected
     */
    protected $stderr = STDERR;


    /**
     * Constructor
     *
     * Create a new Console application
     *
     * @param array    $commands available commands
     * @param callable $resolver optional resolver
     *
     * @access public
     */
    public function __construct(
        array $commands,
        callable $resolver = null
    ) {
        $this->commands = $commands;

        if (null === $resolver) {
            $resolver = new SimpleResolver;
        }

        $this->resolver = $resolver;
    }

    /**
     * Invoke a command
     *
     * Invoke a command by name with argv
     *
     * @param array $args should be argv probably
     *
     * @return int
     *
     * @access public
     */
    public function __invoke(array $args)
    {
        $this->input($args);

        if (! $this->isValid()) {
            $this->usage();
            return Status::USAGE;
        }

        return $this->execute();
    }

    /**
     * Initialize input
     *
     * Setup state based on argv
     *
     * @param array $args input arguments
     *
     * @return null
     *
     * @access protected
     */
    protected function input(array $args)
    {
        $this->executable = array_shift($args);
        $this->command    = array_shift($args);
        $this->args       = (array) $args;
    }

    /**
     * Is command call valid?
     *
     * Check if input was valid
     *
     * @return bool
     *
     * @access protected
     */
    protected function isValid()
    {
        return (null !== $this->command)
            && array_key_exists($this->command, $this->commands);
    }

    /**
     * Actually execute command
     *
     * @return int
     *
     * @access protected
     */
    protected function execute()
    {
        try {
            $command = $this->getCommand();
            $status  = $this->callCommand($command);
            return is_int($status) ? $status : Status::SUCCESS;

        } catch (\Exception $e) {
            $this->fail($e);
            return Status::FAILURE;
        }
    }

    /**
     * Get Command
     *
     * @return callable
     *
     * @access protected
     */
    protected function getCommand()
    {
        $spec = $this->commands[$this->command];
        return $this->resolve($spec);
    }

    /**
     * Call Command
     *
     * @param callable $command to call
     *
     * @return mixed
     *
     * @access protected
     */
    protected function callCommand(callable $command)
    {
        return call_user_func_array($command, $this->args);
    }

    /**
     * Print fail message
     *
     * @param \Exception $exception thrown exception
     *
     * @return null
     *
     * @access protected
     */
    protected function fail(\Exception $exception)
    {
        $error = sprintf(
            '%s: %s',
            get_class($exception),
            $exception->getMessage()
        );
        $this->errorLine($error);
    }

    /**
     * Resolve spec
     *
     * @param mixed $spec spec to resolve to callable
     *
     * @return callable
     *
     * @access protected
     */
    protected function resolve($spec)
    {
        if (! $this->resolver) {
            return $spec;
        }
        return call_user_func($this->resolver, $spec);
    }

    /**
     * Print usage
     *
     * @return null
     *
     * @access protected
     */
    protected function usage()
    {
        $this->errorLine('Usage:');
        foreach (array_keys($this->commands) as $command) {
            $this->errorLine($this->executable . ' ' . $command);
        }
    }

    /**
     * Write error line to stderr
     *
     * @param string $line to write to stderr
     *
     * @return null
     *
     * @access protected
     */
    protected function errorLine($line)
    {
        fwrite($this->stderr, $line . PHP_EOL);
    }

    /**
     * Set stderr
     *
     * @param resource $stderr stderr resource
     *
     * @return null
     *
     * @access public
     */
    public function setStderr($stderr)
    {
        $this->stderr = $stderr;
    }
}
