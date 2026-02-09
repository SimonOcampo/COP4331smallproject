<?php

	$inData = getRequestInfo();

	$userId = $inData["userId"];
	$contactFirstName = $inData["contactFirstName"]; 
    $contactLastName = $inData["contactLastName"]; 
	$phone = $inData["phone"];
    $email = $inData["email"]; 

	$conn = new mysqli("localhost", "root", "", "ContactManager");
	
    if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("UPDATE Contacts SET FirstName=?, LastName=?, Phone=?, Email=? WHERE UserID=? AND ID=?");
		$stmt->bind_param("ssssii", $contactFirstName, $contactLastName, $phone, $email, $userId, $inData["id"]);
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