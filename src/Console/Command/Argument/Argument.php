<?php

namespace Buttress\Concrete\Console\Command\Argument;

class Argument extends \League\CLImate\Argument\Argument
{

    protected $hidden;

    /**
     * @return bool
     */
    public function hidden()
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = (bool) $hidden;
    }

    /**
     * Get argument params based on settable properties
     *
     * @param array $params
     *
     * @return array
     */
    protected static function getSettableArgumentParams(array $params)
    {
        $allowed = [
            'prefix',
            'longPrefix',
            'description',
            'required',
            'noValue',
            'castTo',
            'defaultValue',
            'hidden',
        ];

        return array_intersect_key($params, array_flip($allowed));
    }

    public static function createFromArray($name, array $params)
    {
        $argument = new static($name);
        $params   = self::getSettableArgumentParams($params);

        foreach ($params as $key => $value) {
            $method = 'set' . ucwords($key);
            $argument->{$method}($value);
        }

        if ($argument->defaultValue()) {
            $argument->setValue($argument->defaultValue());
        }

        return $argument;
    }

}
