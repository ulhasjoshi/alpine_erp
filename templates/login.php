<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
  <form method="POST" class="bg-white p-6 rounded shadow w-full max-w-sm">
    <h2 class="text-xl font-bold mb-4">Login</h2>
    <input type="hidden" name="action" value="login">
    <input name="email" type="email" placeholder="Email" required class="w-full mb-2 p-2 border rounded">
    <input name="password" type="password" placeholder="Password" required class="w-full mb-4 p-2 border rounded">
    <button class="bg-blue-600 text-white px-4 py-2 rounded w-full" type="submit">Login</button>
  </form>
</body>
</html>
