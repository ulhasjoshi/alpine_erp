<?php
$module = $_GET['module'] ?? '';
$sub = $_GET['submodule'] ?? '';
$tablePath = $_SERVER['DOCUMENT_ROOT'] . "/erp/modules/$module/$sub/table.json";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newSchema = $_POST['schema'] ?? '';
    file_put_contents($tablePath, $newSchema);
    header("Location: tables.php?module=$module&submodule=$sub&saved=1");
    exit;
}

$currentSchema = file_exists($tablePath) ? file_get_contents($tablePath) : '[]';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Table Schema</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="p-6">
    <h1 class="text-2xl font-bold mb-4">Edit Table: <?= htmlspecialchars("$module / $sub") ?></h1>
    <?php if (isset($_GET['saved'])): ?>
        <div class="bg-green-100 text-green-800 px-4 py-2 mb-4 rounded">Schema saved successfully.</div>
    <?php endif; ?>
    <form method="POST">
        <textarea name="schema" rows="20" class="w-full border p-4 font-mono text-sm"><?= htmlspecialchars($currentSchema) ?></textarea>
        <button type="submit" class="mt-4 bg-black text-white px-6 py-2 rounded hover:bg-gray-800">Save Schema</button>
    </form>
</body>
</html>
