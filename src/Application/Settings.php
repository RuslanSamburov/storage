<?php

namespace Storage\Storage\Application;

class Settings
{
    public const DB_HOST = 'localhost';
    public const DB_NAME = 'storage';
    public const DB_USER = 'root';
    public const DB_PASSWORD = '';

    public const SMTP_SERVER = 'smtp.timeweb.ru';
    public const SMTP_GMAIL = 'support@cu41258.tw1.ru';
    public const SMTP_PORT = 25;
    public const SMTP_PASSWORD = 'tester123';

    public const LENGTH_PASSWORD = 8;

    public const STORAGES = 'storages';

    public const TYPES_STORAGE_PRIVATE = 1;
    public const TYPES_STORAGE_PUBLIC = 2;
    public const TYPES_STORAGE_CLOSE = 3;
}
