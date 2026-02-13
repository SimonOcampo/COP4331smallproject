<?php

	$inData = getRequestInfo();

	$userId = isset($inData["userId"]) ? $inData["userId"] : null;
	$id = isset($inData["id"]) ? $inData["id"] : null;
	$contactFirstName = isset($inData["contactFirstName"]) ? trim($inData["contactFirstName"]) : ""; 
    $contactLastName = isset($inData["contactLastName"]) ? trim($inData["contactLastName"]) : "";   
	$phone = isset($inData["phone"]) ? trim($inData["phone"]) : "";
    $email = isset($inData["email"]) ? trim($inData["email"]) : ""; 

	if ($userId === null || $id === null || $contactFirstName === "" || $contactLastName === "") {
		returnWithError("Missing required fields");
		exit;
	}

	include('config.php');
	
    if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("UPDATE Contacts SET FirstName=?, LastName=?, Phone=?, Email=? WHERE UserID=? AND ID=?");
		$stmt->bind_param("ssssii", $contactFirstName, $contactLastName, $phone, $email, $userId, $id);
		$stmt->execute();
		
		// Success check
        if($stmt->affected_rows > 0){
            returnWithInfo("Contact updated");
        }
        else{
            returnWithError("No matching contact found");
        }

        $stmt->close();
        $conn->close();
    }

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $msg )
	{
		$retValue = '{"result":"' . $msg . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>