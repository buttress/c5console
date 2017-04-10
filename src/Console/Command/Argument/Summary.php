<?php

namespace Buttress\Concrete\Console\Command\Argument;

class Summary extends \League\CLImate\Argument\Summary
{

    /**
     * Output the full summary for the program
     */
    public function output()
    {

        if ($this->description) {
            $this->climate->out($this->description)->br();
        }

        // Print the usage statement with the arguments without a prefix at the end.
        $this->climate->out($this->summarize());

        // Print argument details.
        foreach (['required', 'optional'] as $type) {
            $this->outputArguments($this->filter->{$type}(), $type);
        }
    }

    /**
     * Output the short summary
     */
    public function summarize()
    {
        return "<yellow>Usage</yellow>: <dim>c5</dim> <bold>{$this->command}</bold> <dim>" . $this->short($this->getOrderedArguments()) . '</dim>';
    }

}
