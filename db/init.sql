-- Runs automatically on first start of the MySQL container
-- (the database itself is created from MYSQL_DATABASE).

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL          -- bcrypt hash
);

CREATE TABLE IF NOT EXISTS images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    image_name VARCHAR(255) NOT NULL,       -- random stored file name
    title VARCHAR(100) NOT NULL,            -- display name (editable)
    image_size BIGINT NOT NULL,
    image_type VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
