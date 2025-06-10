<?php
$clientsDir = __DIR__ . '/../../../clients';
$modulesDir = __DIR__ . '/../../../modules';
$sharedDir = __DIR__ . '/../../../shared';
$availableModules = array_filter(scandir($modulesDir), fn($d) => $d[0] !== '.' && is_dir("$modulesDir/$d"));

$editClient = null;
$editModules = [];

function createClientDatabase($clientId, $modules, $clientsDir, $modulesDir, $sharedDir) {
    $clientFolder = "$clientsDir/$clientId";
    $dbPath = "$clientFolder/database.sqlite";
    $db = new SQLite3($dbPath);
    $db->enableExceptions(true);

    foreach ($modules as $module) {
        $modulePath = "$modulesDir/$module";
        $submodules = is_dir($modulePath)
            ? array_filter(scandir($modulePath), fn($d) => $d[0] !== '.' && is_dir("$modulePath/$d"))
            : [];

        foreach ($submodules as $sub) {
            $paths = [
                "$modulePath/$sub/table.json",
                "$sharedDir/$sub/table.json"
            ];

            $schema = null;
            foreach ($paths as $path) {
                if (file_exists($path)) {
                    $schema = json_decode(file_get_contents($path), true);
                    break;
                }
            }

            if (!$schema) continue;

            $columns = array_map(fn($f) => "`{$f['name']}` " . strtoupper($f['type'] ?? 'TEXT'), $schema);
            $columns[] = "`created_at` TEXT";
            $columns[] = "`updated_at` TEXT";
            $columns[] = "`is_deleted` INTEGER DEFAULT 0";

            $sql = "CREATE TABLE IF NOT EXISTS `$sub` (" . implode(', ', $columns) . ");";

            try {
                $db->exec($sql);
            } catch (Exception $e) {
                echo "<script>console.error('Error creating $sub: " . addslashes($e->getMessage()) . "');</script>";
            }
        }
    }
}

function cloneSchemasToClient($clientId, $modules, $clientsDir, $modulesDir) {
    foreach ($modules as $module) {
        $modulePath = "$modulesDir/$module";
        $submodules = is_dir($modulePath)
            ? array_filter(scandir($modulePath), fn($d) => $d[0] !== '.' && is_dir("$modulePath/$d"))
            : [];

        foreach ($submodules as $sub) {
            $srcForm = "$modulePath/$sub/form.json";
            $srcTable = "$modulePath/$sub/table.json";

            $destDir = "$clientsDir/$clientId/schemas/$module/$sub";
            if (!is_dir($destDir)) mkdir($destDir, 0777, true);

            if (file_exists($srcForm)) copy($srcForm, "$destDir/form.json");
            if (file_exists($srcTable)) copy($srcTable, "$destDir/table.json");
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['rebuild_db'])) {
        $clientId = $_POST['rebuild_db'];
        $menuFile = "$clientsDir/$clientId/menu.json";
        if (file_exists($menuFile)) {
            $menu = json_decode(file_get_contents($menuFile), true);
            $modules = array_column($menu, 'module');
            createClientDatabase($clientId, $modules, $clientsDir, $modulesDir, $sharedDir);
            echo "<script>alert('Database rebuilt for client: $clientId');location.href='?page=clients';</script>";
            exit;
        }
    }

    if (isset($_POST['clone_schemas'])) {
        $clientId = $_POST['clone_schemas'];
        $menuFile = "$clientsDir/$clientId/menu.json";
        if (file_exists($menuFile)) {
            $menu = json_decode(file_get_contents($menuFile), true);
            $modules = array_column($menu, 'module');
            cloneSchemasToClient($clientId, $modules, $clientsDir, $modulesDir);
            echo "<script>alert('Schemas cloned for client: $clientId');location.href='?page=clients';</script>";
            exit;
        }
    }

    $clientId = $_POST['client_id'] ?? null;
    $clientName = $_POST['name'] ?? $clientId;
    $modules = $_POST['modules'] ?? [];

    if ($clientId) {
        $clientFolder = "$clientsDir/$clientId";
        if (!is_dir($clientFolder)) {
            mkdir($clientFolder);
            copy(__DIR__ . '/../../../storage/starter.sqlite', "$clientFolder/database.sqlite");
            mkdir("$clientFolder/plugins");
        }

        file_put_contents("$clientFolder/menu.json", json_encode(array_map(fn($m) => ["label" => ucfirst($m), "module" => $m], $modules), JSON_PRETTY_PRINT));
        createClientDatabase($clientId, $modules, $clientsDir, $modulesDir, $sharedDir);
        echo "<script>alert('Client saved successfully');location.href='?page=clients';</script>";
        exit;
    }
} elseif (isset($_GET['edit'])) {
    $editClient = $_GET['edit'];
    $menuFile = "$clientsDir/$editClient/menu.json";
    if (file_exists($menuFile)) {
        $menu = json_decode(file_get_contents($menuFile), true);
        $editModules = array_column($menu, 'module');
    }
}

