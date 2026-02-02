DROP DATABASE IF EXISTS ContactManager;
CREATE DATABASE ContactManager;
USE ContactManager;

-- Users table to store user account information
CREATE TABLE Users (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    Login VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL
);

-- Contacts table to store contacts for each user
CREATE TABLE Contacts (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    Phone VARCHAR(50),
    Email VARCHAR(100),
    UserID INT NOT NULL,

    CONSTRAINT fk_contacts_user
        FOREIGN KEY (UserID)
        REFERENCES Users(ID)
        ON DELETE CASCADE
);