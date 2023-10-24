-- Insert data into the Users table
INSERT INTO Users (email, password, type, first_name, last_name) VALUES
    ('buyer@example.com', 'password1', 1, 'John', 'Doe'),       -- Status: buyer
    ('seller@example.com', 'password2', 0, 'Jane', 'Smith');    -- Status: seller

-- Insert data into the Item table
INSERT INTO Item (name, description, reserve_price, starting_price, category, status) VALUES
    ('Smartphone', 'Brand new smartphone', 500.00, 300.00, 'Electronics', 0),       -- Status: available
    ('Laptop', 'Used laptop in good condition', 800.00, 600.00, 'Electronics', 0),  -- Status: available
    ('Designer Dress', 'High-end fashion dress', 1000.00, 700.00, 'Fashion', 1);    -- Status: sold

-- Insert data into the Auction table
INSERT INTO Auction (item_id, start_time, end_time, auction_title) VALUES
    (1, '2023-10-25 10:00:00', '2023-10-26 10:00:00', 'Smartphone Auction'),
    (2, '2023-10-26 14:00:00', '2023-10-27 14:00:00', 'Laptop Auction'),
    (3, '2023-10-27 12:00:00', '2023-10-28 12:00:00', 'Designer Dress Auction');

-- Insert data into the Bids table
INSERT INTO Bids (auction_id, user_id, time_of_bid, price) VALUES
    (1, 1, '2023-10-25 10:30:00', 320.00),
    (1, 2, '2023-10-25 10:45:00', 350.00),
    (2, 1, '2023-10-26 14:15:00', 620.00);

-- Insert data into the Watchlist table
INSERT INTO Watchlist (user_id, item_id) VALUES
    (1, 2),
    (2, 1);