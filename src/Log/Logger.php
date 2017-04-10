<?php

namespace Buttress\Concrete\Log;

use League\CLImate\CLImate;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

/**
 * A PSR3 logger that pushes entries out to CLImate
 */
class Logger implements LoggerInterface
{

    use LoggerTrait;

    /** @var bool Debug mode */
    public $debug = false;

    /** @var \League\CLImate\CLImate */
    protected $cli;

    public function __construct(CLImate $climate)
    {
        $this->cli = $climate;
    }

    /**
     * Handle logging different log levels
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        // Handle PSR-3 interpolation
        $message = $this->interpolate($message, $context);

        switch ($level) {
            case LogLevel::NOTICE:
            case LogLevel::INFO:
            case LogLevel::WARNING:
            case LogLevel::ALERT:
                return $this->handleInfo($level, $message, $context);
            case LogLevel::DEBUG:
                return $this->handleDebug($level, $message, $context);
            default:
                return $this->handleError($level, $message, $context);
        }
    }

    /**
     * Handle info types
     *
     * @param $level
     * @param $message
     * @param array $context
     */
    private function handleInfo($level, $message, array $context)
    {
        $this->cli->output(sprintf(' - [%s] %s ', strtoupper($level), $message));
    }

    /**
     * Handle debub types
     *
     * @param $level
     * @param $message
     * @param array $context
     */
    private function handleDebug($level, $message, array $context)
    {
        if ($this->debug) {
            $this->cli->output(sprintf(' ~ [%s] %s ', strtoupper($level), $message));;
        }
    }

    /**
     * Handle Errors
     *
     * @param $level
     * @param $message
     * @param array $context
     */
    private function handleError($level, $message, array $context)
    {
        $this->cli->error(sprintf(' ! [%s] %s ', strtoupper($level), $message));;
    }

    /**
     * Interpolate context into a message.
     * This is copied directly from PSR-3 documentation
     *
     * @param $message
     * @param array $context
     * @return string
     */
    public function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            // check that the value can be casted to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

}
