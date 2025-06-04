<?php
class Losml_Autoloader {
    protected static $prefixes = [];

    public static function registerNamespace($prefix, $baseDir) {
        $prefix = trim($prefix, '\\') . '\\';
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        self::$prefixes[$prefix] = $baseDir;
    }

    public static function register() {
        spl_autoload_register([self::class, 'loadClass']);
    }

    public static function loadClass($class) {
        foreach (self::$prefixes as $prefix => $baseDir) {
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                continue;
            }

            $relativeClass = substr($class, $len);

            $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

            if (file_exists($file)) {
                require_once $file;
                return true;
            }
        }

        return false;
    }
}
