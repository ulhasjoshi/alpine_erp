<?php
// modules/dashboard/view.php

function getCount($module, $sub) {
    $clientId = $_SESSION['client_id'] ?? 'default';
    $dbPath = __DIR__ . "/../../clients/$clientId/database.sqlite";
    if (!file_exists($dbPath)) return 0;

    try {
        $db = new PDO("sqlite:$dbPath");
        $table = strtolower("{$module}_{$sub}");
        $stmt = $db->query("SELECT COUNT(*) FROM $table");
        return $stmt ? $stmt->fetchColumn() : 0;
    } catch (Exception $e) {
        return 0;
    }
}
?>

<h2 class="text-2xl font-bold mb-6">Invoicing Dashboard</h2>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <div class="bg-white rounded-xl shadow p-4">
    <h3 class="text-gray-500 text-sm">Total Invoices</h3>
    <p class="text-2xl font-bold text-gray-800"><?= getCount('invoicing', 'invoices') ?></p>
  </div>

  <div class="bg-white rounded-xl shadow p-4">
    <h3 class="text-gray-500 text-sm">Total Products</h3>
    <p class="text-2xl font-bold text-gray-800"><?= getCount('invoicing', 'products') ?></p>
  </div>

  <div class="bg-white rounded-xl shadow p-4">
    <h3 class="text-gray-500 text-sm">Total Customers</h3>
    <p class="text-2xl font-bold text-gray-800"><?= getCount('crm', 'parties') ?></p>
  </div>

  <div class="bg-white rounded-xl shadow p-4">
    <h3 class="text-gray-500 text-sm">Draft Invoices</h3>
    <p class="text-2xl font-bold text-gray-800">Coming Soon</p>
  </div>
</div>

<div class="bg-white rounded-xl shadow p-6">
  <h3 class="text-lg font-semibold mb-4">Invoice Summary Chart (Coming Soon)</h3>
  <div class="text-center text-gray-500">[ Chart Placeholder ]</div>
</div>
