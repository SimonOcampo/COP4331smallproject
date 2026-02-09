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
		$stmt = $conn->prepare("INSERT into Contacts (UserID,FirstName,LastName,Phone,Email) VALUES(?,?,?,?,?)");
		$stmt->bind_param("issss", $userId, $contactFirstName, $contactLastName, $phone, $email);
		$stmt->execute();
		$stmt->close();
		$conn->close();
		returnWithError("");
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
	
?>