# FakeSocial

Clon minimalista de Instagram construido con **vanilla PHP**, **MySQL (mysqli)** y **Tailwind CSS**. Interfaz en español.

## Características

- **Feed** con publicaciones, likes y comentarios en tiempo real (AJAX)
- **Perfiles** de usuario con foto, biografía, seguidores y grilla de posts
- **Seguir / Dejar de seguir** sin recarga de página
- **Crear, editar y eliminar** posts con upload de imágenes a imgBB
- **Comentarios** en modal (mobile: bottom sheet, desktop: side panel)
- **Scroll infinito** en el feed (carga 5 posts por lote)
- **Explorar** usuarios
- **Autenticación** con registro, login y logout
- **Temas** oscuro, claro, océano y atardecer con paleta personalizable
- **CSRF** token en todos los formularios
- **UUID v4** como IDs de registro
- **Diseño responsive** (mobile-first con Tailwind)

## Stack

| Componente | Tecnología |
|---|---|
| Backend | PHP 8.x (vanilla, sin frameworks) |
| Base de datos | MySQL con mysqli |
| Frontend | Tailwind CSS via CDN |
| Iconos | Bootstrap Icons |
| Fuente | Google Fonts (Inter) |
| Imágenes | imgBB API + fallback local en `uploads/posts/` |
| Avatares | DiceBear API |

## Requisitos

- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- WAMP / XAMPP / LAMP / similar
- Extensión cURL habilitada en PHP
- Composer (opcional, para seed data en Python)

## Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/MasterA5/fakeInstagram.git

# 2. Importar la base de datos
mysql -u root -p < fakeinstagram.sql

# 3. Configurar conexión en core/db/db.php
# 4. (Opcional) Configurar API key de imgBB en .env
#    API_KEY=tu_api_key

# 5. Iniciar el servidor
#    En WAMP: copiar a C:\wamp64\www\ y acceder via localhost
```

## Seed Data

El archivo `gen_users_post.py` genera datos de prueba:

```bash
python gen_users_post.py
```

Esto produce archivos SQL con ~300 usuarios, ~1650 posts, ~5200 follows, ~9000 likes y ~12000 comentarios.

## Estructura del Proyecto

```
├── index.php                     # Ruteo principal
├── components/
│   ├── appbar.php                # Barra de navegación superior
│   ├── post_card.php             # Componente de publicación
│   ├── profile_header.php        # Cabecera de perfil
│   ├── sidebar.php               # Sidebar con sugerencias
│   └── upload_card.php           # Formulario de creación de posts
├── core/
│   ├── auth/                     # Login, registro, logout
│   ├── db/db.php                 # Conexión a MySQL
│   ├── extras/                   # CSRF, UUID, temas, fechas
│   ├── feed/                     # Feed principal, lazy loading, explorar
│   ├── follow/                   # Lógica de seguir/dejar de seguir (AJAX)
│   ├── post/
│   │   ├── comments/             # CRUD de comentarios (AJAX)
│   │   ├── images/upload_image.php # Subida a imgBB
│   │   ├── create_post.php       # AJAX
│   │   ├── delete_post.php       # AJAX
│   │   ├── edit_post.php         # AJAX
│   │   ├── like/                 # Like/unlike (AJAX)
│   │   └── view.php              # Página individual de post
│   └── profile/                  # Edición perfil, settings, temas
├── uploads/posts/                # Imágenes subidas localmente
├── gen_users_post.py             # Generador de datos de prueba
└── .env                           # API keys
```

## API Endpoints (AJAX)

| Endpoint | Método | Descripción |
|---|---|---|
| `core/follow/follow.php` | POST | Seguir / dejar de seguir |
| `core/post/create_post.php` | POST | Crear publicación |
| `core/post/delete_post.php` | POST | Eliminar publicación |
| `core/post/edit_post.php` | POST | Editar publicación |
| `core/post/like/like.php` | POST | Like / unlike |
| `core/post/comments/create_comment.php` | POST | Crear comentario |
| `core/post/comments/delete_comment.php` | POST | Eliminar comentario |
| `core/post/comments/get_comments.php` | GET | Obtener comentarios |
| `core/feed/load_posts.php` | GET | Carga paginada de posts |

## Licencia

MIT
