-- MySQL Database Schema for Ocean Audio Video DVD Shop

-- Table: dvds
CREATE TABLE IF NOT EXISTS dvds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    language VARCHAR(50) NOT NULL,
    genre VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image_path VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table: sales
CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dvd_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    sale_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sales_dvd
        FOREIGN KEY (dvd_id) REFERENCES dvds(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insert sample data
INSERT INTO dvds (title, language, genre, year, price, stock, image_path) VALUES
('The Matrix', 'english', 'scifi', 1999, 1500.00, 10, 'uploads/matrix.jpg'),
('Inception', 'english', 'scifi', 2010, 1800.00, 8, 'uploads/inception.jpg'),
('Titanic', 'english', 'romance', 1997, 1200.00, 15, 'uploads/titanic.jpg'),
('Bahubali', 'tamil', 'action', 2015, 1000.00, 5, 'uploads/bahubali.jpg'),
('Parasite', 'korean', 'drama', 2019, 2000.00, 3, 'uploads/parasite.jpg');

-- Insert sample sales data
INSERT INTO sales (dvd_id, quantity, price, total, sale_date) VALUES
(1, 2, 1500.00, 3000.00, '2024-11-08'),
(2, 1, 1800.00, 1800.00, '2024-11-09'),
(3, 3, 1200.00, 3600.00, '2024-11-09');
