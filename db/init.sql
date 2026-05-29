CREATE DATABASE IF NOT EXISTS db_facturacion;
USE db_facturacion;

-- Módulo Razón Social (Datos de la empresa)
CREATE TABLE razon_social (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ruc VARCHAR(20) NOT NULL,
    nombre_empresa VARCHAR(100) NOT NULL,
    direccion VARCHAR(255)
);

-- Módulo Registro Usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    rol VARCHAR(50) DEFAULT 'Vendedor'
);

-- Módulo Registro Clientes
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identificacion VARCHAR(20) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100)
);

-- Módulo Registro Categoría_producto
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(100) NOT NULL
);

-- Módulo Registro productos
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_categoria INT,
    nombre_producto VARCHAR(100) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id)
);

-- Módulo Facturación y Reportes
CREATE TABLE facturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id)
);

-- Datos iniciales de prueba
INSERT INTO usuarios (nombre, correo, rol) VALUES ('Admin', 'admin@empresa.com', 'Administrador');
INSERT INTO razon_social (ruc, nombre_empresa, direccion) VALUES ('0999999999001', 'Mi Empresa S.A.', 'Av. Principal 123');
