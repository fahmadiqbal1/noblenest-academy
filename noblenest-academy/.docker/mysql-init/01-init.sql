-- Noble Nest Academy — Docker MySQL init
-- Runs once when the noblenest-db container is first created.
CREATE DATABASE IF NOT EXISTS noblenest
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'noblenest'@'%' IDENTIFIED BY 'noblenest_dev_2026';
GRANT ALL PRIVILEGES ON noblenest.* TO 'noblenest'@'%';
FLUSH PRIVILEGES;
