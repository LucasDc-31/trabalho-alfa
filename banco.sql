CREATE DATABASE IF NOT EXISTS crud_php CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE crud_php;

CREATE TABLE IF NOT EXISTS clientes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    telefone VARCHAR(20) NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO clientes (nome, email, telefone) VALUES
('Maria Silva', 'maria@exemplo.com', '(44) 99999-1111'),
('João Souza', 'joao@exemplo.com', '(44) 98888-2222');
