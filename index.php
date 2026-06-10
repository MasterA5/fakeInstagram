<?php
session_start();
include("./core/db/db.php");

// NOTE[😢]: Estilos de tailwind generado con IA por falta de experiencia
// NOTE: Porfavor, no me hagan usar php nunca mas en la vida 🥀
// TODO[☑]: Implementar tailwind css para mejores estilos
// TODO[❌]:  Aprender tailwind (Fallido)
// NOTE: Gracias a ChatGPT por elegir el nombre de la red social
// TODO: usamos STMT como gestor de Querys para evitar inyeccion SQL

// ¿este usuario esta logueado?
$logged = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FakeSocial</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

</head>

<body class="bg-black text-white">

    <!-- APPBAR -->
    <!-- TODO[☑]: Añadir un appbar estetico, en lo posible que sea estilo instagram -->
    <!-- TODO[☑]: Pensar un buen nombre para este proyecto -->
    <?php include("./components/appbar.php"); ?>

    <div class="max-w-4xl mx-auto p-4 space-y-6">

        <!-- si el usuario esta loggeado este puede ver el boton de cerrar sesion -->
        <?php if (isset($_SESSION['user_id'])): ?>

            <!-- 📝 Crear post -->
            <!-- 📝 TODO[☑]: Mejorar la tarjeta visual de los posts -->
            <!-- 📝 TODO[⌛]: Agregar mas informacion a los posts (base de datos y html) -->
            <?php include("./components/upload_card.php"); ?>
            
            <!-- 📰 Feed o Publicaciones Solo visibles si el usuario incio session -->
            <!-- 📰 TODO[⌛]: hacer que se pueda ver bien la informacion del ususario que publico el post -->
            <?php include("./core/feed/test.php"); ?>

        <?php else: ?>

            <!-- 🔐 Inciar Sesion/Registrar -->
            <!-- 🔐 TODO[⌛]: Mejorar la validacion de los campos de entrada -->
            <!-- 🔐 TODO[⌛]: Hacer Mas extensa el registro del usuario -->
            <?php include("./core/forms/forms.php"); ?>

        <?php endif; ?>

    </div>

</body>

</html>