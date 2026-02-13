<?php

    $inData = getRequestInfo();

    $userId = isset($inData["userId"]) ? $inData["userId"] : null;
    $id = isset($inData["id"]) ? $inData["id"] : null;
    $email = isset($inData["email"]) ? trim($inData["email"]) : null;
    $phone = isset($inData["phone"]) ? trim($inData["phone"]) : null;

    if ($userId === null || ($id === null && $email === null && $phone === null)) {
        returnWithError("Missing required fields - need userId and at least one of: id, email, or phone");
        exit;
    }

    include('config.php');
    
    if($conn->connect_error){
        returnWithError($conn->connect_error);
    }
    else{
        // Delete contact by ID, email, or phone number
        $stmt = $conn->prepare(
            "DELETE FROM Contacts
            WHERE UserID = ? 
            AND (ID = ? OR Email = ? OR Phone = ?)"
        );

        $stmt->bind_param("iiss", 
            $userId, 
            $id, 
            $email, 
            $phone
        );

        $stmt->execute();

        // Success check
        if($stmt->affected_rows > 0){
            returnWithInfo("Contact deleted");
        }
        else{
            returnWithError("No matching contact found");
        }

        $stmt->close();
        $conn->close();
    }

    function getRequestInfo(){
        return json_decode(file_get_contents('php://input'), true);
    }

    function sendResultInfoAsJson($obj){
        header('Content-type: application/json');
        echo $obj;
    }

    function returnWithError($err){
        $retValue = '{"error":"' . $err . '"}';
        sendResultInfoAsJson($retValue);
    }

    function returnWithInfo($msg){
        $retValue = '{"result":"' . $msg . '","error":""}';
        sendResultInfoAsJson($retValue);
    }

?>