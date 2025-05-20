CREATE TABLE shop.admin(
    id INT NOT NULL PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME,
    updated_at DATETIME
)

CREATE TABLE shop.members(
    id INT NOT NULL PRIMARY KEY,
    family_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    family_name_kana VARCHAR(255),
    last_name_kana VARCHAR(255),
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    postal_code CHAR(8),
    address VARCHAR(255),
    phone_number VARCHAR(15),
    is_deleted BOOLEAN,
    created_at DATETIME,
    updated_at DATETIME
)

CREATE TABLE shop.products(
    id INT NOT NULL PRIMARY KEY,
    category_id INT,
    product_name VARCHAR(255) NOT NULL,
    description TEXT,
    base_price DECIMAL(10,2) NOT NULL,
    stock_quantity INT,
    is_active BOOLEAN,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (category_id) REFERENCES category(id)
)

CREATE TABLE shop.categories(
    id INT NOT NULL PRIMARY KEY,
    category_name VARCHAR(255) NOT NULL,
    created_at DATETIME,
    updated_at DATETIME
)

CREATE TABLE shop.in_carts(
    id INT NOT NULL PRIMARY KEY,
    member_id INT NOT NULL,
    product_id INT NOT NULL,
    number INT NOT NULL,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)

CREATE TABLE shop.orders(
    id INT NOT NULL PRIMARY KEY,
    member_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    postal_number CHAR(8) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone_number VARCHAR(15),
    email VARCHAR(255),
    shipping_fee DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    order_status VARCHAR(20),
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (member_id) REFERENCES members(id)
)

CREATE TABLE shop.products_ordered(
    id INT NOT NULL PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    number INT NOT NULL,
    ready_status VARCHAR(20),
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)

CREATE TABLE shop.delivery_info(
    id INT NOT NULL PRIMARY KEY,
    member_id INT NOT NULL,
    postal_number CHAR(8) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone_number VARCHAR(15),
    email VARCHAR(255),
    name VARCHAR(255) NOT NULL,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (member_id) REFERENCES members(id)
)