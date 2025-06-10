<?php
$HOOKS = [];

function register_hook($name, $callback) {
    global $HOOKS;
    $HOOKS[$name][] = $callback;
}

function do_hook($name, $params = []) {
    global $HOOKS;
    foreach ($HOOKS[$name] ?? [] as $callback) {
        call_user_func_array($callback, $params);
    }
}
