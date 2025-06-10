<?php
// admin/schema.php

$baseModulesPath = $_SERVER['DOCUMENT_ROOT'] . "/erp/modules";
header('Content-Type: application/json');

// Case 1: From Schema Builder UI (POST JSON schema)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schema'])) {
    $module = preg_replace('/[^a-z0-9_\-]/i', '', $_POST['module'] ?? '');
    $sub = preg_replace('/[^a-z0-9_\-]/i', '', $_POST['submodule'] ?? '');
    $type = $_POST['type'] ?? 'form';
    $schemaJson = $_POST['schema'] ?? '';

    if (!$module || !$sub || !in_array($type, ['form', 'table'])) {
        echo json_encode(['status' => 'invalid']);
        exit;
    }

    $path = "$baseModulesPath/$module/$sub";
    if (!is_dir($path)) mkdir($path, 0777, true);

    file_put_contents("$path/$type.json", $schemaJson);
    echo json_encode(['status' => 'success', 'message' => "$type schema updated"]);
    exit;
}

// Case 2: JSON API - Build schemas
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['module'], $data['submodule'], $data['fields'])) {
    $module = preg_replace('/[^a-z0-9_\-]/i', '', $data['module']);
    $sub = preg_replace('/[^a-z0-9_\-]/i', '', $data['submodule']);
    $fields = $data['fields'];

    $path = "$baseModulesPath/$module/$sub";
    if (!is_dir($path)) mkdir($path, 0777, true);

    file_put_contents("$path/form.json", json_encode($fields, JSON_PRETTY_PRINT));
    file_put_contents("$path/table.json", json_encode($fields, JSON_PRETTY_PRINT));

    echo json_encode(['status' => 'ok']);
    exit;
}

// ✅ Case 3: Plain POST from HTML form (Add Submodule)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['module'], $_POST['submodule'])) {
    $module = preg_replace('/[^a-z0-9_\-]/i', '', $_POST['module']);
    $sub = preg_replace('/[^a-z0-9_\-]/i', '', $_POST['submodule']);

    $path = "$baseModulesPath/$module/$sub";
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
        // Default schema with audit fields
        $defaultForm = [
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'source' => 'picklist:status'],
            ['name' => 'created_at', 'label' => 'Created At', 'type' => 'datetime', 'hidden' => true],
            ['name' => 'updated_at', 'label' => 'Updated At', 'type' => 'datetime', 'hidden' => true],
        ];
        file_put_contents("$path/form.json", json_encode($defaultForm, JSON_PRETTY_PRINT));
        file_put_contents("$path/table.json", json_encode([['label' => 'Name', 'field' => 'name']], JSON_PRETTY_PRINT));
    }

    echo json_encode(['status' => 'success', 'message' => 'Submodule created']);
exit;

    exit;
}

echo json_encode(['status' => 'invalid']);
