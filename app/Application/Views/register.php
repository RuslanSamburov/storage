<?php

use Solmer\Storage\Core\{Shows, Texts};

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once Texts::getFragmentPath('css'); ?>
    <link rel="stylesheet" href="css/register.css">
    <title>Регистрация</title>
</head>

<body>
    <div id="root">
        <div class="App">
            <form method="post">
                <div>
                    <img src="media/logo.svg" alt="my files">
                </div>
                <div>
                    <label for="inputEmail" class="col-form-label">Почта</label>
                    <div>
                        <input id="inputEmail" type="email" class="form-control" name="email" value="<?= $form['email']; ?>">
                    </div>
                    <?php Shows::showErrors('email', $form); ?>
                </div>
                <div>
                    <label for="inputPassword" class="col-form-label">Пароль</label>
                    <div>
                        <input id="inputPassword" type="password" class="form-control" name="password" value="<?= $form['password']; ?>">
                    </div>
                    <?php Shows::showErrors('password', $form); ?>
                </div>
                <div>
                    <label for="inputPassword2" class="col-form-label">Повторите пароль</label>
                    <div>
                        <input id="inputPassword2" type="password" class="form-control" name="password2" value="<?= $form['password2']; ?>">
                    </div>
                    <?php Shows::showErrors('password2', $form); ?>
                </div>
                <div class="my-3">
                    <a href="/login">Авторизация</a>
                </div>
                <button type="submit" class="my-3 btn btn-primary">Зарегистрироваться</button>
            </form>
        </div>
    </div>
</body>

</html>