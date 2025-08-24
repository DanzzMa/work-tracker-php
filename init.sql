-- Buat database secara manual terlebih dahulu (jika belum), lalu gunakan:
--   CREATE DATABASE work_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
--   USE work_tracker;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','worker') DEFAULT 'worker',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS work_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  start_time DATETIME,
  end_time DATETIME,
  duration_minutes INT DEFAULT 0, -- total menit
  created_at DATE,                -- tanggal start
  created_ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insert admin default (username: admin / password: admin123)
INSERT INTO users (username, password, role)
VALUES ('admin', '$2y$10$Nva9m7Zmqrf9U9aX9nMOhuYcgHR9g8SwhM5z4yq52P4t6MoGVRTlu', 'admin')
ON DUPLICATE KEY UPDATE username=username;
