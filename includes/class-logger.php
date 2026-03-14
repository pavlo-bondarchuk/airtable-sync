<?php
if (!defined('ABSPATH')) exit;

class AT_Sync_Logger
{

    private static $file;

    public static function init()
    {
        self::$file = WP_CONTENT_DIR . '/airtable-sync.log';
        if (!file_exists(self::$file)) {
            file_put_contents(self::$file, "=== Airtable Sync Log Started ===\n");
        }
    }

    public static function log($msg)
    {
        $time = date('Y-m-d H:i:s');
        $line = "[$time] $msg\n";
        file_put_contents(self::$file, $line, FILE_APPEND);
    }

    public static function read()
    {
        if (!file_exists(self::$file)) return '';
        return file_get_contents(self::$file);
    }

    public static function clear()
    {
        file_put_contents(self::$file, "=== Log cleared at " . date('Y-m-d H:i:s') . " ===\n");
    }
}

AT_Sync_Logger::init();

// Global helper
function atsync_log($msg)
{
    AT_Sync_Logger::log($msg);
}
