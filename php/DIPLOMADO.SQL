-- Tabla de categorías principales
CREATE TABLE categorias (
    categoria_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

-- Tabla de subcategorías
CREATE TABLE subcategorias (
    subcategoria_id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT,
    nombre VARCHAR(50) NOT NULL,
    FOREIGN KEY (categoria_id) REFERENCES categorias(categoria_id)
);

-- Tabla de productos
CREATE TABLE productos (
    producto_id INT AUTO_INCREMENT PRIMARY KEY,
    subcategoria_id INT,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(10, 2),
    stock INT,
    FOREIGN KEY (subcategoria_id) REFERENCES subcategorias(subcategoria_id)
);

INSERT INTO categorias (nombre) VALUES ('Celulares'), ('Tablets'), ('Accesorios');

-- Celulares
INSERT INTO subcategorias (categoria_id, nombre) VALUES 
(1, 'Gama Alta'), (1, 'Gama Media'), (1, 'Gama Baja');

-- Tablets
INSERT INTO subcategorias (categoria_id, nombre) VALUES 
(2, 'Gama Alta'), (2, 'Gama Media'), (2, 'Gama Baja');

-- Accesorios
INSERT INTO subcategorias (categoria_id, nombre) VALUES 
(3, 'Gama Alta'), (3, 'Gama Media'), (3, 'Gama Baja');

-- Productos para Celulares Gama Alta
INSERT INTO productos (subcategoria_id, nombre, precio, stock) VALUES 
(1, 'iPhone 15 Pro Max', 1299.99, 20), 
(1, 'Samsung Galaxy S23 Ultra', 1199.99, 15);

-- Productos para Celulares Gama Media
INSERT INTO productos (subcategoria_id, nombre, precio, stock) VALUES 
(2, 'OnePlus Nord 2', 399.99, 30), 
(2, 'Samsung Galaxy A54', 449.99, 25);

-- Productos para Celulares Gama Baja
INSERT INTO productos (subcategoria_id, nombre, precio, stock) VALUES 
(3, 'Xiaomi Redmi 12', 199.99, 50), 
(3, 'Motorola G Power', 179.99, 45);

-- Productos para Tablets Gama Alta
INSERT INTO productos (subcategoria_id, nombre, precio, stock) VALUES 
(4, 'iPad Pro 12.9"', 1099.99, 10), 
(4, 'Samsung Galaxy Tab S9', 999.99, 12);

-- Productos para Tablets Gama Media
INSERT INTO productos (subcategoria_id, nombre, precio, stock) VALUES 
(5, 'iPad Air', 599.99, 20), 
(5, 'Samsung Galaxy Tab S7', 649.99, 18);

-- Productos para Tablets Gama Baja
INSERT INTO productos (subcategoria_id, nombre, precio, stock) VALUES 
(6, 'Amazon Fire HD 10', 149.99, 40), 
(6, 'Lenovo Tab M10', 129.99, 35);

-- Productos para Accesorios Gama Alta
INSERT INTO productos (subcategoria_id, nombre, precio, stock) VALUES 
(7, 'AirPods Pro', 249.99, 50), 
(7, 'Samsung Galaxy Buds Pro', 199.99, 40);

-- Productos para Accesorios Gama Media
INSERT INTO productos (subcategoria_id, nombre, precio, stock) VALUES 
(8, 'JBL Tune 225', 99.99, 60), 
(8, 'Sony WH-CH710N', 129.99, 55);

-- Productos para Accesorios Gama Baja
INSERT INTO productos (subcategoria_id, nombre, precio, stock) VALUES 
(9, 'Xiaomi Mi Band 7', 49.99, 100), 
(9, 'Anker Soundcore', 39.99, 80);


SELECT c.nombre AS categoria, sc.nombre AS subcategoria, p.nombre AS producto, p.precio, p.stock
FROM categorias c
JOIN subcategorias sc ON c.categoria_id = sc.categoria_id
JOIN productos p ON sc.subcategoria_id = p.subcategoria_id
ORDER BY c.nombre, sc.nombre, p.nombre;

SELECT p.nombre AS producto, p.precio, p.stock
FROM productos p
ORDER BY p.nombre;

