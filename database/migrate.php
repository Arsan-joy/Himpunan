<?php
require_once __DIR__ . '/../includes/functions.php';

$files = glob(__DIR__ . '/migrations/*.sql') ?: [];
sort($files, SORT_STRING);

foreach ($files as $file) {
    $sql = file_get_contents($file);
    if ($sql === false || trim($sql) === '') continue;
    try {
        db()->exec($sql);
        echo 'OK ' . basename($file) . PHP_EOL;
    } catch (Throwable $e) {
        fwrite(STDERR, 'FAIL ' . basename($file) . ': ' . $e->getMessage() . PHP_EOL);
        exit(1);
    }
}
