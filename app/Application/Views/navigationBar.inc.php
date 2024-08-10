<?php

use Solmer\Storage\Core\{Math, Storage};

?>

<div class="navigationBar">
    <img src="media/logo.svg" alt="my files" class="logoNavigation">
    <nav class="navigationList">
        <a href="/" class="navLink">Мои файлы</a>
        <a href="/password" class="navLink">Сменить пароль</a>
        <a href="/logout" class="navLink">
            <img src="media/exit.svg" alt="exit" class="icon"> Выйти
        </a>
    </nav>
    <div class="progressBar">
        <div class="progress">
            <div role="progressbar" class="progress-bar" style="width: <?= Math::getProcent(Storage::getFilesSize(), Storage::getMySize()); ?>%;"></div>
        </div>
        <p>Свободно: <?= Storage::formatByte(Storage::getMyFreeSize()); ?></p>
    </div>
</div>