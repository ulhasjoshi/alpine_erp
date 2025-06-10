<?php
$clientsDir = __DIR__ . '/../../../clients';
$modulesDir = __DIR__ . '/../../../modules';
$sharedDir = __DIR__ . '/../../../shared';
$adminDb = new SQLite3(__DIR__ . '/../../../storage/admin.sqlite');
$adminDb->exec("CREATE TABLE IF NOT EXISTS client_admins (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  client_id TEXT,
  email TEXT,
  password TEXT,
  role_id TEXT
)");
$adminDb->exec("CREATE TABLE IF NOT EXISTS roles (
  id TEXT,
  client_id TEXT,
  name TEXT,
  default_page TEXT
)");

$availableModules = array_filter(scandir($modulesDir), fn($d) => $d[0] !== '.' && is_dir("$modulesDir/$d"));
$editClient = $_GET['edit'] ?? null;
$editModules = [];
$editUsers = [];
$editRoles = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientId = $_POST['client_id'];
    $modules = $_POST['modules'] ?? [];

    // Save modules to menu.json
    $clientFolder = "$clientsDir/$clientId";
    if (!is_dir($clientFolder)) {
        mkdir($clientFolder, 0777, true);
        copy(__DIR__ . '/../../../storage/starter.sqlite', "$clientFolder/database.sqlite");
        mkdir("$clientFolder/plugins");
    }
    file_put_contents("$clientFolder/menu.json", json_encode(array_map(fn($m) => ["label" => ucfirst($m), "module" => $m], $modules), JSON_PRETTY_PRINT));

    // Save admin user
    if (!empty($_POST['admin_email'])) {
        $email = $_POST['admin_email'];
        $password = $_POST['admin_password'];
        $roleId = $_POST['role_id'];

        $stmt = $adminDb->prepare("SELECT id FROM client_admins WHERE client_id = ? AND email = ?");
        $stmt->bindValue(1, $clientId);
        $stmt->bindValue(2, $email);
        $exists = $stmt->execute()->fetchArray();

        if ($exists) {
            if ($password) {
                $stmt = $adminDb->prepare("UPDATE client_admins SET password = ?, role_id = ? WHERE client_id = ? AND email = ?");
                $stmt->bindValue(1, password_hash($password, PASSWORD_DEFAULT));
                $stmt->bindValue(2, $roleId);
                $stmt->bindValue(3, $clientId);
                $stmt->bindValue(4, $email);
            } else {
                $stmt = $adminDb->prepare("UPDATE client_admins SET role_id = ? WHERE client_id = ? AND email = ?");
                $stmt->bindValue(1, $roleId);
                $stmt->bindValue(2, $clientId);
                $stmt->bindValue(3, $email);
            }
        } else {
            $stmt = $adminDb->prepare("INSERT INTO client_admins (client_id, email, password, role_id) VALUES (?, ?, ?, ?)");
            $stmt->bindValue(1, $clientId);
            $stmt->bindValue(2, $email);
            $stmt->bindValue(3, password_hash($password, PASSWORD_DEFAULT));
            $stmt->bindValue(4, $roleId);
        }
        $stmt->execute();
    }

    echo "<script>alert('Client saved successfully');location.href='?page=clients';</script>";
    exit;
}

if (isset($_GET['edit'])) {
    $menuFile = "$clientsDir/$editClient/menu.json";
    if (file_exists($menuFile)) {
        $menu = json_decode(file_get_contents($menuFile), true);
        $editModules = array_column($menu, 'module');
    }

    $stmt = $adminDb->prepare("SELECT * FROM client_admins WHERE client_id = ?");
    $stmt->bindValue(1, $editClient);
    $result = $stmt->execute();
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $editUsers[] = $row;
    }

    $stmt = $adminDb->prepare("SELECT * FROM roles WHERE client_id = ?");
    $stmt->bindValue(1, $editClient);
    $result = $stmt->execute();
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $editRoles[] = $row;
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
    <div class="mt-4">
      <label class="block font-medium">Add/Edit Client User</label>
      <input type="email" name="admin_email" placeholder="User Email" required class="w-full border px-3 py-2 rounded" />
      <input type="password" name="admin_password" placeholder="Password (leave blank to retain)" class="w-full border px-3 py-2 rounded" />
      <select name="role_id" class="w-full border px-3 py-2 rounded" required>
        <option value="">Select Role</option>
        <?php foreach ($editRoles as $role): ?>
          <option value="<?= htmlspecialchars($role['id']) ?>"><?= htmlspecialchars($role['name']) ?> → <?= $role['default_page'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Save Client</button>
  </form>

  <?php if ($editClient): ?>
    <div class="mt-8">
      <h3 class="text-lg font-semibold mb-2">Existing Users</h3>
      <table class="w-full bg-white border rounded">
        <thead>
          <tr class="bg-gray-100">
            <th class="px-4 py-2 text-left">Email</th>
            <th class="px-4 py-2 text-left">Role</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($editUsers as $user): ?>
            <tr class="border-t">
              <td class="px-4 py-2"><?= htmlspecialchars($user['email']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($user['role_id']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
