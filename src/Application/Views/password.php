<?php

use Storage\Storage\Core\Helpers;

?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once Helpers::get_fragment_path('css'); ?>
    <title>Storage</title>
</head>

<body>
    <div id="root">
        <div class="App">
            <div class="main">
                <?php require_once Helpers::get_fragment_path('navigationBar'); ?>
                <div class="content">
                    <h1>Сменить пароль</h1>
                    <form class="passwordForm" method="post">
                        <input type="hidden" name="_method" value="PUT">
                        <div class="mb-3">
                            <label class="form-label" for="formBasicOldPassword">Текущий пароль</label>
                            <input type="password" id="formBasicOldPassword" class="form-control" name="old_password" value="<?= $form['old_password']; ?>">
                            <?php Helpers::show_errors('old_password', $form); ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="formBasicNewPassword">Новый пароль</label>
                            <input type="password" id="formBasicNewPassword" class="form-control" name="password" value="<?= $form['password']; ?>">
                            <?php Helpers::show_errors('password', $form); ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="formBasicRepeatNewPassword">Повторите пароль</label>
                            <input type="password" id="formBasicRepeatNewPassword" class="form-control" name="password2" value="<?= $form['password2']; ?>">
                            <?php Helpers::show_errors('password2', $form); ?>
                        </div>
                        <?php Helpers::show_results('message', $form); ?>
                        <button class="btn btn-primary">Сменить пароль</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>