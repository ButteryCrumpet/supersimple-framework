<?php

namespace SuperSimpleFramework;

interface ResolverInterface
{
    public function resolve($name, $args = array());
}