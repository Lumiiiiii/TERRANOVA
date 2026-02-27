<?php
session_start();

// Password fissa per semplicità (come richiesto per esame/locale)
$PASSWORD_SEGRETA = 'naturopata';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    if ($password === $PASSWORD_SEGRETA) {
        // Password corretta!
        $_SESSION['logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Password errata. Riprova.';
    }
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TerraNova</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100">

    <div class="card shadow-lg p-4" style="width: 100%; max-width: 380px;">
        <div class="text-center mb-4">
            <div class="avatar-circle-lg mx-auto mb-3 bg-primary text-white" style="background-color: var(--color-primary) !important; color: white !important;">
                TN
            </div>
            <h1 class="h3 fw-bold mb-0">TerraNova</h1>
            <p class="text-muted small">Accesso riservato</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger text-center small py-2">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control form-control-lg"
                    placeholder="Inserisci password..." required autofocus>
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-lg fw-bold">
                Entra
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>