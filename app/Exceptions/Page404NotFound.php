<?php

namespace Solmer\Storage\Exceptions;

class Page404NotFound extends \Exception
{
    public function __construct()
    {
        parent::__construct('Страница не найдена', 404);
    }
}
