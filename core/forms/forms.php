<?php
require_once __DIR__ . '/../extras/csrf.php';

$mode = $_GET['mode'] ?? 'register';
$allowed = ['login', 'register'];
if (!in_array($mode, $allowed)) {
    $mode = 'register';
}
?>

<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="w-full max-w-sm animate-slide-up">
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl font-bold mx-auto mb-4" style="background: var(--accent); color: white;">
                <i class="bi bi-camera"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">
                Fake<span style="color: var(--accent);">Social</span>
            </h1>
            <p class="text-muted text-sm mt-1"><?= htmlspecialchars($mode === 'login' ? 'Inicia sesión' : 'Regístrate para ver fotos de tus amigos') ?></p>
        </div>

        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="bg-red-600/15 border border-red-600/30 text-red-400 px-4 py-3 rounded-xl text-sm mb-4 flex items-center gap-2 animate-fade-in">
                <i class="bi bi-exclamation-circle"></i>
                <?= htmlspecialchars($_SESSION['login_error']) ?>
                <?php unset($_SESSION['login_error']); ?>
            </div>
        <?php endif; ?>

        <div class="card border rounded-xl p-5 shadow-sm" style="border-color: var(--border);">
            <form action="<?= htmlspecialchars($mode === 'login' ? './core/auth/login.php' : './core/auth/register.php') ?>" method="post" class="space-y-3">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                <div>
                    <input type="text" name="username" required placeholder="Usuario" class="w-full px-3 py-2.5 rounded-lg text-sm transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);">
                </div>

                <?php if ($mode === 'register'): ?>
                    <div>
                        <input type="email" name="email" required placeholder="Email" class="w-full px-3 py-2.5 rounded-lg text-sm transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);">
                    </div>
                <?php endif; ?>
                
                <?php if ($mode === 'register'): ?>
                    <div>
                        <input type="text" name="display_name" required placeholder="Nombre Completo" class="w-full px-3 py-2.5 rounded-lg text-sm transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);">
                    </div>
                <?php endif; ?>

                <div>
                    <input type="password" name="password" required placeholder="Contraseña" class="w-full px-3 py-2.5 rounded-lg text-sm transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);">
                </div>

                <?php if ($mode === 'register'): ?>
                    <div>
                        <input type="password" name="password_confirm" required placeholder="Confirmar contraseña" class="w-full px-3 py-2.5 rounded-lg text-sm transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);">
                    </div>
                <?php endif; ?>

                <button type="submit" class="w-full py-2.5 rounded-lg font-semibold text-sm transition text-white" style="background: var(--accent);">
                    <?= htmlspecialchars($mode === 'login' ? 'Entrar' : 'Registrarse') ?>
                </button>
            </form>
        </div>

        <p class="mt-4 text-center text-sm text-muted">
            <?php if ($mode === 'login'): ?>
                ¿No tienes cuenta? <a href="?mode=register" style="color: var(--accent);" class="font-semibold">Regístrate</a>
            <?php else: ?>
                ¿Tienes cuenta? <a href="?mode=login" style="color: var(--accent);" class="font-semibold">Entra</a>
            <?php endif; ?>
        </p>
    </div>
</div>