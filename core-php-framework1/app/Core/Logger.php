<?php

namespace App\Core;

/**
 * Very small file-based logger writing to storage/logs/app.log.
 */
class Logger
{
    public static function error(string $message): void
    {
        self::write('ERROR', $message);
    }

    public static function info(string $message): void
    {
        self::write('INFO', $message);
    }

    protected static function write(string $level, string $message): void
    {
        $logFile   = STORAGE_PATH . '/logs/app.log';
        $timestamp = date('Y-m-d H:i:s');
        $line      = "[{$timestamp}] {$level}: {$message}" . PHP_EOL;

        file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
    }
}
