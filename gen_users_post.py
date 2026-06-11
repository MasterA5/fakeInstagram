import httpx
import uuid
import random
from datetime import datetime, timedelta

CAPTIONS = [
    "Atardecer increíble hoy",
    "Nueva aventura, nuevo lugar",
    "Good vibes only",
    "Momento para recordar",
    "Así empezó el día",
    "Nada mejor que esto",
    "En mode relax",
    "Capturando el momento",
    "Sonrisas y buenos momentos",
    "Domingo de sol",
    "Así me gusta la vida",
    "Pequeños placeres",
    "#nofilter",
    "Con mis favoritos",
    "Así se hace",
    "Una de esas noches",
    "Brunch del domingo",
    "En modo viaje",
    "Pura felicidad",
    "Así estamos",
    "Mañana de café",
    "Con vistas al mar",
    "Disfrutando el proceso",
    "Atardecer desde aquí",
    "Así es mi mundo",
    "Nunca es suficiente",
    # English ones too
    "Living my best life",
    "Sunset chaser",
    "Weekend mode on",
    "Just another day in paradise",
]

IMAGES = [
    "https://picsum.photos/seed/{seed}/600/600",
    "https://picsum.photos/seed/{seed}/600/600",
    "https://picsum.photos/seed/{seed}/600/600",
]


def random_date(days_back=60):
    return datetime.now() - timedelta(
        days=random.randint(0, days_back),
        hours=random.randint(0, 23),
        minutes=random.randint(0, 59),
    )


def esc(val):
    """Escape single quotes for SQL values"""
    return str(val).replace("'", "''")


async def generate_users():
    async with httpx.AsyncClient() as client:
        response = await client.get("https://randomuser.me/api/?results=300")
        return response.json()


def generate_users_sql(users):
    users_sql = []
    posts_sql = []
    follows_sql = []
    user_ids = []

    all_post_ids = []

    for user in users:
        user_id = uuid.uuid4()
        user_ids.append(str(user_id))
        username = user["login"]["username"]
        email = user["email"]
        first = user["name"]["first"]
        last = user["name"]["last"]
        display = f"{first} {last}"
        bio = f"Bio de {display}"
        avatar = user["picture"]["medium"]

        users_sql.append(
            f"INSERT INTO users (id, username, email, password, display_name, bio, avatar) VALUES "
            f"('{user_id}', '{esc(username)}', '{esc(email)}', 'password123', '{esc(display)}', '{esc(bio)}', '{esc(avatar)}');"
        )

        # Generate 1-10 posts per user
        for _ in range(random.randint(1, 10)):
            post_id = uuid.uuid4()
            caption = random.choice(CAPTIONS)
            image = random.choice(IMAGES).format(seed=post_id)
            created = random_date()

            all_post_ids.append(str(post_id))
            posts_sql.append(
                f"INSERT INTO posts (id, user_id, content, image, created_at) VALUES "
                f"('{post_id}', '{user_id}', '{esc(caption)}', '{image}', '{created.strftime('%Y-%m-%d %H:%M:%S')}');"
            )

    # Generate follows (each user follows 5-30 random users)
    for uid in user_ids:
        targets = random.sample(
            [u for u in user_ids if u != uid],
            k=min(random.randint(5, 30), len(user_ids) - 1),
        )
        for target in targets:
            created = random_date(30)
            follows_sql.append(
                f"INSERT IGNORE INTO follows (follower_id, followed_id, created_at) VALUES "
                f"('{uid}', '{target}', '{created.strftime('%Y-%m-%d %H:%M:%S')}');"
            )

    # Generate likes (each user likes 10-50 random posts)
    likes_sql = []
    for uid in user_ids:
        sample_size = min(random.randint(10, 50), len(all_post_ids))
        chosen = random.sample(all_post_ids, sample_size)
        for post_id in chosen:
            created = random_date(30)
            likes_sql.append(
                f"INSERT IGNORE INTO likes (id, post_id, user_id, created_at) VALUES "
                f"('{uuid.uuid4()}', '{post_id}', '{uid}', '{created.strftime('%Y-%m-%d %H:%M:%S')}');"
            )

    with open("users.sql", "w", encoding="utf-8") as f:
        f.write("\n".join(users_sql))
        print(f"  users.sql -> {len(users_sql)} usuarios")

    with open("posts.sql", "w", encoding="utf-8") as f:
        f.write("\n".join(posts_sql))
        print(f"  posts.sql -> {len(posts_sql)} posts")

    with open("follows.sql", "w", encoding="utf-8") as f:
        f.write("\n".join(follows_sql))
        print(f"  follows.sql -> {len(follows_sql)} follows")

    with open("likes.sql", "w", encoding="utf-8") as f:
        f.write("\n".join(likes_sql))
        print(f"  likes.sql -> {len(likes_sql)} likes")


if __name__ == "__main__":
    import asyncio

    print("Generando usuarios desde randomuser.me...")
    data = asyncio.run(generate_users())
    print(f"Obtenidos {len(data['results'])} usuarios")
    print("Generando SQL...")
    generate_users_sql(data["results"])
    print("¡Listo!")
