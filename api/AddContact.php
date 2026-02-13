<?php

	$inData = getRequestInfo();

	$userId = isset($inData["userId"]) ? $inData["userId"] : null;
	$contactFirstName = isset($inData["contactFirstName"]) ? trim($inData["contactFirstName"]) : ""; 
    $contactLastName = isset($inData["contactLastName"]) ? trim($inData["contactLastName"]) : ""; 
	$phone = isset($inData["phone"]) ? trim($inData["phone"]) : "";
    $email = isset($inData["email"]) ? trim($inData["email"]) : ""; 

	if ($userId === null || $contactFirstName === "" || $contactLastName === "") {
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
		$stmt = $conn->prepare("INSERT into Contacts (UserID,FirstName,LastName,Phone,Email) VALUES(?,?,?,?,?)");
		$stmt->bind_param("issss", $userId, $contactFirstName, $contactLastName, $phone, $email);
		$stmt->execute();
		$stmt->close();
		$conn->close();
		returnWithInfo("Contact added successfully");
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