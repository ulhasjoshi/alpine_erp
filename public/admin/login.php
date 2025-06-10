<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client = $_POST['client'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $pdo = new PDO("sqlite:../clients/$client/database.sqlite");
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['client'] = $client;
        $_SESSION['user'] = $user;
        header("Location: admin/index.php");
        exit;
    } else {
        $error = "Invalid login.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">
    <form method="POST" class="bg-white p-8 shadow rounded w-96">
        <h2 class="text-2xl font-bold mb-6">ERP Login</h2>
        <?php if (!empty($error)): ?>
            <p class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <input name="client" placeholder="Client ID" required class="w-full mb-4 p-2 border rounded" />
        <input type="email" name="email" placeholder="Email" required class="w-full mb-4 p-2 border rounded" />
        <input type="password" name="password" placeholder="Password" required class="w-full mb-4 p-2 border rounded" />
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full">Login</button>
    </form>
</body>
</html>
