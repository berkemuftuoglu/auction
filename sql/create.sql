CREATE DATABASE auction;

USE auction;

CREATE TABLE Users (
    user_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role BOOL, -- 0 for seller, 1 for buyer
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    UNIQUE(email)
);

CREATE TABLE Item (
    item_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description VARCHAR(511),
    category ENUM('Electronics', 'Fashion', 'Home', 'Books', 'Other') NOT NULL,
    colour ENUM('Red', 'Orange', 'Yellow', 'Green', 'Blue', 'Purple', 'Pink', 'White', 'Grey', 'Black', 'Brown', 'Other'),
    `condition` ENUM('Great', 'Good', 'Okay', 'Poor'),
    photo VARCHAR(255) -- filepath
);

CREATE TABLE Auction (
    auction_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    item_id INT(11),
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL, -- if end time in earlier than current time, then the auction is over
    auction_title VARCHAR(255),
    reserve_price FLOAT(2),
    starting_price FLOAT(2),
    FOREIGN KEY (item_id) REFERENCES Item(item_id)
);

CREATE TABLE Bids (
    bid_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    auction_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    time_of_bid DATETIME NOT NULL,
    price FLOAT(2),
    FOREIGN KEY (auction_id) REFERENCES Auction(auction_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE Watchlist (
    user_id INT(11) NOT NULL,
    item_id INT(11) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (item_id) REFERENCES Item(item_id),
    PRIMARY KEY (user_id, item_id)
);
