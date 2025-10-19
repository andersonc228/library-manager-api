CREATE TABLE IF NOT EXISTS books (
     id INT AUTO_INCREMENT PRIMARY KEY,
     title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) UNIQUE,
    year INT,
    description TEXT,
    cover_url VARCHAR(512),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT INTO books (title, author, isbn, year, description, cover_url) VALUES
('1984', 'George Orwell', '0451526538', 1949, 'Distopía política sobre vigilancia y control social.', NULL),
('El Principito', 'Antoine de Saint-Exupéry', '9780156012195', 1943, 'Obra filosófica y poética sobre la inocencia y la sabiduría.', NULL),
('To Kill a Mockingbird', 'Harper Lee', '9780060935467', 1960, 'Clásico sobre justicia y racismo en el sur de EE. UU.', NULL),
('Fahrenheit 451', 'Ray Bradbury', '9781451673319', 1953, 'Sociedad donde los libros están prohibidos.', NULL),
('Cien años de soledad', 'Gabriel García Márquez', '9780307474728', 1967, 'Saga familiar mágica en Macondo.', NULL);
