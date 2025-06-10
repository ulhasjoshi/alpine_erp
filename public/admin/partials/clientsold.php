<?php
$clientsDir = __DIR__ . '/../../../clients';
$modulesDir = __DIR__ . '/../../../modules';
$availableModules = array_filter(scandir($modulesDir), fn($d) => $d[0] !== '.' && is_dir("$modulesDir/$d"));

$editClient = null;
$editModules = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientId = $_POST['client_id'];
    $clientName = $_POST['name'] ?? $clientId;
    $modules = $_POST['modules'] ?? [];

    $clientFolder = "$clientsDir/$clientId";
    if (!is_dir($clientFolder)) {
        mkdir($clientFolder);
        copy(__DIR__ . '/../../../storage/starter.sqlite', "$clientFolder/database.sqlite");
        mkdir("$clientFolder/plugins");
    }

    file_put_contents("$clientFolder/menu.json", json_encode(array_map(fn($m) => ["label" => ucfirst($m), "module" => $m], $modules), JSON_PRETTY_PRINT));

    echo "<script>alert('Client saved successfully');location.href='?page=clients';</script>";
    exit;
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
          <th class="px-4 py-2">Action</th>
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
            <td class="px-4 py-2 text-right">
              <a href="?page=clients&edit=<?= urlencode($clientId) ?>" class="text-blue-600 hover:underline">Edit</a>
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
