<?php

namespace Solmer\Storage\Core;

use Solmer\Storage\Application\Models\{Users, Profiles};

use Solmer\Storage\Core\Account;

class BaseController
{
    private $currentUser = null;
    private $currentProfile = null;

    private static function renderView(string $template, array $context): void
    {
        extract($context);
        require_once path_app('Application/Views/' . $template . '.php'); ;
    }

    public function __construct() 
    {
        if(session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        $userId = Account::getCurrentUser();
        if ($userId) {
            $users = new Users();
            $user = $users->get($userId);
            if(!$user) {
                Account::logout();
                return;
            }
            $profiles = new Profiles();
            $profile = $profiles->get($userId, 'user_id');
            if(!$profile) {
                $profiles->insert(['user_id' => $userId]);
                $profile = $profiles->get($userId, 'user_id');
            }
            $this->currentUser = $user;
            $this->currentProfile = $profile;
        }
    }
    
    protected function contextAppend(array &$context): void
    {
        $context['__current_user'] = $this->currentUser;
        $context['__current_profile'] = $this->currentProfile;
    }

    protected function render(string $template, array $context = []): void
    {
        $this->contextAppend($context);

        self::renderView($template, $context);
    }
}
