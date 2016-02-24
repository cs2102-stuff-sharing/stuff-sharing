CREATE table Users (
firstName varchar(64) NOT NULL,
lastName varchar(64) NOT NULL,
password varchar(64) NOT NULL,
dob DATE NOT NULL,
email varchar(256) PRIMARY KEY,
reputation INT);

CREATE table Categories (
category varchar(64) PRIMARY KEY);

CREATE table itemList (
itemID SERIAL PRIMARY KEY,
ownerEmail varchar(256) REFERENCES Users(email),
itemName varchar(64),
itemDescription varchar(256),
itemAvailability varchar(64),
itemCategory varchar(64) REFERENCES Categories(category));

CREATE table record (
recordID SERIAL PRIMARY KEY,
borrowerEmail varchar(256) REFERENCES Users(email),
itemID INT REFERENCES itemList(itemID),
fee INT,
timeToReturn DATE,
time TIME,
successful BOOLEAN);

CREATE TABLE Advertise (
ownerEmail varchar(256) REFERENCES Users(email),
itemID INT REFERENCES itemList(itemID),
statement varchar(256),
price INT,
location varchar(256));