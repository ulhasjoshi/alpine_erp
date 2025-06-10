<?php
header('Content-Type: application/json');

// Validate input
$module = $_POST['module'] ?? '';
$submodule = $_POST['submodule'] ?? '';

if (!$module || !$submodule || preg_match('/[^a-zA-Z0-9_]/', $module . $submodule)) {
    echo json_encode(['status' => 'invalid']);
    exit;
}

// Build paths
$basePath = $_SERVER['DOCUMENT_ROOT'] . '/erp/modules';
$targetPath = "$basePath/$module/$submodule";

// Create submodule folder if it doesn't exist
if (!is_dir($targetPath)) {
    mkdir($targetPath, 0777, true);
}

// Create default form.json if not exists
$formSchema = [
    ['name' => 'name', 'label' => 'Name', 'type' => 'text']
];
$formFile = "$targetPath/form.json";
if (!file_exists($formFile)) {
    file_put_contents($formFile, json_encode($formSchema, JSON_PRETTY_PRINT));
}

// Create default table.json if not exists
$tableSchema = [
    ['name' => 'name', 'label' => 'Name']
];
$tableFile = "$targetPath/table.json";
if (!file_exists($tableFile)) {
    file_put_contents($tableFile, json_encode($tableSchema, JSON_PRETTY_PRINT));
}

echo json_encode(['status' => 'success', 'message' => 'Submodule created']);
