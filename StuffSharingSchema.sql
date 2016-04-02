CREATE table Users (
    firstName VARCHAR(64) NOT NULL,
    lastName VARCHAR(64) NOT NULL,
    password VARCHAR(64) NOT NULL,
    dob DATE NOT NULL,
    email VARCHAR(256) PRIMARY KEY,
    isAdmin BOOLEAN DEFAULT false,
    userPoint INT DEFAULT 100,
    blackListCount INT DEFAULT 0
);

CREATE table ItemList (
    itemId SERIAL PRIMARY KEY,
    ownerEmail VARCHAR(256) REFERENCES Users(email),
    itemName VARCHAR(64),
    itemDescription VARCHAR(256),
    itemCategory VARCHAR(64) 
);

CREATE TABLE Advertise (
    itemId SERIAL PRIMARY KEY REFERENCES ItemList(itemId),
    minimumBidPoint INT NOT NULL
);

/* All current biddings will be recorded in this table, after the bidding ends, all the bidding records (successful or not) will be removed */
CREATE TABLE BiddingList (
    itemId SERIAL REFERENCES ItemList(itemId),
    bidderId VARCHAR(256) REFERENCES Users(email),
    bidAmount INT CHECK (bidAmount > 0),
    PRIMARY KEY (itemId, bidderId)
);

/* This table records all the successful biddings, the record will be deleted after the transaction is done */
CREATE TABLE Record (
    itemId SERIAL REFERENCES ItemList(itemId),
    bidderId VARCHAR(256) REFERENCES Users(email),
    bidAmount INT CHECK (bidAmount > 0),
    PRIMARY KEY (itemId, bidderId)
);

/* Add default administrator account, password: 'adminpassword */
INSERT INTO Users( firstName, lastName, dob, email, password, isAdmin) 
VALUES('Admin', 'Root', CURRENT_DATE, 'admin@stuffsharing.com','e3274be5c857fb42ab72d786e281b4b8', true);
