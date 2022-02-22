
DROP DATABASE IF EXISTS `laravel`;

CREATE DATABASE `laravel` CHARACTER SET utf8 COLLATE utf8_unicode_ci;

USE `laravel`;

DROP TABLE IF EXISTS auth_assignment;
DROP TABLE IF EXISTS auth_item_child;
DROP TABLE IF EXISTS auth_item;
DROP TABLE IF EXISTS user;
CREATE TABLE user(
	id BIGINT AUTO_INCREMENT NOT NULL,
	name VARCHAR(25) NOT NULL,
	email VARCHAR(255) UNIQUE NOT NULL,
	PRIMARY KEY(id)
) CHARACTER SET utf8 COLLATE utf8_unicode_ci;
INSERT INTO user(name, email) VALUES
('Raka Helviansyah Putra', 'rakahelviansyahputra@gmail.com'),
('Ayu Apriani', 'aprilaa9@gmail.com');

CREATE TABLE auth_item(
	name VARCHAR(64) PRIMARY KEY NOT NULL,
	type ENUM('role', 'operation') NOT NULL,
	data TEXT NULL,
	description TEXT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP NULL
) CHARACTER SET utf8 COLLATE utf8_unicode_ci;
INSERT INTO auth_item(name, type, description) VALUES
('webmaster', 'role', 'Hak akses untuk semua peran'),
('guest', 'role', 'Hak akses tamu'),
('api', 'role', 'Hak akses untuk Api'),
('finance', 'role', 'Hak akses untuk peran "Finance"'),
('user_index', 'operation', 'Halaman utama daftar pengguna <link>'),
('user_create', 'operation', 'Form membuat pengguna baru <link>'),
('user_update', 'operation', 'Form mengubah data pengguna <link>'),
('user_delete', 'operation', 'Menghapus pengguna <image>');

CREATE TABLE auth_item_child(
	parent VARCHAR(64) NOT NULL,
	child VARCHAR(64) NOT NULL,
	PRIMARY KEY(parent, child),
	FOREIGN KEY(parent) REFERENCES auth_item(name) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY(child) REFERENCES auth_item(name) ON DELETE CASCADE ON UPDATE CASCADE
) CHARACTER SET utf8 COLLATE utf8_unicode_ci;
INSERT INTO auth_item_child VALUES
('webmaster','guest'),
('webmaster','api'),
('webmaster','finance'),
('guest','user_index');

CREATE TABLE auth_assignment(
	auth_item VARCHAR(64) NOT NULL,
	user_id BIGINT NOT NULL,
	status ENUM('Active','Inactive') NOT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY(auth_item, user_id),
	FOREIGN KEY(auth_item) REFERENCES auth_item(name) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY(user_id) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE
) CHARACTER SET utf8 COLLATE utf8_unicode_ci;
INSERT INTO auth_assignment VALUES
('webmaster', 1, 'Active', NOW()),
('guest', 1, 'Inactive'),
('guest', 2, 'Active', NOW());





