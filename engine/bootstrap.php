<?php
$tenant = $_SESSION['tenant'] ?? 'default';
$pluginDir = __DIR__ . "/../clients/$tenant/plugins/";

if (is_dir($pluginDir)) {
    foreach (glob("$pluginDir/*.php") as $file) {
        include_once $file;
    }
}
