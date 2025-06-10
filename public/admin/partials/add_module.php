<?php
// admin/partials/add_module.php
?>
<div class="bg-white rounded-lg shadow p-6">
  <h2 class="text-xl font-semibold mb-4">Create New Module</h2>
  <form action="../modules.php" method="POST" class="space-y-4">
    <div>
      <label class="block text-gray-700 font-medium">Module Name</label>
      <input type="text" name="module" class="w-full mt-1 px-3 py-2 border rounded-md" required>
    </div>
    <button type="submit" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800">Create Module</button>
  </form>
</div>
