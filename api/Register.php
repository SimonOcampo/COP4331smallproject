<?php

    // Sets header early to ensure all output is treated as JSON
    header('Content-type: application/json');

    $inData = getRequestInfo();

    if ($inData === null) {
        returnWithError("Invalid JSON format or empty request body");
        exit;
    }

    $firstName = isset($inData["firstName"]) ? trim($inData["firstName"]) : (isset($inData["FirstName"]) ? trim($inData["FirstName"]) : "");
    $lastName  = isset($inData["lastName"])  ? trim($inData["lastName"])  : (isset($inData["LastName"]) ? trim($inData["LastName"]) : "");
    $login     = isset($inData["login"])     ? trim($inData["login"])     : "";
    $password  = isset($inData["password"])  ? $inData["password"]        : "";

    if ($firstName === "" || $lastName === "" || $login === "" || $password === "")
    {
        returnWithError("Missing required fields");
        exit;
    }

    include('config.php');
    
    // Checks connection explicitly
    if (!$conn || $conn->connect_error)
    {
        returnWithError("Database connection failed: " . ($conn->connect_error ?? "Unknown error"));
        exit;
    }

    // Checks if the login already exists
    $stmt = $conn->prepare("SELECT ID FROM Users WHERE Login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->fetch_assoc())
    {
        $stmt->close();
        returnWithError("Login already exists");
        exit;
    }
    $stmt->close();

    // Hashes password and inserts
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO Users (FirstName, LastName, Login, Password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $firstName, $lastName, $login, $passwordHash);

    if ($stmt->execute())
    {
        $newId = $conn->insert_id;
        $stmt->close();
        $conn->close();
        returnWithInfo($firstName, $lastName, $newId);
    }
    else
    {
        $errorMsg = $stmt->error;
        $stmt->close();
        $conn->close();
        returnWithError("Insert failed: " . $errorMsg);
    }

    function getRequestInfo()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    function returnWithError($err)
    {
        // Use an array and json_encode to prevent manual string errors
        $retValue = array("id" => 0, "firstName" => "", "lastName" => "", "error" => $err);
        echo json_encode($retValue);
    }

    function returnWithInfo($firstName, $lastName, $id)
    {
        // Ensuring ID is cast to an integer to prevent empty values
        $retValue = array("id" => (int)$id, "firstName" => $firstName, "lastName" => $lastName, "error" => "");
        echo json_encode($retValue);
    }
?>
