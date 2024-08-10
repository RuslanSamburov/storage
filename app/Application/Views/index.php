<?php

use Solmer\Storage\Core\{Shows, Storage, Texts};

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
                    <h1>Мои файлы</h1>
                    <div class="rendered-react-keyed-file-browser">
                        <div class="rendered-file-browser">
                            <div class="action-bar">
                                <div class="item-actions">&nbsp;</div>
                            </div>
                            <div class="files">
                                <table>
                                    <thead class="folder">
                                        <th>Файл</th>
                                        <th>Ссылка</th>
                                        <th>Размер</th>
                                        <th>Управление</th>
                                        <th>Приватность</th>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($files as $filename => $info) { ?>
                                        <tr class="folder" draggable="true">
                                            <td class="name">
                                                <div style="padding-left: 0;">
                                                    <div>
                                                        <div>
                                                            <a href="/download/<?= urlencode($info['name']); ?>">
                                                                <i class="folder" aria-hidden="true"></i>
                                                                <?= $filename; ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <input readonly type="text" value="http://<?= $_SERVER['SERVER_NAME'] ?>/download/<?= $info['name'] ?>">
                                            </td>
                                            <td>
                                                <?= Storage::formatByte($info['size']); ?>
                                            </td>
                                            <td>
                                                <form class="d-inline" method="post">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="filename" value="<?= $info['name']; ?>">
                                                    <input type="submit" value="Удалить">
                                                </form>
                                            </td>
                                            <td>
                                                <form class="d-inline" method="post">
                                                    <input type="hidden" name="_method" value="PUT">
                                                    <input type="hidden" name="filename" value="<?= $info['name']; ?>">
                                                    <input type="submit" value="<?= (Storage::getStatusFile($info['name']))['string']; ?>">
                                                </form>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="fileLoader">
                        <div class="fileInput">
                            <p class="fileInputLabel" id="fileupload">Загрузить файл</p>
                        </div>
                    </div>
                    <?php Shows::showErrors('file', $file); ?>
                    <?php Shows::showErrors('filename', $file); ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/myfiles.js"></script>
</body>

</html>