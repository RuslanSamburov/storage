<?php

use Solmer\Storage\Core\{Texts, Shows};

?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once Texts::getFragmentPath('css'); ?>
    <title>Storage</title>
</head>

<body>
    <div id="root">
        <div class="App">
            <div class="main">
                <?php require_once Texts::getFragmentPath('navigationBar'); ?>
                <div class="content">
                    <h1>Сменить пароль</h1>
                    <form class="passwordForm" method="post">
                        <input type="hidden" name="_method" value="PUT">
                        <div class="mb-3">
                            <label class="form-label" for="formBasicOldPassword">Текущий пароль</label>
                            <input type="password" id="formBasicOldPassword" class="form-control" name="old_password" value="<?= $form['old_password']; ?>">
                            <?php Shows::showErrors('old_password', $form); ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="formBasicNewPassword">Новый пароль</label>
                            <input type="password" id="formBasicNewPassword" class="form-control" name="password" value="<?= $form['password']; ?>">
                            <?php Shows::showErrors('password', $form); ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="formBasicRepeatNewPassword">Повторите пароль</label>
                            <input type="password" id="formBasicRepeatNewPassword" class="form-control" name="password2" value="<?= $form['password2']; ?>">
                            <?php Shows::showErrors('password2', $form); ?>
                        </div>
                        <?php Shows::showResults('message', $form); ?>
                        <button class="btn btn-primary">Сменить пароль</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>