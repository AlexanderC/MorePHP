<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 11/5/13
 * @time 11:58 AM
 */

spl_autoload_register(function ($class) {
    $rawParts = explode("\\", $class);

    if (count($rawParts) <= 0) {
        return false;
    }

    if ($rawParts[0] == "PHP") {
        $path = __DIR__ . '/../src/';
        $parts = $rawParts;

        $file = realpath($path . implode("/", $parts) . ".php");

        return is_file($file) ? require $file : false;
    } else {
        $path = __DIR__;
        $parts = & $rawParts;
        $file = realpath($path . implode("/", $parts) . ".php");

        return is_file($file) ? require $file : false;
    }
});