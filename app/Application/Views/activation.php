<?php

use Solmer\Storage\Core\Texts;

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once Texts::getFragmentPath('css'); ?>
    <link rel="stylesheet" href="css/activation.css">
    <title>Ативация аккаунта</title>
</head>

<body>
    <div id="root">
        <div class="App">
            <div>
                Проверьте почту, и перейдите по ссылке, для активации аккаунта
            </div>
            <div>
                <form method="post">
                    <input type="hidden" name="_method" value="PUT">
                    <button type="submit" class="my-3 btn btn-primary">Отправить заново</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>