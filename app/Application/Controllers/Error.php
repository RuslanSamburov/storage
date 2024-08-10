<?php

namespace Solmer\Storage\Application\Controllers;

use Solmer\Storage\Core\BaseController;

class Error extends BaseController
{
    public function page404(object $e): void
    {
        $ctx = [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
        ];
        $this->render('error', $ctx);
    }

    public function page500(): void
    {
        $ctx = [
            'message' => 'Ошибка на стороне сервера',
            'code' => 500,
        ];
        $this->render('error', $ctx);
    }
}
