<?php

namespace Kai\Log;

class Log
{
    private static string $logDirectory = __DIR__ . '/../logs';
    private static string $logLevel = 'info';
    private static int $maxFileSize = 2 * 1024 * 1024;

    public static function init(string $directory, string $level = 'info', int $maxFileSize = 2097152): void
    {
        self::$logDirectory = $directory;
        self::$logLevel = $level;
        self::$maxFileSize = $maxFileSize;

        if (!is_dir(self::$logDirectory)) {
            mkdir(self::$logDirectory, 0777, true);
        }
    }

    public static function write(string $message, string $level = 'info'): void
    {
        if (!self::isLogLevelAllowed($level)) {
            return;
        }

        $logFile = self::getLogFilePath();

        if (file_exists($logFile) && filesize($logFile) >= self::$maxFileSize) {
            $logFile = self::getLogFilePath(true);
        }

        $date = date('Y-m-d H:i:s');
        $formattedMessage = "[$date] [$level] $message" . PHP_EOL;

        file_put_contents($logFile, $formattedMessage, FILE_APPEND);
    }

    private static function isLogLevelAllowed(string $level): bool
    {
        $levels = ['info' => 0, 'warning' => 1, 'error' => 2];
        return $levels[$level] >= $levels[self::$logLevel];
    }

    private static function getLogFilePath(bool $isNewFile = false): string
    {
        $date = date('Y-m-d');
        $filePath = self::$logDirectory . "/log-{$date}.log";

        if ($isNewFile) {
            $filePath = self::$logDirectory . "/log-{$date}-" . time() . ".log";
        }

        return $filePath;
    }
}
