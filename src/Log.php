<?php

namespace Kai\Log;

class Log
{
    private static ?string $logDirectory = null;
    private static string $logLevel = 'info';
    private static int $maxFileSize = 2 * 1024 * 1024; // 2 MB
    private static string $logFileName = 'log.log';

    /**
     * 初始化日志设置
     */
    private static function initialize(): void
    {
        if (self::$logDirectory === null) {
            // 默认日志目录为 /logs 或 /kailogs，若不可写则使用系统临时目录
            $defaultDirectory = __DIR__ . '/../logs';
            self::$logDirectory = is_writable($defaultDirectory) ? $defaultDirectory : sys_get_temp_dir() . '/kailogs';

            if (!is_dir(self::$logDirectory)) {
                mkdir(self::$logDirectory, 0777, true);
            }
        }
    }

    /**
     * 写入日志
     */
    public static function write(string $message, string $level = 'info'): void
    {
        // 自动初始化（仅在首次调用时执行）
        self::initialize();

        if (!self::isLogLevelAllowed($level)) {
            return;
        }

        // 获取当前日志文件路径
        $logFile = self::getLogFilePath();

        // 文件大小超过限制时重命名文件
        if (file_exists($logFile) && filesize($logFile) >= self::$maxFileSize) {
            rename($logFile, self::$logDirectory . '/log_' . time() . '.log');
        }

        // 格式化日志内容并写入
        $date = date('Y-m-d H:i:s');
        $formattedMessage = "[$date] [$level] $message" . PHP_EOL;
        file_put_contents($logFile, $formattedMessage, FILE_APPEND);
    }

    /**
     * 检查日志等级是否符合要求
     */
    private static function isLogLevelAllowed(string $level): bool
    {
        $levels = ['info' => 0, 'warning' => 1, 'error' => 2];
        return $levels[$level] >= $levels[self::$logLevel];
    }

    /**
     * 获取日志文件路径
     */
    private static function getLogFilePath(): string
    {
        return self::$logDirectory . '/' . self::$logFileName;
    }
}
