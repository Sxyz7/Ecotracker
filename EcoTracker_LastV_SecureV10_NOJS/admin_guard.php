<?php
function normalize_role(string $role): string
{
    $normalized = strtolower(trim($role));

    if ($normalized === 'administrator') {
        return 'admin';
    }

    return $normalized;
}

function is_admin_role($role): bool
{
    return normalize_role((string) $role) === 'admin';
}

function refresh_admin_role_from_database(mysqli $conn, int $userId): bool
{
    $roleStmt = $conn->prepare("SELECT role FROM usuarios WHERE id = ? LIMIT 1");

    if (!$roleStmt) {
        return false;
    }

    $roleStmt->bind_param("i", $userId);
    $roleStmt->execute();
    $roleResult = $roleStmt->get_result();

    $isAdmin = false;

    if ($roleResult && $roleResult->num_rows === 1) {
        $dbUser = $roleResult->fetch_assoc();
        $isAdmin = is_admin_role($dbUser['role'] ?? '');

        if ($isAdmin) {
            $_SESSION['usuario_role'] = 'admin';
        }
    }

    $roleStmt->close();

    return $isAdmin;
}

function deny_access(bool $asJson = false): void
{
    http_response_code(403);

    if ($asJson) {
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Access denied']);
    } else {
        echo 'Access denied!';
    }

    exit();
}

function require_admin(mysqli $conn, bool $asJson = false): int
{
    $userId = isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : 0;

    if ($userId <= 0) {
        deny_access($asJson);
    }

    $isAdmin = is_admin_role($_SESSION['usuario_role'] ?? '');

    if (!$isAdmin) {
        $isAdmin = refresh_admin_role_from_database($conn, $userId);
    }

    if (!$isAdmin) {
        deny_access($asJson);
    }

    return $userId;
}
?>