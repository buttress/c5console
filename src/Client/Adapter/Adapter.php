<?php

namespace Buttress\Concrete\Client\Adapter;

/**
 * Adapters are used to connect the CLI tool with an existing concrete5 installation
 */
interface Adapter
{

    /**
     * Attach to a concrete5 site
     *
     * @return \Buttress\Concrete\Client\Connection\Connection $connection
     */
    public function attach();

}
