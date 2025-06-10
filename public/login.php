if ($client === 'admin' && $email === 'admin@example.com') {
    // Super Admin Login (no role check)
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
    // Client User Login with Role Redirect
    $adminDb = new PDO("sqlite:../storage/admin.sqlite");
    $stmt = $adminDb->prepare("SELECT * FROM client_admins WHERE client_id = ? AND email = ?");
    $stmt->execute([$client, $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['client'] = $client;
        $_SESSION['user'] = $admin;

        $db = new PDO("sqlite:../clients/$client/database.sqlite");

        // Fetch role-based default page
        $roleId = $admin['role_id'] ?? '';
        $stmt = $db->prepare("SELECT default_page FROM roles WHERE id = ?");
        $stmt->execute([$roleId]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        $redirect = $role['default_page'] ?? 'index.php'; // fallback
        header("Location: $redirect");
        exit;
    } else {
        $error = "Invalid client admin login.";
    }
}
