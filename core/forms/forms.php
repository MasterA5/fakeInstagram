<?php
$mode = $_GET['mode'] ?? 'register';
?>

<div class="min-h-[80vh] flex items-center justify-center">


<div class="w-full max-w-md p-8 bg-zinc-900 border border-zinc-800 rounded-2xl shadow-2xl">

    <!-- 🧠 Branding -->
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold tracking-tight">
            Fake<span class="text-indigo-500">Social</span>
        </h1>
    </div>

    <!-- 📝 Título -->
    <div class="mb-8 text-center">
        <h2 class="text-2xl font-semibold text-white">
            <?= $mode === 'login' ? 'Bienvenido' : 'Crear cuenta' ?>
        </h2>

        <p class="text-zinc-400 mt-2 text-sm">
            <?= $mode === 'login' ? 'Ingresa tus credenciales' : 'Regístrate para continuar' ?>
        </p>
    </div>

    <!-- 🔐 Form -->
    <form action="<?= $mode === 'login' ? './core/auth/login.php' : './core/auth/register.php' ?>" method="post" class="space-y-5">

        <!-- USERNAME -->
        <div>
            <label class="block text-xs mb-2 text-zinc-400 uppercase tracking-wide">Usuario</label>
            <input type="text" name="username" required
                class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-sm text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
        </div>

        <!-- EMAIL SOLO EN REGISTER -->
        <?php if ($mode === 'register'): ?>
            <div>
                <label class="block text-xs mb-2 text-zinc-400 uppercase tracking-wide">Email</label>
                <input type="email" name="email" required
                    class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-sm text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            </div>
        <?php endif; ?>

        <!-- PASSWORD -->
        <div>
            <label class="block text-xs mb-2 text-zinc-400 uppercase tracking-wide">Contraseña</label>
            <input type="password" name="password" required
                class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-sm text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
        </div>

        <!-- BOTÓN -->
        <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-500 py-3 rounded-lg font-semibold text-sm transition-all shadow-lg shadow-indigo-500/20">
            <?= $mode === 'login' ? 'Iniciar sesión' : 'Registrarse' ?>
        </button>
    </form>

    <!-- 🔁 Switch -->
    <p class="mt-6 text-center text-sm text-zinc-500">
        <?php if ($mode === 'login'): ?>
            ¿No tienes cuenta?
            <a href="?mode=register" class="text-indigo-400 hover:underline">Regístrate</a>
        <?php else: ?>
            ¿Ya tienes cuenta?
            <a href="?mode=login" class="text-indigo-400 hover:underline">Inicia sesión</a>
        <?php endif; ?>
    </p>

</div>

</div>
