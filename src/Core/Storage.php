<?php

namespace Storage\Storage\Core;

use Storage\Storage\Core\Helpers;
use Storage\Storage\Application\Models\Profiles;
use Storage\Storage\Application\Models\Storages;
use Storage\Storage\Application\Models\TypesStorage;
use Storage\Storage\Application\Settings;

class Storage
{
    public static function getDirStorage(int $id = null): string
    {
        global $base_path;
        $id = $id ? $id : Account::getCurrentUser();
        return $base_path . Settings::STORAGES . '/' . $id . '/';
    }

    public static function getFiles(string $folder, array &$result): void // Функция с модуля 7.5 :)
    {
        if (!is_dir($folder)) {
            return;
        }
        if ($folder[-1] == '/' || $folder[-1] == '\\') {
            $folder = mb_substr($folder, 0, mb_strlen($folder) - 1);
        }
        $files = array_diff(scandir($folder), ['.', '..']);
        foreach ($files as $file) {
            $way = $folder . DIRECTORY_SEPARATOR . $file;
            if (!is_dir($way)) {
                $result[] = $way;
            } else {
                self::getFiles($way, $result);
            }
        }
    }

    public static function getStorageFiles(): array
    {
        $result = [];
        $storages = new Storages();
        $files = $storages->get_all(where: 'user_id = ?', params: [Account::getCurrentUser()]);
        foreach ($files as $file) {
            $filename = $file['file'];
            $result[$filename] = [
                ...$file,
                'size' => self::getFileSize($filename),
            ];
        }

        return $result;
    }

    public static function getFileSize(string $filename): int|false
    {
        $file = self::fileExists($filename);
        return $file ? filesize($file) : false;
    }

    public static function getMySize(): int
    {
        $profiles = new Profiles();
        $profile = $profiles->get(Account::getCurrentUser(), 'user_id', 'bytes.byte as byte', ['bytes']);
        return $profile['byte'];
    }

    public static function getFilesSize(): int
    {
        $searchResult = [];

        self::getFiles(self::getDirStorage(), $searchResult);

        $size = 0;

        foreach ($searchResult as $e) {
            $size += filesize($e);
        }

        return $size;
    }

    public static function getMyFreeSize(): int
    {
        return self::getMySize() - self::getFilesSize();
    }

    public static function getStatusFile(string $file): array|false
    {
        $storage = self::getStorage($file, 'name', 'type');
        if (!$storage) {
            return false;
        }
        $typesStorage = new TypesStorage();
        $type = $typesStorage->get($storage['type']);
        return $type;
    }

    public static function getStorage(string $value, string $key_field = 'id', string $fields = '*', array $links = []): array|bool
    {
        $storages = new Storages();
        return $storages->get($value, $key_field, $fields, $links);
    }

    public static function fileExists(string $file): string|false
    {
        $file = self::getDirStorage() . $file;
        return file_exists($file) ? $file : false;
    }

    public static function generateName(): string
    {
        $i = 2;
        do {
            $i++;
            $name = Helpers::generateSymbols($i);
        } while (self::getStorage($name, 'name')); // Всегда задавался вопросом, где можно найти приминение do while
        return $name;
    }

    public static function formatByte(int $bytes): string // Создано Chat-GPT (Я бы не додумался)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public static function get_filename(string $file): string
    {
        $info = pathinfo($file);
        $filename = $info['filename'];
        $extension = isset($info['extension']) ? '.' . $info['extension'] : '';

        $storage = self::getDirStorage();
        $postfix = '';

        $i = 0;
        do {
            $i++;
            $name = $filename . $postfix . $extension;
            $postfix = '_' . $i;
        } while (file_exists($storage . $name)); // Как я запомнил do while? Вначале сделать, потом проверить

        return $name;
    }

    public static function upload(array $file, string $name): void
    {
        $storage = self::getDirStorage();
        if (!file_exists($storage)) {
            mkdir($storage, 0, true);
        }
        move_uploaded_file($file['tmp_name'], $storage . '/' . $name);
    }

    public static function delete(string $filename): void
    {
        $storage = self::fileExists($filename);
        if ($storage) {
            unlink($storage);
        }
    }

    public static function download(int $id, string $file): void
    {
        $file = self::getDirStorage($id) . $file;
        if (!file_exists($file)) {
            Response::redirect('/');
            return;
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));
    }
}
