-- SCHEMA DE LA BASE DE DONNEES POUR E-PHARM
CREATE TABLE IF NOT EXISTS users (
  id int PRIMARY KEY AUTO_INCREMENT,
  username varchar(50) NOT NULL,
  email varchar(50) NOT NULL,
  password varchar(255) NOT NULL,
  role enum('user','admin') DEFAULT 'user',
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS entreprises (
  id int PRIMARY KEY AUTO_INCREMENT,
  name varchar(50) NOT NULL,
  email varchar(50) NOT NULL,
  phone varchar(50) NOT NULL,
  address varchar(255) NOT NULL,
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime DEFAULT CURRENT_TIMESTAMP,
  status enum('pending','accepted','rejected'),
  adminId int,
  FOREIGN KEY (adminId)   REFERENCES users (id)
);

CREATE TABLE IF NOT EXISTS roles (
  id int PRIMARY KEY AUTO_INCREMENT,
  role varchar(255)
);

CREATE TABLE IF NOT EXISTS staff (
  id int PRIMARY KEY AUTO_INCREMENT,
  entreprise_id int,
  user_id int,
  role int,
  FOREIGN KEY (entreprise_id) REFERENCES  entreprises (id),
  FOREIGN KEY (user_id) REFERENCES  users (id),
  FOREIGN KEY (role) REFERENCES  roles (id)
);

CREATE TABLE IF NOT EXISTS categories (
  id int PRIMARY KEY AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  description varchar(255)
);

CREATE TABLE IF NOT EXISTS products (
  id int PRIMARY KEY AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  about varchar(255),
  description text,
  price varchar(255) NOT NULL,
  category int,
  image varchar(255) NOT NULL,
  stock int NOT NULL,
  created_by int,
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category) REFERENCES categories (id),
  FOREIGN KEY (created_by) REFERENCES entreprises (id)
);


CREATE TABLE IF NOT EXISTS orders (
  id int PRIMARY KEY AUTO_INCREMENT,
  user_id int,
  status enum('pending','completed','rejected'),
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE TABLE IF NOT EXISTS orders_details (
  id int PRIMARY KEY AUTO_INCREMENT,
  order_id int,
  product_id int,
  quantity int NOT NULL,
  price int NOT NULL,
  status enum('pending','accepted','sent','received','rejected') DEFAULT 'pending',
  FOREIGN KEY (order_id) REFERENCES orders (id),
  FOREIGN KEY (product_id) REFERENCES products (id)
);

CREATE TABLE IF NOT EXISTS cart {
  id int PRIMARY KEY AUTO_INCREMENT,
  user_id int,
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users (id),
};

CREATE TABLE IF NOT EXISTS cart_items {
  id int PRIMARY KEY AUTO_INCREMENT,
  cart_id int,
  product_id int,
  quantity int NOT NULL,
  FOREIGN KEY (cart_id) REFERENCES cart (id),
  FOREIGN KEY (product_id) REFERENCES products (id)
};