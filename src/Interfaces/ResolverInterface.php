<?php

namespace SuperSimpleFramework\Interfaces;

interface ResolverInterface
{
    public function resolve($name, $args = array());
}