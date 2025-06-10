<?php
// partials/schemas.php

$modulesPath = $_SERVER['DOCUMENT_ROOT'] . '/erp/modules';
$modules = array_filter(scandir($modulesPath), function ($dir) use ($modulesPath) {
    return $dir !== '.' && $dir !== '..' && is_dir("$modulesPath/$dir");
});
?>

<div x-data="{ openModules: {} }" class="space-y-6">
    <?php foreach ($modules as $module): ?>
        <div class="bg-white shadow rounded">
            <button @click="openModules['<?= $module ?>'] = !openModules['<?= $module ?>']"
                    class="w-full text-left px-4 py-2 bg-gray-200 hover:bg-gray-300 flex justify-between items-center">
                <span class="text-lg font-semibold"><?= htmlspecialchars(ucfirst($module)) ?></span>
                <svg :class="openModules['<?= $module ?>'] ? 'transform rotate-90' : ''"
                     class="w-5 h-5 transition-transform" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            <div x-show="openModules['<?= $module ?>']" x-transition class="p-4">
                <?php
                $subPath = "$modulesPath/$module";
                $submodules = array_filter(scandir($subPath), function ($dir) use ($subPath) {
                    return $dir !== '.' && $dir !== '..' && is_dir("$subPath/$dir");
                });
                ?>
                <?php if ($submodules): ?>
                
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <?php foreach ($submodules as $sub): ?>
                            <div class="border rounded p-3 bg-gray-50">
                                <h3 class="font-medium text-gray-800 mb-2"><?= htmlspecialchars(ucfirst($sub)) ?></h3>
                <div class="space-y-2">
    <a href="/erp/public/admin/edit-schema.php?module=<?= urlencode($module) ?>&submodule=<?= urlencode($sub) ?>&type=form"
       class="block text-blue-600 hover:underline">✏️ Edit Form</a>

    <a href="/erp/public/admin/edit-schema.php?module=<?= urlencode($module) ?>&submodule=<?= urlencode($sub) ?>&type=table"
       class="block text-green-600 hover:underline">📋 Edit Table</a>
</div>

                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-sm text-gray-500 italic">No submodules yet.</p>
                <?php endif; ?>

                <!-- Add Submodule Form -->
                <form action="../../admin/schema.php" method="POST" class="mt-4">
                    <input type="hidden" name="module" value="<?= htmlspecialchars($module) ?>">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Add Submodule</label>
                    <div class="flex space-x-2">
                        <input type="text" name="submodule" class="flex-1 px-3 py-2 border rounded" required>
                        <button type="submit"
                                class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800">Add</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>
