<?php

namespace Kai\Log;

class Log
{
    private static string $logDirectory = __DIR__ . '/../logs';
    private static string $logLevel = 'info';
    private static int $maxFileSize = 2 * 1024 * 1024; // 2 MB
    private static string $logFileName = 'log.log'; // 默认日志文件名

    /**
     * 初始化日志设置
     */
    public static function init(string $directory, string $level = 'info', int $maxFileSize = 2097152): void
    {
        self::$logDirectory = $directory;
        self::$logLevel = $level;
        self::$maxFileSize = $maxFileSize;

        // 创建日志目录（如果不存在）
        if (!is_dir(self::$logDirectory)) {
            mkdir(self::$logDirectory, 0777, true);
        }
    }

    /**
     * 写入日志
     */
    public static function write(string $message, string $level = 'info'): void
    {
        // 检查日志级别是否符合要求
        if (!self::isLogLevelAllowed($level)) {
            return;
        }

        // 获取当前日志文件路径
        $logFile = self::getLogFilePath();

        // 如果文件超过指定大小，则重命名文件
        if (file_exists($logFile) && filesize($logFile) >= self::$maxFileSize) {
            $newFileName = self::$logDirectory . '/log_' . time() . '.log';
            rename($logFile, $newFileName);
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
