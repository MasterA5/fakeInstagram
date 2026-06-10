<?php
$userId = $_SESSION['user_id'] ?? '';
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("s", $userId);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
if (!$profile) {
    $profile = [];
}
$currentTab = (string)($_GET['tab'] ?? 'profile');
?>
<div class="max-w-2xl mx-auto animate-slide-up">
    <div class="card border rounded-2xl p-6 shadow-sm" style="border-color: var(--border);">
        <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
            <i class="bi bi-gear" style="color: var(--accent);"></i>
            Configuración
        </h2>

        <div class="flex gap-2 mb-6 p-1 rounded-xl" style="background: var(--bg-primary);">
            <a href="?settings&tab=profile" class="flex-1 px-4 py-2 rounded-lg text-sm font-medium text-center transition-all <?= $currentTab === 'profile' ? 'text-white shadow-sm' : 'text-muted hover:text-white' ?>" style="<?= $currentTab === 'profile' ? 'background: var(--accent);' : '' ?>">
                <i class="bi bi-person mr-1"></i> Perfil
            </a>
            <a href="?settings&tab=theme" class="flex-1 px-4 py-2 rounded-lg text-sm font-medium text-center transition-all <?= $currentTab === 'theme' ? 'text-white shadow-sm' : 'text-muted hover:text-white' ?>" style="<?= $currentTab === 'theme' ? 'background: var(--accent);' : '' ?>">
                <i class="bi bi-palette mr-1"></i> Tema
            </a>
            <a href="?settings&tab=account" class="flex-1 px-4 py-2 rounded-lg text-sm font-medium text-center transition-all <?= $currentTab === 'account' ? 'text-white shadow-sm' : 'text-muted hover:text-white' ?>" style="<?= $currentTab === 'account' ? 'background: var(--accent);' : '' ?>">
                <i class="bi bi-shield mr-1"></i> Cuenta
            </a>
        </div>

        <?php if ($currentTab === 'theme'): ?>
            <div class="space-y-4">
                <p class="text-sm text-muted">Elige un tema para personalizar tu experiencia</p>
                <form action="./core/profile/theme.php" method="POST" class="space-y-3">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <div class="grid grid-cols-2 gap-3">
                        <?php
                        $themes = [
                            'dark' => ['Oscuro', '#000', 'bg-zinc-900'],
                            'light' => ['Claro', '#fff', 'bg-white'],
                            'ocean' => ['Oceano', '#0f172a', 'bg-slate-900'],
                            'sunset' => ['Atardecer', '#1c1917', 'bg-stone-900'],
                        ];
                        foreach ($themes as $key => [$label, $color, $bg]):
                        ?>
                            <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all <?= ($profile['theme'] ?? 'dark') === $key ? 'border-indigo-500 bg-indigo-500/10 ring-1 ring-indigo-500' : 'hover:border-zinc-500' ?>" style="border-color: <?= ($profile['theme'] ?? 'dark') === $key ? '' : 'var(--border)' ?>;">
                                <input type="radio" name="theme" value="<?= $key ?>" <?= ($profile['theme'] ?? 'dark') === $key ? 'checked' : '' ?> class="sr-only" onchange="this.form.submit()">
                                <div class="w-9 h-9 rounded-lg border-2 flex items-center justify-center <?= $bg ?>" style="border-color: var(--border);">
                                    <?php if (($profile['theme'] ?? 'dark') === $key): ?>
                                        <i class="bi bi-check text-indigo-400 text-sm"></i>
                                    <?php endif; ?>
                                </div>
                                <span class="text-sm font-medium"><?= $label ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" class="btn-accent px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition shadow-lg shadow-indigo-500/20">Aplicar tema</button>
                </form>
            </div>
        <?php elseif ($currentTab === 'account'): ?>
            <div class="space-y-4">
                <div class="bg-red-600/10 border border-red-600/20 rounded-xl p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-600/20 flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-exclamation-triangle text-red-400"></i>
                        </div>
                        <div>
                            <h3 class="text-red-400 font-semibold text-sm">Eliminar cuenta</h3>
                            <p class="text-xs text-muted mt-1">Esta acción eliminará todos tus datos, publicaciones y comentarios permanentemente. No se puede deshacer.</p>
                            <form action="./core/profile/delete_account.php" method="POST" onsubmit="return confirm('¿Estás completamente seguro? Esta acción es irreversible.')">
                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                <button type="submit" class="mt-3 bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-xl text-sm transition flex items-center gap-1.5">
                                    <i class="bi bi-trash"></i> Eliminar cuenta
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php include(__DIR__ . '/edit.php'); ?>
        <?php endif; ?>
    </div>
</div>
