<?php

namespace Solmer\Storage\Core;

use Solmer\Storage\Application\Models\{
    Profiles,
    Storages,
    TypesStorage,
};

class Storage
{
    public static function getUserStorage(int $id = null): string
    {
        global $basePath;
        $id = $id ? $id : Account::getCurrentUser();
        return path(env('STORAGES') . '/' . $id . '/');
    }

    public static function getUserFiles(array &$result, int $id = null, string $next = null): void
    {
        $folder = self::getUserStorage($id) . $next;
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
                self::getUserFiles($result, $id, $way);
            }
        }
    }

    public static function getStorageFiles(): array
    {
        $result = [];
        $storages = new Storages();
        $files = $storages->getAll(where: 'user_id = ?', params: [Account::getCurrentUser()]);
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

        self::getUserFiles($searchResult);

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

    public static function getStorage(
        string $value,
        string $keyField = 'id',
        string $fields = '*',
        array $links = [],
    ): array|bool {
        $storages = new Storages();
        return $storages->get($value, $keyField, $fields, $links);
    }

    public static function fileExists(string $file): string|false
    {
        $file = self::getUserStorage() . $file;
        return file_exists($file) ? $file : false;
    }

    public static function generateName(): string
    {
        $i = 2;
        do {
            $i++;
            $name = Texts::generateSymbols($i);
        } while (self::getStorage($name, 'name'));
        return $name;
    }

    public static function formatByte(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public static function getFilename(string $file): string
    {
        $info = pathinfo($file);
        $filename = $info['filename'];
        $extension = isset($info['extension']) ? '.' . $info['extension'] : '';

        $storage = self::getUserStorage();
        $postfix = '';

        $i = 0;
        do {
            $i++;
            $name = $filename . $postfix . $extension;
            $postfix = '_' . $i;
        } while (file_exists($storage . $name));

        return $name;
    }

    public static function upload(array $file, string $name): void
    {
        $storage = self::getUserStorage();
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
        $file = self::getUserStorage($id) . $file;
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
