-- Banco de Dados para Plataforma de Rifas
-- Criado em: <?= date('Y-m-d H:i:s') ?>

-- 1) Cria o banco e seleciona
CREATE DATABASE IF NOT EXISTS rifa_platform;
USE rifa_platform;

-- 2) Tabela de usuários
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (email),
    INDEX (role)
);

-- 3) Tabela de rifas
CREATE TABLE IF NOT EXISTS raffles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_path VARCHAR(500),
    total_numbers INT NOT NULL,
    price_per_number DECIMAL(10,2) NOT NULL,
    draw_date DATETIME,
    draw_location VARCHAR(255),
    contact_phone VARCHAR(20),
    is_published BOOLEAN DEFAULT TRUE,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (is_published),
    INDEX (created_by)
);

-- 4) Tabela de números/cotas
CREATE TABLE IF NOT EXISTS raffle_numbers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    raffle_id INT NOT NULL,
    number INT NOT NULL,
    status ENUM('available','reserved','paid') DEFAULT 'available',
    reserved_at TIMESTAMP NULL,
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (raffle_id) REFERENCES raffles(id) ON DELETE CASCADE,
    UNIQUE KEY (raffle_id, number),
    INDEX (raffle_id, status),
    INDEX (status)
);

-- 5) Tabela de reservas
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    raffle_id INT NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_name VARCHAR(255),
    numbers JSON NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('reserved','paid','cancelled') DEFAULT 'reserved',
    payment_proof_path VARCHAR(500),
    notes TEXT,
    reserved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (raffle_id) REFERENCES raffles(id) ON DELETE CASCADE,
    INDEX (raffle_id),
    INDEX (customer_email),
    INDEX (status),
    INDEX (reserved_at)
);

-- 6) Tabela de logs do sistema
CREATE TABLE IF NOT EXISTS system_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    level ENUM('info','warning','error') DEFAULT 'info',
    message TEXT NOT NULL,
    context JSON,
    user_id INT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX (level),
    INDEX (created_at),
    INDEX (user_id)
);

-- 7) Usuário admin padrão (senha "password" – hash via PHP password_hash)
INSERT INTO users (name, email, password, role)
VALUES ('Administrador','admin@rifas.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin')
ON DUPLICATE KEY UPDATE role = 'admin';

-- 8) Remove triggers existentes (se houver)
DROP TRIGGER IF EXISTS create_raffle_numbers;
DROP TRIGGER IF EXISTS update_numbers_on_payment;

-- 9) Cria triggers com delimitador especial
DELIMITER $$

-- 9.1) Gera automaticamente os números ao criar uma rifa
CREATE TRIGGER create_raffle_numbers
AFTER INSERT ON raffles
FOR EACH ROW
BEGIN
    DECLARE i INT DEFAULT 1;
    WHILE i <= NEW.total_numbers DO
        INSERT INTO raffle_numbers (raffle_id, number)
        VALUES (NEW.id, i);
        SET i = i + 1;
    END WHILE;
END$$

-- 9.2) Atualiza status dos números quando a reserva muda para “paid” ou “cancelled”
CREATE TRIGGER update_numbers_on_payment
AFTER UPDATE ON reservations
FOR EACH ROW
BEGIN
    IF NEW.status = 'paid' AND OLD.status <> 'paid' THEN
        UPDATE raffle_numbers rn
        SET rn.status    = 'paid',
            rn.paid_at   = NOW()
        WHERE rn.raffle_id = NEW.raffle_id
          AND JSON_CONTAINS(NEW.numbers, JSON_ARRAY(rn.number));
    END IF;

    IF NEW.status = 'cancelled' AND OLD.status <> 'cancelled' THEN
        UPDATE raffle_numbers rn
        SET rn.status       = 'available',
            rn.reserved_at  = NULL,
            rn.paid_at      = NULL
        WHERE rn.raffle_id  = NEW.raffle_id
          AND JSON_CONTAINS(NEW.numbers, JSON_ARRAY(rn.number));
    END IF;
END$$

DELIMITER ;
