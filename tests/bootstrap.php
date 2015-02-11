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
    $file = dirname(__DIR__) . DIRECTORY_SEPARATOR
          . 'src' . DIRECTORY_SEPARATOR
          . str_replace('\\', DIRECTORY_SEPARATOR, $classname)
          . '.php';
    if (file_exists($file)) {
        include $file;
    }
});
