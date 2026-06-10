<?php
require_once __DIR__ . '/../extras/csrf.php';

$mode = $_GET['mode'] ?? 'register';
$allowed = ['login', 'register'];
if (!in_array($mode, $allowed)) {
    $mode = 'register';
}
?>

<div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="w-full max-w-sm animate-slide-up">
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-4" style="background: var(--accent); color: white;">
                <i class="bi bi-camera"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">
                Fake<span style="color: var(--accent);">Social</span>
            </h1>
            <p class="text-muted text-sm mt-2"><?= htmlspecialchars($mode === 'login' ? 'Ingresa a tu cuenta' : 'Crea tu cuenta gratis') ?></p>
        </div>

        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="bg-red-600/15 border border-red-600/30 text-red-400 px-4 py-3 rounded-xl text-sm mb-4 flex items-center gap-2 animate-fade-in">
                <i class="bi bi-exclamation-circle"></i>
                <?= htmlspecialchars($_SESSION['login_error']) ?>
                <?php unset($_SESSION['login_error']); ?>
            </div>
        <?php endif; ?>

        <div class="card border rounded-2xl p-6 shadow-sm" style="border-color: var(--border);">
            <form action="<?= htmlspecialchars($mode === 'login' ? './core/auth/login.php' : './core/auth/register.php') ?>" method="post" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                <div>
                    <label class="block text-xs mb-1.5 text-muted font-medium">Usuario</label>
                    <input type="text" name="username" required placeholder="Tu usuario" class="w-full px-4 py-2.5 rounded-xl text-sm transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);">
                </div>

                <?php if ($mode === 'register'): ?>
                    <div>
                        <label class="block text-xs mb-1.5 text-muted font-medium">Email</label>
                        <input type="email" name="email" required placeholder="tu@email.com" class="w-full px-4 py-2.5 rounded-xl text-sm transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);">
                    </div>
                <?php endif; ?>

                <div>
                    <label class="block text-xs mb-1.5 text-muted font-medium">Contraseña</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl text-sm transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);">
                </div>

                <?php if ($mode === 'register'): ?>
                    <div>
                        <label class="block text-xs mb-1.5 text-muted font-medium">Confirmar contraseña</label>
                        <input type="password" name="password_confirm" required placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl text-sm transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);">
                    </div>
                <?php endif; ?>

                <button type="submit" class="w-full btn-accent py-2.5 rounded-xl font-semibold text-sm transition text-white shadow-lg shadow-indigo-500/20 hover:shadow-xl hover:shadow-indigo-500/30 flex items-center justify-center gap-2">
                    <?= htmlspecialchars($mode === 'login' ? 'Iniciar sesión' : 'Crear cuenta') ?>
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-sm text-muted">
            <?php if ($mode === 'login'): ?>
                ¿No tienes cuenta? <a href="?mode=register" style="color: var(--accent);" class="font-medium hover:underline">Regístrate</a>
            <?php else: ?>
                ¿Ya tienes cuenta? <a href="?mode=login" style="color: var(--accent);" class="font-medium hover:underline">Inicia sesión</a>
            <?php endif; ?>
        </p>
    </div>
</div>
