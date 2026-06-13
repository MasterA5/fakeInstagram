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
<div class="px-4 sm:px-0 pt-4 sm:pt-0 animate-slide-up">
    <div class="max-w-xl mx-auto">
        <h2 class="text-lg font-semibold mb-5 flex items-center gap-2">
            <i class="bi bi-gear" style="color: var(--accent);"></i>
            Configuración
        </h2>

        <div class="flex gap-1 mb-6 p-0.5 rounded-lg" style="background: var(--bg-card);">
            <a href="?settings&tab=profile" class="flex-1 px-3 py-2 rounded-md text-xs font-medium text-center transition-all <?= $currentTab === 'profile' ? 'text-white shadow-sm' : 'text-muted hover:text-[var(--text-primary)]' ?>" style="<?= $currentTab === 'profile' ? 'background: var(--accent);' : '' ?>">
                <i class="bi bi-person mr-1"></i> Perfil
            </a>
            <a href="?settings&tab=theme" class="flex-1 px-3 py-2 rounded-md text-xs font-medium text-center transition-all <?= $currentTab === 'theme' ? 'text-white shadow-sm' : 'text-muted hover:text-[var(--text-primary)]' ?>" style="<?= $currentTab === 'theme' ? 'background: var(--accent);' : '' ?>">
                <i class="bi bi-palette mr-1"></i> Tema
            </a>
            <a href="?settings&tab=account" class="flex-1 px-3 py-2 rounded-md text-xs font-medium text-center transition-all <?= $currentTab === 'account' ? 'text-white shadow-sm' : 'text-muted hover:text-[var(--text-primary)]' ?>" style="<?= $currentTab === 'account' ? 'background: var(--accent);' : '' ?>">
                <i class="bi bi-shield mr-1"></i> Cuenta
            </a>
        </div>

        <?php if ($currentTab === 'theme'): ?>
            <div class="card border rounded-xl p-5 shadow-sm" style="border-color: var(--border);">
                <h3 class="font-semibold text-sm mb-1">Tema base</h3>
                <p class="text-xs text-muted mb-4">Selecciona un tema predefinido</p>
                <form action="./core/profile/theme.php" method="POST" id="themeForm">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <?php
                        $themes = [
                            'dark'    => ['Oscuro',     '#000000', '#6366f1'],
                            'light'   => ['Claro',      '#fafafa', '#6366f1'],
                            'ocean'   => ['Oceano',     '#0f172a', '#06b6d4'],
                            'sunset'  => ['Atardecer',  '#1c1917', '#f97316'],
                            'forest'  => ['Bosque',     '#0a0f0a', '#22c55e'],
                            'rose'    => ['Rosa',       '#1a0a0f', '#ec4899'],
                            'midnight'=> ['Medianoche', '#050a14', '#3b82f6'],
                            'amber'   => ['Ámbar',      '#141008', '#f59e0b'],
                        ];
                        foreach ($themes as $key => [$label, $bg, $accent]):
                        ?>
                            <label class="flex flex-col items-center gap-2 p-3 rounded-xl border cursor-pointer transition-all <?= ($profile['theme'] ?? 'dark') === $key ? 'ring-2' : 'hover:opacity-80' ?>" style="border-color: <?= ($profile['theme'] ?? 'dark') === $key ? 'var(--accent)' : 'var(--border)' ?>; <?= ($profile['theme'] ?? 'dark') === $key ? 'box-shadow: 0 0 0 2px var(--accent);' : '' ?>">
                                <input type="radio" name="theme" value="<?= $key ?>" <?= ($profile['theme'] ?? 'dark') === $key ? 'checked' : '' ?> class="sr-only" onchange="this.form.submit()">
                                <div class="w-10 h-10 rounded-xl border-2 flex items-center justify-center relative overflow-hidden" style="background: <?= $bg ?>; border-color: <?= $bg === '#fafafa' ? '#e4e4e7' : 'transparent' ?>;">
                                    <div class="w-4 h-4 rounded-full" style="background: <?= $accent ?>; box-shadow: 0 0 6px <?= $accent ?>80;"></div>
                                    <?php if (($profile['theme'] ?? 'dark') === $key): ?>
                                        <div class="absolute inset-0 flex items-center justify-center" style="background: rgba(0,0,0,0.3);">
                                            <i class="bi bi-check text-white text-sm"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <span class="text-xs font-medium"><?= $label ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </form>

        <?php elseif ($currentTab === 'account'): ?>
            <div class="card border rounded-xl p-5 shadow-sm" style="border-color: var(--border);">
                <div class="p-4 rounded-xl" style="background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.2);">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(239, 68, 68, 0.15);">
                            <i class="bi bi-exclamation-triangle" style="color: #ef4444;"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold" style="color: #ef4444;">Eliminar cuenta</h3>
                            <p class="text-xs text-muted mt-1">Esta acción eliminará todos tus datos permanentemente.</p>
                            <form action="./core/profile/delete_account.php" method="POST" onsubmit="return confirm('¿Estás completamente seguro? Esta acción es irreversible.')">
                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                <button type="submit" class="mt-3 px-4 py-2 rounded-lg text-xs font-semibold text-white transition" style="background: #ef4444;">
                                    <i class="bi bi-trash mr-1"></i> Eliminar cuenta
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card border rounded-xl p-5 shadow-sm" style="border-color: var(--border);">
                <?php include(__DIR__ . '/edit.php'); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
