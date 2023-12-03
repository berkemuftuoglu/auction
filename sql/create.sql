CREATE DATABASE auction;

USE auction;

CREATE TABLE Users (
    user_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role BOOL, -- 0 for seller, 1 for buyer
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    UNIQUE(email),
    total_ratings INT(11) DEFAULT 0,
    average_rating FLOAT DEFAULT 0.0
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
    user_id INT(11),
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL, -- if end time in earlier than current time, then the auction is over
    auction_title VARCHAR(255),
    reserve_price FLOAT(2),
    starting_price FLOAT(2),
    FOREIGN KEY (item_id) REFERENCES Item(item_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

CREATE TABLE Bids (
    bid_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    auction_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    time_of_bid DATETIME NOT NULL,
    price FLOAT(2),
    FOREIGN KEY (auction_id) REFERENCES Auction(auction_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

CREATE TABLE Watchlist (
    user_id INT(11) NOT NULL,
    item_id INT(11) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES Item(item_id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, item_id)
);

CREATE TABLE Ratings (
    rating_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    rater_user_id INT(11) NOT NULL,
    rated_user_id INT(11) NOT NULL,
    item_id INT(11) NOT NULL,
    rating_value ENUM('0', '1', '2', '3', '4', '5'),
    UNIQUE (rater_user_id, rated_user_id),
    FOREIGN KEY (rater_user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (rated_user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

CREATE TABLE Admins (
    admin_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    UNIQUE(email)
);