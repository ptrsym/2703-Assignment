DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS manufacturers;
DROP TABLE IF EXISTS items;
DROP TABLE IF EXISTS reviews;


-- redundant but troubleshooting errors and exploring syntax

CREATE TABLE IF NOT EXISTS users (
    id integer primary key autoincrement,
    username VARCHAR(40) UNIQUE COLLATE NOCASE NOT NULL,
    pass VARCHAR(40) NOT NULL
);

CREATE TABLE IF NOT EXISTS manufacturers (
    id integer primary key autoincrement,
    manname varchar(40) UNIQUE not null
);

CREATE TABLE IF NOT EXISTS items (
    id integer primary key autoincrement,
    productname VARCHAR(40) UNIQUE not null,
    manufacturer_id int not null,
    FOREIGN KEY (manufacturer_id) references manufacturers(id)
);

CREATE TABLE IF NOT EXISTS reviews (
    id integer primary key autoincrement,
    user_id int not null,
    item_id int not null,
    reviewtext TEXT not null,
    postdate DATE NOT NULL DEFAULT (DATE('now')),
    rating int not null check (rating >= 1 and rating <= 5),
    FOREIGN KEY (user_id) references users(id),
    FOREIGN KEY (item_id) references items(id)
);



-- initialised some test data 

-- Users
INSERT INTO users (username, pass) VALUES 
('Garfield', '123'),
('Jon', '123'),
('Odie', '123'),
('Nermal', '123'),
('Liz', '123');

-- Manufacturers
INSERT INTO manufacturers (manname) VALUES 
('Purrrfect Cat Foods'),  
('Barkin'' Good Dog Snacks'),  
('Feline Feast Co.'), 
('Paws & Claws Inc.');  

-- Items
INSERT INTO items (productname, manufacturer_id) VALUES 
('Fishy Feast', 1),  
('Beefy Bites', 2),  
('Tuna Treats', 3),  
('Chicken Crunchies', 4),  
('Catnip Crunch', 1); 

-- Reviews
INSERT INTO reviews (user_id, item_id, reviewtext, postdate, rating) VALUES 
(1, 1, 'Tasty, but no lasagna.', '2024-09-01', 4), 
(2, 1, 'The fish smell is strong, but the cats enjoy it.', '2024-09-02', 3),  
(3, 3, 'woof', '2024-09-03', 5),  
(4, 4, 'The crunchiness is cute, just like me!', '2024-09-04', 5), 
(5, 5, 'Perfect for the feline friends.', '2024-09-05', 4), 
(1, 2, 'Garfield is unimpressed, needs more flavor.', '2024-09-06', 2), 
(2, 4, 'Dogs seem to enjoy it!', '2024-09-07', 4);  





