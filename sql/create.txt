CREATE TABLE Users (
    UserID INT PRIMARY KEY AUTO_INCREMENT,
    Username VARCHAR(255) NOT NULL,
    Password VARCHAR(255) NOT NULL,  -- hashed and salted
    Email VARCHAR(255) UNIQUE NOT NULL,
    RegistrationDate DATE,
    LastLogin DATE
);

CREATE TABLE Items (
    ItemID INT PRIMARY KEY AUTO_INCREMENT,
    SellerID INT,
    Name VARCHAR(255) NOT NULL,
    Description TEXT,
    StartDate DATE,
    EndDate DATE,
    StartingPrice DECIMAL(10,2),
    CurrentPrice DECIMAL(10,2),
    ImagePath TEXT,
    FOREIGN KEY (SellerID) REFERENCES Users(UserID)
);

CREATE TABLE Bids (
    BidID INT PRIMARY KEY AUTO_INCREMENT,
    ItemID INT,
    BidderID INT,
    BidAmount DECIMAL(10,2),
    BidDate DATE,
    FOREIGN KEY (ItemID) REFERENCES Items(ItemID),
    FOREIGN KEY (BidderID) REFERENCES Users(UserID)
);

CREATE TABLE Categories (
    CategoryID INT PRIMARY KEY AUTO_INCREMENT,
    CategoryName VARCHAR(255) NOT NULL,
    ParentCategoryID INT,
    FOREIGN KEY (ParentCategoryID) REFERENCES Categories(CategoryID)
);

CREATE TABLE ItemCategories (
    ItemID INT,
    CategoryID INT,
    PRIMARY KEY (ItemID, CategoryID),
    FOREIGN KEY (ItemID) REFERENCES Items(ItemID),
    FOREIGN KEY (CategoryID) REFERENCES Categories(CategoryID)
);

CREATE TABLE UserRatings (
    RatingID INT PRIMARY KEY AUTO_INCREMENT,
    RatedUserID INT,
    RaterUserID INT,
    RatingValue INT CHECK(RatingValue >= 1 AND RatingValue <= 5),
    RatingComment TEXT,
    RatingDate DATE,
    FOREIGN KEY (RatedUserID) REFERENCES Users(UserID),
    FOREIGN KEY (RaterUserID) REFERENCES Users(UserID)
);

CREATE TABLE Transactions (
    TransactionID INT PRIMARY KEY AUTO_INCREMENT,
    BuyerID INT,
    SellerID INT,
    ItemID INT,
    TransactionDate DATE,
    Amount DECIMAL(10,2),
    FOREIGN KEY (BuyerID) REFERENCES Users(UserID),
    FOREIGN KEY (SellerID) REFERENCES Users(UserID),
    FOREIGN KEY (ItemID) REFERENCES Items(ItemID)
);
