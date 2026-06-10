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
$userPalette = json_decode($profile['palette'] ?? '{}', true) ?: [];
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
                <p class="text-xs text-muted mb-4">Selecciona un tema y personaliza los colores debajo</p>
                <form action="./core/profile/theme.php" method="POST" id="themeForm">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="palette" id="paletteInput" value="<?= htmlspecialchars($profile['palette'] ?? '') ?>">

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-6">
                        <?php
                        $themes = [
                            'dark' => ['Oscuro', '#000'],
                            'light' => ['Claro', '#fff'],
                            'ocean' => ['Oceano', '#0f172a'],
                            'sunset' => ['Atardecer', '#1c1917'],
                        ];
                        foreach ($themes as $key => [$label, $color]):
                        ?>
                            <label class="flex flex-col items-center gap-2 p-3 rounded-xl border cursor-pointer transition-all <?= ($profile['theme'] ?? 'dark') === $key ? 'ring-2' : 'hover:opacity-80' ?>" style="border-color: <?= ($profile['theme'] ?? 'dark') === $key ? 'var(--accent)' : 'var(--border)' ?>; <?= ($profile['theme'] ?? 'dark') === $key ? 'box-shadow: 0 0 0 2px var(--accent);' : '' ?>">
                                <input type="radio" name="theme" value="<?= $key ?>" <?= ($profile['theme'] ?? 'dark') === $key ? 'checked' : '' ?> class="sr-only" onchange="this.form.submit()">
                                <div class="w-8 h-8 rounded-lg border-2 flex items-center justify-center" style="background: <?= $color ?>; border-color: var(--border);">
                                    <?php if (($profile['theme'] ?? 'dark') === $key): ?>
                                        <i class="bi bi-check text-sm" style="color: <?= $key === 'light' ? '#000' : '#fff' ?>;"></i>
                                    <?php endif; ?>
                                </div>
                                <span class="text-xs font-medium"><?= $label ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div style="border-top: 1px solid var(--border);" class="pt-4">
                        <h4 class="text-xs font-semibold text-muted uppercase tracking-wider mb-3">Personalizar colores</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <?php
                            $colorFields = [
                                '--bg-primary' => 'Fondo principal',
                                '--bg-card' => 'Fondo tarjeta',
                                '--border' => 'Bordes',
                                '--text-primary' => 'Texto principal',
                                '--text-secondary' => 'Texto secundario',
                                '--accent' => 'Color acento',
                                '--danger' => 'Peligro',
                                '--success' => 'Éxito',
                            ];
                            $base = getBaseTheme($profile['theme'] ?? 'dark');
                            foreach ($colorFields as $var => $label):
                                $val = $userPalette[$var] ?? $base[$var];
                            ?>
                                <div>
                                    <label class="block text-[10px] text-muted mb-1"><?= $label ?></label>
                                    <div class="flex items-center gap-1.5">
                                        <input type="color" value="<?= $val ?>" class="w-8 h-8 rounded-lg border-0 cursor-pointer p-0 bg-transparent palette-color" data-var="<?= $var ?>">
                                        <input type="text" value="<?= $val ?>" class="flex-1 px-2 py-1.5 rounded-lg text-xs transition palette-input" data-var="<?= $var ?>" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary); font-family: monospace;">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="flex gap-2 mt-5">
                            <button type="button" id="savePalette" class="px-5 py-2 rounded-lg text-xs font-semibold text-white transition" style="background: var(--accent);">
                                Guardar paleta
                            </button>
                            <button type="button" id="resetPalette" class="px-4 py-2 rounded-lg text-xs transition" style="background: var(--bg-card); border: 1px solid var(--border); color: var(--text-secondary);">
                                Restablecer
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <script>
            document.getElementById('savePalette')?.addEventListener('click', function() {
                const palette = {};
                document.querySelectorAll('.palette-input').forEach(inp => {
                    palette[inp.dataset.var] = inp.value;
                });
                document.getElementById('paletteInput').value = JSON.stringify(palette);
                document.getElementById('themeForm').submit();
            });

            document.querySelectorAll('.palette-color').forEach(input => {
                input.addEventListener('input', function() {
                    const textInput = this.closest('div').querySelector('.palette-input');
                    if (textInput) textInput.value = this.value;
                });
            });

            document.querySelectorAll('.palette-input').forEach(input => {
                input.addEventListener('input', function() {
                    const colorInput = this.closest('div').querySelector('.palette-color');
                    if (colorInput && /^#[0-9a-fA-F]{6}$/.test(this.value)) colorInput.value = this.value;
                });
            });

            document.getElementById('resetPalette')?.addEventListener('click', function() {
                document.getElementById('paletteInput').value = '';
                document.getElementById('themeForm').submit();
            });
            </script>

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
