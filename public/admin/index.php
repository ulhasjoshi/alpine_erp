<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../engine/auth.php';

requireLogin();

$page = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Builder</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet"/>
  <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100">
  <div x-data="{ sidebarOpen: false, menu: { modules: false, schema: false } }" class="flex h-screen overflow-hidden">
    <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed z-30 inset-y-0 left-0 w-64 transition duration-300 transform bg-black text-white shadow-lg md:translate-x-0 md:static md:inset-0">
      <div class="flex items-center justify-between p-4 border-b border-gray-700">
        <span class="text-xl font-semibold">Admin Builder</span>
        <button @click="sidebarOpen = false" class="md:hidden">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
      <nav class="mt-4">
        <a href="index.php" class="block px-4 py-2 hover:bg-gray-800">Dashboard</a>
        <a href="index.php?page=clients" class="block px-4 py-2 hover:bg-gray-800">Clients</a>
        <div>
          <button @click="menu.modules = !menu.modules" class="w-full text-left px-4 py-2 hover:bg-gray-800 flex justify-between items-center">
            Modules
            <svg :class="menu.modules ? 'rotate-90' : ''" class="w-4 h-4 transform transition-transform"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </button>
          <div x-show="menu.modules" class="pl-6">
            <a href="index.php?page=add_module" class="block px-2 py-1 text-gray-300 hover:bg-gray-800">Add Module</a>
            <a href="index.php?page=edit_modules" class="block px-2 py-1 text-gray-300 hover:bg-gray-800">Edit Modules</a>
          </div>
        </div>
        <div>
          <button @click="menu.schema = !menu.schema" class="w-full text-left px-4 py-2 hover:bg-gray-800 flex justify-between items-center">
            Schema Builder
            <svg :class="menu.schema ? 'rotate-90' : ''" class="w-4 h-4 transform transition-transform"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </button>
          <div x-show="menu.schema" class="pl-6">
            <a href="index.php?page=schemas" class="block px-2 py-1 text-gray-300 hover:bg-gray-800">Central Schema Builder</a>  
            <a href="forms.php" class="block px-2 py-1 text-gray-300 hover:bg-gray-800">Forms</a>
            <a href="tables.php" class="block px-2 py-1 text-gray-300 hover:bg-gray-800">Tables</a>
          </div>
        </div>
      <a href="../logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-800 mt-4 border-t border-gray-700">
  Logout
</a>
</nav>

    </div>

    <div class="flex-1 flex flex-col overflow-hidden">
      <header class="flex items-center justify-between bg-white shadow px-4 py-3 md:hidden">
        <button @click="sidebarOpen = true">
          <svg class="w-6 h-6" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <span class="text-lg font-semibold">Admin Builder</span>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <?php
          switch ($page) {
            case 'clients':
              include 'partials/clients.php';
              break;
            case 'add_module':
              include 'partials/add_module.php';
              break;
            case 'edit_modules':
              include 'partials/edit_modules.php';
              break;
            case 'schemas':
              include 'partials/schemas.php';
              break;
              
  
            default:
              echo '<h1 class="text-2xl font-bold">Welcome to Admin Builder</h1>';
          }
        ?>
      </main>
    </div>
  </div>
</body>
</html>
