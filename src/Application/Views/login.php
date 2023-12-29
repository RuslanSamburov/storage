<?php

use Storage\Storage\Core\Helpers;

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once Helpers::get_fragment_path('css'); ?>
    <link rel="stylesheet" href="css/login.css">
    <title>Авторизация</title>
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
                    <?php Helpers::show_errors('email', $form); ?>
                </div>
                <div>
                    <label for="inputPassword" class="col-form-label">Пароль</label>
                    <div>
                        <input id="inputPassword" type="password" class="form-control" name="password" value="<?= $form['password']; ?>">
                    </div>
                    <?php Helpers::show_errors('password', $form); ?>
                </div>
                <div class="my-3">
                    <a href="/register">Регистрация</a>
                </div>
                <button type="submit" class="my-3 btn btn-primary">Войти</button>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>

</html>