<?php
require_once __DIR__ . '/auth.php';

$client = $_SESSION['client'];
$module = 'rbac';
$submodule = 'users';

// Permissions
$canCreate = hasPermission($module, $submodule, 'create');
$canEdit = hasPermission($module, $submodule, 'update');
$canDelete = hasPermission($module, $submodule, 'delete');

// Load table schema
$schema = loadSchema($client, $module, $submodule, 'table');
$columns = $schema['columns'] ?? [];

// Fetch data (example using PDO, replace with your actual data source)
$pdo = new PDO("sqlite:../clients/$client/database.sqlite");
$data = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="p-4">
    <h2 class="text-xl font-bold mb-4"><?= $schema['title'] ?? 'Users' ?></h2>

    <?php if ($canCreate): ?>
        <button class="mb-4 px-4 py-2 bg-blue-600 text-white rounded">+ Add User</button>
    <?php endif; ?>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white shadow rounded-lg">
            <thead class="bg-gray-100 text-gray-600">
                <tr>
                    <?php foreach ($columns as $col): ?>
                        <th class="px-4 py-2 text-left"><?= $col['label'] ?></th>
                    <?php endforeach; ?>
                    <?php if ($canEdit || $canDelete): ?>
                        <th class="px-4 py-2">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="text-gray-800">
                <?php foreach ($data as $row): ?>
                    <tr class="border-b">
                        <?php foreach ($columns as $col): ?>
                            <td class="px-4 py-2"><?= htmlspecialchars($row[$col['name']] ?? '') ?></td>
                        <?php endforeach; ?>

                        <?php if ($canEdit || $canDelete): ?>
                            <td class="px-4 py-2 space-x-2">
                                <?php if ($canEdit): ?>
                                    <button class="text-blue-600">Edit</button>
                                <?php endif; ?>
                                <?php if ($canDelete): ?>
                                    <button class="text-red-600">Delete</button>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
