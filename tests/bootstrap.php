<?php
/**
 * Bootstrap the test cases.
 * @author David Desberg  <david@daviddesberg.com>
 */

namespace tests;

// minimal PSR-0 autoloader
/**
 * @codeCoverageIgnore
 */
spl_autoload_register(function ($classname) {
    $classname = ltrim($classname, '\\');
    preg_match('/^(.+)?([^\\\\]+)$/U', $classname, $match);
    $level = error_reporting(0);
    include ( DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $match[1]) . str_replace(['\\', '_'], DIRECTORY_SEPARATOR, $match[2]) . '.php' );
    error_reporting($level);
});