<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module = preg_replace('/[^a-z0-9_]/i', '', trim($_POST['module'] ?? ''));

    if ($module) {
        $basePath = __DIR__ . '/../modules/' . $module;

        if (!is_dir($basePath)) {
            mkdir($basePath, 0755, true);
            file_put_contents($basePath . '/form.json', '{}');
            file_put_contents($basePath . '/table.json', '{}');
            file_put_contents($basePath . '/detail.json', '{}');
            file_put_contents($basePath . '/logic.php', "<?php\n// Custom logic for $module\n");
            header("Location: ../public/admin/index.php?success=1");
            exit;
        } else {
            header("Location: ../public/admin/index.php?error=exists");
            exit;
        }
    }
}

header("Location: ../public/admin/index.php?error=invalid");
exit;
