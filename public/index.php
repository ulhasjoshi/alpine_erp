if ($client === 'admin' && $email === 'admin@example.com') {
    $pdo = new PDO("sqlite:../storage/admin.sqlite");
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['client'] = 'admin';
        $_SESSION['user'] = $user;
        header("Location: admin/index.php");
        exit;
    } else {
        $error = "Invalid Super Admin login.";
    }
} else {
    $adminDb = new PDO("sqlite:../storage/admin.sqlite");
    $stmt = $adminDb->prepare("SELECT * FROM client_admins WHERE client_id = ? AND email = ?");
    $stmt->execute([$client, $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['client'] = $client;
        $_SESSION['user'] = $admin;

        // 🔁 Get default_page from role
        $db = new PDO("sqlite:../clients/$client/database.sqlite");
        $roleId = $admin['role_id'] ?? '';
        $stmt = $db->prepare("SELECT default_page FROM roles WHERE id = ?");
        $stmt->execute([$roleId]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        $redirect = $role['default_page'] ?? 'index.php';

        header("Location: $redirect");
        exit;
    } else {
        $error = "Invalid client admin login.";
    }
}
