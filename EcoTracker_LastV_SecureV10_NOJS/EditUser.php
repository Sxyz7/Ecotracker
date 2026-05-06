<?php
session_start();
include 'Conection.php';
require_once 'admin_guard.php';

require_admin($conn, false);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$defaultAvatar = 'default-avatar.svg';

if ($id <= 0) {
    echo "Invalid user ID.";
    exit();
}

$message = '';

function save_admin_avatar(array $file, int $userId): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Avatar upload failed.');
    }

    if (($file['size'] ?? 0) > (3 * 1024 * 1024)) {
        throw new Exception('Avatar is too large. Maximum size is 3MB.');
    }

    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif'
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);

    if (!$finfo) {
        throw new Exception('Could not validate avatar type.');
    }

    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!isset($allowedMimes[$mime])) {
        throw new Exception('Invalid avatar format. Use JPG, PNG, WEBP, or GIF.');
    }

    $uploadDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'avatars';

    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true)) {
        throw new Exception('Could not create avatar folder.');
    }

    $token = bin2hex(random_bytes(8));
    $fileName = 'avatar_admin_' . $userId . '_' . $token . '.' . $allowedMimes[$mime];
    $targetFile = $uploadDirectory . DIRECTORY_SEPARATOR . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
        throw new Exception('Could not save avatar.');
    }

    return 'uploads/avatars/' . $fileName;
}

$stmt = $conn->prepare("
    SELECT id, nome, usuario, email, role, COALESCE(NULLIF(avatar_path, ''), ?) AS avatar_path
    FROM usuarios
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("si", $defaultAvatar, $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "User not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = trim($_POST['role'] ?? 'user');

    if ($nome === '' || $usuario === '' || $email === '') {
        $message = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email address.';
    } elseif (!in_array($role, ['user', 'admin'], true)) {
        $message = 'Invalid role.';
    } else {
        $checkStmt = $conn->prepare("SELECT id FROM usuarios WHERE (email = ? OR usuario = ?) AND id <> ? LIMIT 1");
        $checkStmt->bind_param("ssi", $email, $usuario, $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult && $checkResult->num_rows > 0) {
            $message = 'Email or username already exists.';
        } else {
            try {
                $newAvatarPath = null;

                if (isset($_FILES['avatar']) && is_array($_FILES['avatar'])) {
                    $newAvatarPath = save_admin_avatar($_FILES['avatar'], $id);
                }

                if ($newAvatarPath !== null) {
                    $updateStmt = $conn->prepare("
                        UPDATE usuarios 
                        SET nome = ?, usuario = ?, email = ?, role = ?, avatar_path = ?
                        WHERE id = ?
                    ");
                    $updateStmt->bind_param("sssssi", $nome, $usuario, $email, $role, $newAvatarPath, $id);
                } else {
                    $updateStmt = $conn->prepare("
                        UPDATE usuarios 
                        SET nome = ?, usuario = ?, email = ?, role = ?
                        WHERE id = ?
                    ");
                    $updateStmt->bind_param("ssssi", $nome, $usuario, $email, $role, $id);
                }

                if ($updateStmt->execute()) {
                    header("Location: admin.php");
                    exit();
                } else {
                    $message = 'Could not update user.';
                }

                $updateStmt->close();
            } catch (Exception $e) {
                $message = $e->getMessage();
            }
        }

        $checkStmt->close();
    }

    $user['nome'] = $nome;
    $user['usuario'] = $usuario;
    $user['email'] = $email;
    $user['role'] = $role;

    if (!empty($newAvatarPath)) {
        $user['avatar_path'] = $newAvatarPath;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EcoTracker | Edit User</title>
    <link rel="stylesheet" href="Admin.css">
</head>
<body class="admin-page">

<aside class="sidebar">
    <div class="brand">EcoTracker</div>

    <nav>
        <a href="admin.php">Dashboard</a>
        <a href="index.html">Website</a>
        <a href="Community.html">Community</a>
        <a href="Resources.html">Resources</a>
        <a href="Contact.html">Contact</a>
    </nav>

    <a href="logout.php" class="logout">Log out</a>
</aside>

<main class="admin-main">
    <section class="edit-wrapper">
        <div class="edit-card">
            <p class="eyebrow">Admin Panel</p>
            <h1>Edit User</h1>
            <p>Update account information, permissions, and profile picture.</p>

            <?php if ($message !== ''): ?>
                <p class="admin-error"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <form method="POST" class="edit-form" enctype="multipart/form-data">
                <div class="admin-avatar-editor">
                    <img 
                        src="<?php echo htmlspecialchars($user['avatar_path']); ?>" 
                        alt="Current avatar"
                        class="admin-edit-avatar"
                        onerror="this.onerror=null;this.src='default-avatar.svg'"
                    >

                    <div>
                        <label class="avatar-upload-label" for="avatar">Change profile picture</label>
                        <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/webp,image/gif">
                        <small>JPG, PNG, WEBP or GIF. Max 3MB.</small>
                    </div>
                </div>

                <div class="field">
                    <label>Name</label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
                </div>

                <div class="field">
                    <label>Username</label>
                    <input type="text" name="usuario" value="<?php echo htmlspecialchars($user['usuario']); ?>" required>
                </div>

                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="field">
                    <label>Role</label>
                    <select name="role">
                        <option value="user" <?php echo strtolower($user['role']) === 'user' ? 'selected' : ''; ?>>User</option>
                        <option value="admin" <?php echo strtolower($user['role']) === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>

                <div class="form-actions">
                    <a href="admin.php" class="btn-outline">Cancel</a>
                    <button type="submit" class="btn-fill">Save Changes</button>
                </div>
            </form>
        </div>
    </section>
</main>

</body>
</html>
<?php
$conn->close();
?>