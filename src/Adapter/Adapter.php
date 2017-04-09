<?php

namespace Buttress\Concrete\Adapter;

/**
 * Adapters are used to connect the CLI tool with an existing concrete5 installation
 */
interface Adapter
{

    /**
     * Attach to a concrete5 site
     *
     * @return void
     */
    public function attach();

}