$clientFolders = array_filter(scandir($clientsDir), fn($f) => $f[0] !== '.' && is_dir("$clientsDir/$f"));
?>

<div class="p-6">
  <h1 class="text-xl font-bold mb-4">Manage Clients</h1>

  <div class="mb-8">
    <h2 class="text-lg font-semibold mb-2">Existing Clients</h2>
    <table class="w-full bg-white border rounded shadow">
      <thead>
        <tr class="bg-gray-100">
          <th class="px-4 py-2 text-left">Client ID</th>
          <th class="px-4 py-2 text-left">Modules</th>
          <th class="px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($clientFolders as $clientId): ?>
          <?php
            $menuFile = "$clientsDir/$clientId/menu.json";
            $modules = file_exists($menuFile) ? json_decode(file_get_contents($menuFile), true) : [];
            $moduleLabels = implode(', ', array_map(fn($m) => $m['label'], $modules));
          ?>
          <tr class="border-t">
            <td class="px-4 py-2 font-medium text-gray-800"><?= htmlspecialchars($clientId) ?></td>
            <td class="px-4 py-2 text-gray-600"><?= htmlspecialchars($moduleLabels) ?></td>
            <td class="px-4 py-2 text-right space-x-2">
              <a href="?page=clients&edit=<?= urlencode($clientId) ?>" class="text-blue-600 hover:underline">Edit</a>
              <form method="POST" class="inline">
                <input type="hidden" name="rebuild_db" value="<?= htmlspecialchars($clientId) ?>">
                <button type="submit" class="text-red-600 hover:underline">Rebuild DB</button>
              </form>
              <form method="POST" class="inline">
                <input type="hidden" name="clone_schemas" value="<?= htmlspecialchars($clientId) ?>">
                <button type="submit" class="text-green-600 hover:underline">📁 Clone Schemas</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <form method="POST" class="space-y-4">
    <h2 class="text-lg font-semibold mb-2"><?= $editClient ? "Edit Client: $editClient" : "Add New Client" ?></h2>
    <div>
      <label class="block font-medium">Client ID</label>
      <input type="text" name="client_id" value="<?= htmlspecialchars($editClient ?? '') ?>" <?= $editClient ? 'readonly' : '' ?> required class="w-full border px-3 py-2 rounded bg-gray-100" />
    </div>
    <div>
      <label class="block font-medium">Modules</label>
      <div class="grid grid-cols-2 gap-2">
        <?php foreach ($availableModules as $module): ?>
          <label class="flex items-center space-x-2">
            <input type="checkbox" name="modules[]" value="<?= $module ?>" class="form-checkbox" <?= in_array($module, $editModules) ? 'checked' : '' ?> />
            <span><?= ucfirst($module) ?></span>
          </label>
        <?php endforeach; ?>
      </div>
    </div>
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Save Client</button>
  </form>
</div>
