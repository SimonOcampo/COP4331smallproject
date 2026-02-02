USE ContactManager;

-- Sample Users
INSERT INTO Users (FirstName, LastName, Login, Password)
VALUES 
('Kelvin', 'Student', 'kelvin', 'password123'),
('Test', 'User', 'testuser', 'testpass');

-- Sample Contacts for Kelvin (UserID = 1)
INSERT INTO Contacts (FirstName, LastName, Phone, Email, UserID)
VALUES
('John', 'Doe', '407-555-1234', 'john.doe@example.com', 1),
('Jane', 'Smith', '321-555-5678', 'jane.smith@example.com', 1);

-- Sample Contact for Test User (UserID = 2)
INSERT INTO Contacts (FirstName, LastName, Phone, Email, UserID)
VALUES
('Bob', 'Brown', '305-555-9999', 'bob.brown@example.com', 2);