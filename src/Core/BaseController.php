<?php

namespace Storage\Storage\Core;

use Storage\Storage\AbstractEntities\Controller;

use Storage\Storage\Application\Models\Users;
use Storage\Storage\Core\Account;

use Storage\Storage\Application\Models\Profiles;

class BaseController extends Controller
{
    private $current_user = null;
    private $current_profile = null;

    private static function renderView(string $template, array $context): void
    {
        global $base_path;
        extract($context);
        require_once $base_path . '/src/Application/Views/' . $template . '.php';
    }

    public function __construct() 
    {
        if(session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        $user_id = Account::getCurrentUser();
        if ($user_id) {
            $users = new Users();
            $user = $users->get($user_id);
            if(!$user) {
                Account::logout();
                return;
            }
            $profiles = new Profiles();
            $profile = $profiles->get($user_id, 'user_id');
            if(!$profile) {
                $profiles->insert(['user_id' => $user_id]);
                $profile = $profiles->get($user_id, 'user_id');
            }
            $this->current_user = $user;
            $this->current_profile = $profile;
        }
    }
    
    protected function context_append(array &$context): void
    {
        $context['__current_user'] = $this->current_user;
        $context['__current_profile'] = $this->current_profile;
    }

    protected function render(string $template, array $context = []): void
    {
        $this->context_append($context);

        self::renderView($template, $context);
    }
}
