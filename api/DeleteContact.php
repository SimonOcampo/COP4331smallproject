<?php

    $inData = getRequestInfo();

    $conn = new mysqli("localhost", "root", "", "ContactManager");
    
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
            $inData["userId"], 
            $inData["id"], 
            $inData["email"], 
            $inData["phone"]
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