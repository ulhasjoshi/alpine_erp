<pre>
Modules Path: <?= $modulesPath . PHP_EOL ?>
Modules Found:
<?php print_r($modules); ?>
</pre>
<?php
// admin/partials/edit_modules.php

$modulesPath = $_SERVER['DOCUMENT_ROOT'] . '/erp/modules';
$modules = array_filter(scandir($modulesPath), function($dir) use ($modulesPath) {
  return $dir !== '.' && $dir !== '..' && is_dir("$modulesPath/$dir");
});
?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <?php foreach ($modules as $module): ?>
    <div class="bg-white rounded-lg shadow p-4">
      <h2 class="text-lg font-semibold mb-2">Module: <?= htmlspecialchars($module) ?></h2>
      <?php
        $subPath = "$modulesPath/$module";
        $submodules = array_filter(scandir($subPath), function($dir) use ($subPath) {
          return $dir !== '.' && $dir !== '..' && is_dir("$subPath/$dir");
        });
      ?>
      <?php if ($submodules): ?>
        <ul class="text-sm text-gray-700 list-disc pl-5">
          <?php foreach ($submodules as $sub): ?>
            <li><?= htmlspecialchars($sub) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="text-sm text-gray-500 italic">No submodules yet.</p>
      <?php endif; ?>

      <form action="../../admin/schema.php" method="POST" ...>


        <input type="hidden" name="module" value="<?= $module ?>">
        <label class="block text-sm font-medium text-gray-700">Add Submodule</label>
        <input type="text" name="submodule" class="w-full px-3 py-2 border rounded" required>
        <button type="submit" class="bg-black text-white px-3 py-1 rounded hover:bg-gray-800">Add</button>
      </form>
    </div>
  <?php endforeach; ?>
</div>
