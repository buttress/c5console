<?php

namespace Buttress\Concrete\Exception;

use League\CLImate\CLImate;

class ErrorHandler
{

    /** @var \League\CLImate\CLImate */
    private $cli;

    protected $verbose = false;

    public function __construct(CLImate $cli)
    {
        $this->cli = $cli;
    }

    /**
     * Register this error handler
     */
    public function register()
    {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
    }

    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;
    }

    /**
     * Handle an exception
     * @param \Exception $e
     */
    public function handleException(\Exception $e)
    {
        $cli = $this->cli;

        $cli->errorInline(sprintf('<bold>%s</bold> ', '<underline>Uncaught Exception</underline>'))
            ->errorInline(sprintf('on line <bold><yellow>%s</yellow></bold> ', $e->getLine()))
            ->error(sprintf('in <bold><yellow>%s</yellow></bold>', $e->getFile()));

        $cli->out("<dim><error>>></error></dim> " . $e->getMessage());

        $this->outputStack();
        exit(1);
    }

    /**
     * Handle an error
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array $context
     * @return bool
     */
    public function handleError($code, $message, $file, $line, $context)
    {
        if (!(error_reporting() & $code)) {
            return false;
        }

        $cli = $this->cli;

        switch ($code) {
            case E_ERROR:
            case E_USER_ERROR:
                $type = '<error>Fatal Error</error>';
                break;
            case E_RECOVERABLE_ERROR:
            case E_WARNING:
            case E_USER_WARNING:
                $type = '<yellow>Warning</yellow>';
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
                $type = '<green>Notice</green>';
                break;
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $type = '<blue>Deprecated Alert</blue>';
                break;
            default:
                $type = 'Unknown Error';
                break;
        }

        $cli->errorInline(sprintf('<bold>%s</bold> ', $type))
            ->errorInline(sprintf('on line <bold><yellow>%s</yellow></bold> ', $line))
            ->error(sprintf('in <bold><yellow>%s</yellow></bold>', $file));

        $cli->out("<dim><error>>></error></dim> " . $message);

        $this->outputStack();
        exit(1);
    }

    protected function outputStack()
    {
        if (!$this->verbose) {
            return;
        }

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $count = count($backtrace);

        $this->cli->br();
        foreach ($backtrace as $key => $item) {
            $this->cli->dim()->inline(str_pad($count-- . '. ', 4, ' ', STR_PAD_RIGHT));

            $string = '%s()';
            $data = [
                $item['function']
            ];

            if (isset($item['class'])) {
                list($class, $namespace) = explode('\\', strrev($item['class']), 2);
                $string = '%s\\<green>%s</green><dim>%s</dim>%s()';
                $data = [
                    strrev($namespace),
                    strrev($class),
                    $item['type'],
                    $item['function']
                ];
            }

            array_unshift($data, $string);
            $this->cli->inline(call_user_func_array('sprintf', $data));

            if (isset($item['file'])) {
                $this->cli->out(
                    sprintf(
                        '<dim> >> On line <yellow>%s</yellow> in <bold>%s</bold></dim>',
                        $item['line'],
                        $item['file']
                    ));
            } else {
                $this->cli->br();
            }
        }
    }

}
