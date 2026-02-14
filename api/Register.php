<?php

    $inData = getRequestInfo();

    if ($inData === null) {
        returnWithError("Invalid JSON format or empty request body");
        exit;
    }

    $firstName = isset($inData["firstName"]) ? trim($inData["firstName"]) : "";
    $lastName  = isset($inData["lastName"])  ? trim($inData["lastName"])  : "";
    $login     = isset($inData["login"])     ? trim($inData["login"])     : "";
    $password  = isset($inData["password"])  ? $inData["password"]        : "";

    if ($firstName === "" || $lastName === "" || $login === "" || $password === "")
    {
        returnWithError("Missing required fields: firstName, lastName, login, and password are required.");
        exit;
    }

    include('config.php');
    if ($conn->connect_error)
    {
        returnWithError($conn->connect_error);
        exit;
    }

    $stmt = $conn->prepare("SELECT ID FROM Users WHERE Login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->fetch_assoc())
    {
        $stmt->close();
        $conn->close();
        returnWithError("Login already exists");
        exit;
    }
    $stmt->close();

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
        $err = $stmt->error;
        $stmt->close();
        $conn->close();
        returnWithError($err);
    }

    function getRequestInfo()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    function sendResultInfoAsJson($obj)
    {
        header('Content-type: application/json');
        echo $obj;
    }

    function returnWithError($err)
    {
        $retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
        sendResultInfoAsJson($retValue);
    }

    function returnWithInfo($firstName, $lastName, $id)
    {
        $retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
        sendResultInfoAsJson($retValue);
    }

?>