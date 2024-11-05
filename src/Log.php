<?php

namespace Kai\Log;

class Log
{
    private static string $logDirectory;
    private static string $logLevel = 'info';
    private static int $maxFileSize = 2 * 1024 * 1024; // 2 MB
    private static string $logFileName = 'log.txt';

    // 静态初始化，自动设置日志目录
    public static function init(string $directory = null, string $level = 'info', int $maxFileSize = 2097152): void
    {
        // 设置默认日志目录
        self::$logDirectory = $directory ?: (is_dir(__DIR__ . '/../logs') ? __DIR__ . '/../logs' : __DIR__ . '/../kailogs');
        self::$logLevel = $level;
        self::$maxFileSize = $maxFileSize;

        // 如果日志目录不存在，则创建
        if (!is_dir(self::$logDirectory)) {
            mkdir(self::$logDirectory, 0777, true);
        }
    }
            
    public static function write(string $message, string $level = 'info'): void
    {
        //
        if (!self::isLogLevelAllowed($level)) {
            return;
        }

        // 获取当前的日志文件路径
        $logFile = self::getLogFilePath();

        // 检查文件大小是否超过限制，若超出则生成新文件
        if (file_exists($logFile) && filesize($logFile) >= self::$maxFileSize) {
            rename($logFile, self::$logDirectory . '/log_' . time() . '.txt');
        }

        // 格式化日志内容并写入
        $date = date('Y-m-d H:i:s');
        $formattedMessage = "[$date] [$level] $message" . PHP_EOL;
        file_put_contents($logFile, $formattedMessage, FILE_APPEND);
    }

    private static function isLogLevelAllowed(string $level): bool
    {
        $levels = ['info' => 0, 'warning' => 1, 'error' => 2];
        return $levels[$level] >= $levels[self::$logLevel];
    }

    private static function getLogFilePath(): string
    {
        return self::$logDirectory . '/' . self::$logFileName;
    }
}