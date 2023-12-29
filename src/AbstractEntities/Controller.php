<?php

namespace Storage\Storage\AbstractEntities;

abstract class Controller
{
    abstract public function __construct();
    abstract protected function context_append(array &$context): void;

    abstract protected function render(string $template, array $context = []): void;
}
