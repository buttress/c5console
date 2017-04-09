<?php
namespace PHPSTORM_META;

// $app->make(SomeClass::class)
override(\Psr\Container\ContainerInterface::get(0), map(['' => '@',]));

// $app[SomeClass::class]
override(new \Psr\Container\ContainerInterface, map(['' => '@']));
