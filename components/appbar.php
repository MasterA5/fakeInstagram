<header class="sticky top-0 z-50 bg-zinc-900/80 backdrop-blur border-b border-zinc-800">
    <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">

        <span class="text-xl font-bold tracking-tight">
            Fake<span class="text-indigo-500">Social</span>
        </span>

        <div class="flex items-center gap-3">
            <?php if ($logged): ?>
                <span class="text-xs text-green-400 hidden sm:block">
                    Conectado
                </span>

                <a href="./core/auth/logout.php"
                    class="bg-zinc-800 hover:bg-red-600/80 px-3 py-1.5 rounded-lg text-sm transition">
                    Salir
                </a>
            <?php endif; ?>
        </div>

    </div>
</header>