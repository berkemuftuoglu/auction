-- Users Table
INSERT INTO Users (email, password, role, first_name, last_name)
VALUES 
    ('john.doe@example.com', 'password123', 0, 'John', 'Doe'),
    ('jane.smith@example.com', 'securepass', 1, 'Jane', 'Smith'),
    ('sam.jones@example.com', 'samspassword', 1, 'Sam', 'Jones'),
    ('sara.white@example.com', 'pass123', 0, 'Sara', 'White');

-- Item Table
INSERT INTO Item (name, description, category, colour, `condition`, photo)
VALUES 
    ('Smartphone', 'Brand new, in original packaging', 'Electronics', 'Black', 'Great', '/photos/smartphone.jpg'),
    ('Vintage Bookshelf', 'Antique wooden bookshelf', 'Home', 'Brown', 'Good', '/photos/bookshelf.jpg'),
    ('Designer Dress', 'Elegant evening dress', 'Fashion', 'Red', 'Great', '/photos/dress.jpg'),
    ('Coffee Table', 'Modern glass coffee table', 'Home', 'White', 'Okay', '/photos/coffee_table.jpg');

-- Auction Table
INSERT INTO Auction (item_id, user_id, start_time, end_time, auction_title, reserve_price, starting_price)
VALUES 
    (1, 1, '2023-11-01 12:00:00', '2023-12-03 18:00:00', 'Latest Smartphone Auction', 500.00, 200.00),
    (2, 1, '2023-11-05 14:00:00', '2023-12-07 20:00:00', 'Vintage Bookshelf Sale', 150.00, 50.00),
    (3, 4, '2023-11-08 10:00:00', '2023-12-18 15:00:00', 'Designer Dress Auction', 300.00, 100.00),
    (4, 4, '2023-11-12 09:00:00', '2023-12-22 12:00:00', 'Modern Coffee Table Bidding', 200.00, 80.00);

-- Bids Table
INSERT INTO Bids (auction_id, user_id, time_of_bid, price)
VALUES 
    (1, 2, '2023-11-05 14:30:00', 220.00),
    (1, 3, '2023-11-07 16:45:00', 250.00),
    (2, 2, '2023-11-10 10:20:00', 60.00),
    (3, 3, '2023-11-15 12:10:00', 150.00),
    (4, 2, '2023-11-18 11:30:00', 90.00),
    (4, 3, '2023-11-14 17:45:00', 60.00);

-- Watchlist Table
INSERT INTO Watchlist (user_id, item_id)
VALUES 
    (2, 2),
    (3, 3),
    (2, 1),
    (3, 4),
    (2, 3);

INSERT INTO Admins (email, password, first_name, last_name)
VALUES 
    ('admin', 'admin', 'Napoleon', 'Bonaparte');