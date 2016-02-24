CREATE table User (
firstName nvarchar(64) NOT NULL,
lastName nvarchar(64) NOT NULL,
password nvarchar(64) NOT NULL,
dob DATE NOT NULL,
email nvarchar(256) PRIMARY KEY,
reputation INT);

CREATE table Categories (
category nvarchar(64));

CREATE table itemList (
itemID int AUTO_INCREMENT PRIMARY KEY;
ownerEmail nvarchar(256) REFERENCES User(email),
itemName nvarchar(64),
itemDescription nvarchar(256),
itemAvailability nvarchar(64),
itemCategory nvarchar(64) REFERENCES Categories(category));

CREATE table record (
recordID int AUTO_INCREMENT PRIMARY KEY,
borrowerEmail nvarchar(256) REFERENCES User(email),
itemID INT REFERENCES itemList(itemID),
fee INT,
timeToReturn DATE,
time TIME,
successful BOOLEAN CHAR(6));

Advertise 
ownerEmail nvarchar(256) REFERENCES User(email),
itemID INT REFERENCES itemList(itemID),
statement nvarchar(256),
price INT,
location nvarchar(256));