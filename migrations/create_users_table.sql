CREATE TABLE IF NOT EXISTS users (
   id INT AUTO_INCREMENT PRIMARY KEY,
   email VARCHAR(255) UNIQUE NOT NULL,
   password VARCHAR(255) NOT NULL
);

INSERT INTO users (email, password)
VALUES ('admin@example.com', '$2y$10$lIxNX3r6OKJ.TmUJo0BtkuKOt6xrdc.k29Bfp4FDkf.NiUhm8Bofu');
