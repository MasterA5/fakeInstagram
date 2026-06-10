CREATE DATABASE IF NOT EXISTS fakeInstagram;
USE fakeInstagram;

CREATE TABLE users (
    id VARCHAR(128) PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    display_name VARCHAR(100),
    bio TEXT,
    avatar TEXT DEFAULT 'https://api.dicebear.com/7.x/avataaars/svg?seed=default',
    theme VARCHAR(20) DEFAULT 'dark',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
    id VARCHAR(128) PRIMARY KEY,
    user_id VARCHAR(128) NOT NULL,
    content TEXT NOT NULL,
    image TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE comments (
    id VARCHAR(128) PRIMARY KEY,
    post_id VARCHAR(128) NOT NULL,
    user_id VARCHAR(128) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE likes (
    id VARCHAR(128) PRIMARY KEY,
    post_id VARCHAR(128) NOT NULL,
    user_id VARCHAR(128) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, post_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE follows (
    follower_id VARCHAR(128) NOT NULL,
    followed_id VARCHAR(128) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (follower_id, followed_id),
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (followed_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Add new columns to existing users table if upgrading
ALTER TABLE users ADD COLUMN display_name VARCHAR(100);
ALTER TABLE users ADD COLUMN bio TEXT;
ALTER TABLE users ADD COLUMN avatar TEXT DEFAULT 'https://api.dicebear.com/7.x/avataaars/svg?seed=default';
ALTER TABLE users ADD COLUMN theme VARCHAR(20) DEFAULT 'dark';
