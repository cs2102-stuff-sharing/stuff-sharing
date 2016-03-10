CREATE table Users (
    firstName VARCHAR(64) NOT NULL,
    lastName VARCHAR(64) NOT NULL,
    password VARCHAR(64) NOT NULL,
    dob DATE NOT NULL,
    email VARCHAR(256) PRIMARY KEY,
    userPoint INT DEFAULT 100,
    blackListCount INT DEFAULT 0
);

/* When a user deletes an item, it still preserves in the database so that the notifications can still be sent properly */
CREATE table ItemList (
    itemId SERIAL PRIMARY KEY,
    ownerEmail VARCHAR(256) REFERENCES Users(email),
    itemName VARCHAR(64),
    itemDescription VARCHAR(256),
    itemDeleted BOOLEAN DEFAULT FALSE,
    itemCategory VARCHAR(64) 
);

CREATE TABLE Advertise (
    itemId SERIAL PRIMARY KEY REFERENCES itemList(itemId),
    minimumBidPoint INT NOT NULL
);

/* All current biddings will be recorded in this table, after the bidding ends, all the bidding records (successful or not) will be removed */
CREATE TABLE BiddingList (
    itemId SERIAL REFERENCES itemList(itemId),
    bidderId VARCHAR(256) REFERENCES Users(email),
    bidAmount INT CHECK (bidAmount > 0),
    PRIMARY KEY (itemId, bidderId)
);

/* This table records all the successful biddings, the record will be deleted after the transaction is done */
CREATE TABLE Record (
    itemId SERIAL REFERENCES itemList(itemId),
    bidderId VARCHAR(256) REFERENCES Users(email),
    bidAmount INT CHECK (bidAmount > 0),
    PRIMARY KEY (itemId, bidderId)
);

CREATE TABLE Notifications (
    receiver VARCHAR(256) REFERENCES Users(email),
    status VARCHAR(64) NOT NULL,
    productId SERIAL NOT NULL,
    PRIMARY KEY (receiver, productId)
);
