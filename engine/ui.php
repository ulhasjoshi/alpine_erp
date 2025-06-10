function loadSchema($client, $module, $submodule, $type = 'form') {
    $clientSchema = __DIR__ . "/../clients/$client/schemas/$module/$submodule/$type.json";
    $defaultSchema = __DIR__ . "/../modules/$module/$submodule/$type.json";

    if (file_exists($clientSchema)) {
        return json_decode(file_get_contents($clientSchema), true);
    } elseif (file_exists($defaultSchema)) {
        return json_decode(file_get_contents($defaultSchema), true);
    } else {
        return [];
    }
}
