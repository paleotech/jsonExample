<?php
/*
 * @desc
 * JSON Example: a sample JSON parser to server as the back end of a mobile app
 * Class JSON example instantiates and processes 
 * with a location-specific background image, and variables printed on the page.  Queries are 
 * prepared statements to a mysql database * using the MySQLi extension.
 *
 */
 
require_once('jsonDefinitions.php');
require_once('jsonPdf.php');
#require_once("/usr/share/php/fpdf/fpdf.php");

class jsonExample
{
  public $caseId;
  public $locationId;
  public $link;
  public $requestType;

  // CTOR with no args.
  public function __construct()
  {
    // Grad a database connection
    $this->link = $this->getConnection();
  }

  public function getConnection()
  {
    $link = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE );
    if(mysqli_connect_errno()) 
    {
        die('Failed to connect to server: ' . mysqli_connect_error());
    }
    return $link;
  }

  public function debug_write($handle, $string)
  {
    $debug = 1;
    if ($debug)
    {
        fwrite($handle, $string);
    }
  }

  public function authenticate($parsedObject, $debug_log)
  {
    // Determine if we're authenticating by username or userid
    $authValue = 0;
    $theUserName = "";
    if ($this->requestType == 10)
    {
        $user = getUserId($user, $this->link);
        $authValue = passwordCheck($user, $pass, $this->link, $debug_log);
    }
    else
    {
        $theUserName = getUserName($user, $link);
        $authValue = passwordCheck($user, $pass, $link, $debug_log);
    }
    if ($authValue == 401)
    {
        header("HTTP/1.0 401 Not Authorized");
        return;
    }
  }

  public function passwordCheck($user, $pass, $link)
  {
    // Verify the password.  If it doesn't match the user id, return.
    $passQuery = "SELECT passhash FROM user
                  WHERE id = ? and passhash = ?";
    $stmt = $link->prepare($passQuery);
    $stmt->bind_param('ss',$user, $pass);
    $stmt->execute();
    $stmt->store_result();
    $rowCount = $stmt->num_rows;
    #debug_write($debug_log, "passwordCheck: user = $user, pass = $pass, rowcount = $rowCount\n\n");
    if ($rowCount < 1)
    {
      return 401;
    }
    return 200;
  }

  public function processRequest()
  {
    if ($this->requestType == 1)
      $this->updateData();
    else if ($this->requestType == 2)
      $this->addSignature();
    else if ($this->requestType == 3)
      $this->fetchPdf();
    else if ($this->requestType == 4)
      $this->fetchTodaysCases();
  }


  //
  // Method fetchTodaysCases: return an associative array of todays cases, give a case ID.
  //
  public function fetchTodaysCases()
  {
    //
    // Return an associative array of today's cases associated with this provider. 
    $locationId = $this->parsedObject->location_id;
    $dateTime = new DateTime();
    $now = $dateTime->format('Y-m-d');
    $key = getKey();

    // Query: all the cases marked as "created_by" for this location from today.
    $query = "SELECT id, patient_id, created_by FROM json_case WHERE location_id = ? AND date_of_service = ?";
    $stmt = $link->prepare($query);
    $stmt->bind_param('is', $locationId,$now);
    $querySuccess = $stmt->execute();
    $numRows = $stmt->num_rows;
    $stmt->bind_result($id, $patient_id, $creator);
    $stmt->store_result();
    $rowCount = $stmt->num_rows;

    $i = 0;
    $assocRow = array();
    while ($stmt->fetch())
    {
      // Create the associative array element for this case
      $assocRow[$i]['case_id'] = $id;
      $patientId = getPatientIdForCase($id, $link);
      $encounterType = getEncounterNameForCase($id, $link);
      $scheduledTime = getScheduledTimeForCase($id, $link);
      $assocRow[$i]['encounter_type'] = $encounterType;
      $assocRow[$i]['patient_id'] = $patient_id;
      $assocRow[$i]['patient_first_name'] = getFirstNameForPatientId($patient_id, $link);
      $assocRow[$i]['patient_last_name'] = getLastNameForPatientId($patient_id, $link);
      $assocRow[$i]['provider_id'] = $creator;
      $assocRow[$i]['provider_first_name'] = getFirstNameForProviderId($user, $link);
      $assocRow[$i]['provider_last_name'] = getLastNameForProviderId($user, $link);
      $assocRow[$i]['scheduled_time'] = $scheduledTime;
      // Log the access 
      $link2 = getConnection();
      $logUpdateQuery = "INSERT INTO json_access_log SET action = 'QUERY', data_accessed = 'patient_id, patient_first_name, patient_last_name, provider_id, provider_first_name, provider_last_name, scheduled_time', patient_id = ?, user_id = ?";
      $stmt2 = $link2->prepare($logUpdateQuery);
      $stmt2->bind_param('ii',$patient_id, $user);
      $querySuccess = $stmt2->execute();
      $stmt2->close();
      $i++;
    }

    $theString = json_encode($assocRow);
    header("HTTP/1.0 200 Accepted");
    print $theString;
    debug_write($debug_log, "eps::fetchTodaysCases :The response sent is: $theString\n"); 
  }

  //
  // Method fetchPdf: generate and return a PDF file, based on the case ID
  //
  public function fetchPdf()
  {
    $theObject = new jsonPdf($this->caseId);
    $theObject->generatePage();
    $filename = "pdfs/".$thisCase.".pdf";
    header('Content-Disposition: attachment; filename='.$filename);
    header('Content-Type: application/pdf-stream');
    header('Content-Length: '.filesize($filename));
    readfile($filename);
    unlink($filename);
  }
 
  //
  // Method addSignature: upload a JPEG image containing a signatue, store in the json_signature
  //                      table along with supporting data.
  // Arguments:
  //
  public function addSignature()
  { 
    // 
    // Receive an uploaded file.
    #debug_write($this->debug_log, "jsonAddSignature: enter\n");
    $encounterId = $theObject->encounter_id;
    $providerName = $theObject->provider_name;
    $upFile = $_FILES['uploadfile']['tmp_name'];
    #debug_write($this->debug_log, "jsonAddSignature upfile = $upFile\n");
  
    // 1) Build the insert query and execute it.
    $zero = 0;
    $insertQuery = "INSERT INTO json_signature (image, encounter_id, user_id, provider_name, is_preop) VALUES (?,?,?,?,?)";
    $stmt = $link->prepare($insertQuery);
    #debug_write($this->debug_log, "eps:updateSignature insertQuery = $insertQuery providerName = $providerName\n");
    $null = NULL;
    $stmt->bind_param("biisi", $null, $encounterId, $user,$providerName, $zero);
    $stmt->send_long_data(0, file_get_contents($upFile));
  
    if (!$stmt->execute()) {
      debug_write($this->debug_log, "jsonAddSignature: insert failed with error %s", $stmt->error);
    }
    $id = $link->insert_id;
    if ($id == 0)
    {
      debug_write($this->debug_log, "eps::updateSignature db insert failed, Returning 400\n\n");
      header("HTTP/1.0 400 Bad Request");
      return;
    }
  
    // 2) Log the signature upload into the access file.
    $logUpdateQuery = "INSERT INTO json_access_log SET action = 'CHANGE', data_accessed = 'signature', patient_id = ?, user_id = ?";
    $stmt = $link->prepare($logUpdateQuery);
    $stmt->bind_param('ii', $patientId, $user);
    $querySuccess = $stmt->execute();
  
    header("HTTP/1.0 200 Accepted");
    #debug_write($this->debug_log, "jsonAddSignature: normal success, returning 200\n\n");
  }

  //
  // Method updateData: take a piece of data, encrypt it, and insert into the json_data table.
  // Arguments:
  // 
  public function udpateData()
  {
    $encounterId = $theObject->encounter_id;
    $localTimestamp = $theObject->local_timestamp;
    $varName = $theObject->field;
    $data = $theObject->field_value;
    debug_write($this->debug_log, "jsonUpdateData::update data: data is $data\n");
    $key = getKey();
    
    // Verify that there is a data value to set.
    if (isset($data))
    {
      // First query: accomplish three things:
      //                                1) Check that this encounter exists 
      //                                2) Get patient id for access log 
      //                                3) Get active flag for encounter since adding data activates an inactive encounter
      $patientIdQuery = "SELECT patient_id, active, case_id FROM json_encounter WHERE id = ?";
      $stmt = $link->prepare($patientIdQuery);
      $stmt->bind_param('i', $encounterId);
      $querySuccess = $stmt->execute();
      $stmt->bind_result($patientId, $active, $caseId);
    
      if (!$querySuccess || !$stmt->fetch()){
        #debug_write($this->debug_log, "jsonUpdateData::update data: encounter not found for encounterId:{$encounterId} returning 400\n\n");
        header("HTTP/1.0 400 Bad Request");
        return;
      }
      $stmt->close();
    
      // Retrieve the property id for this field name
      $propIdQuery = "SELECT id FROM json_property WHERE name = ?";
      $propStmt = $link->prepare($propIdQuery);
      $propStmt->bind_param('s', $varName);
      $querySuccess = $propStmt->execute();
      #debug_write($this->debug_log, "jsonUpdateData::update data: searching for property $varName, success is $querySuccess\n");
      $propStmt->bind_result($id);
      $propStmt->close();
    
      // If a property ID was returned, game on! Insert into the data table, and calculate what kind of operation to log, depending on prior existence
      if ($propStmt->fetch())
      {
        $actionQuery = "SELECT AES_DECRYPT(data, '$key') FROM json_data WHERE property_id = ? AND encounter_id = ? ORDER BY local_timestamp DESC LIMIT 1";
        $actionStmt = $link->prepare($actionQuery);
        $actionStmt->bind_param('ii', $id, $encounterId);
        $querySuccess = $actionStmt->execute();
        $actionStmt->bind_result($oldData);
        $actionStmt->fetch();
        $actionStmt->close();
    
        // If there is old data, it's CHANGE or DELETE...
        if (isset($oldData)){
          if (trim($data) === ""){
            // If new data is blank string, it's a DELETE
            $actionString = "DELETE";
          }
          else{
            // Else it's a CHANGE
            $actionString = "CHANGE";
          }
        }
        else {
          // But if there was no no old data, this is a SET
          $actionString = 'SET';
        }
    
        // At last! Do the insert.
        $insertQuery = "INSERT INTO json_data SET property_id = ?, encounter_id = ?, data = AES_ENCRYPT(?, '$key'), user_id = ?, local_timestamp = ?";
        $insertStmt = $link->prepare($insertQuery);
        $insertStmt->bind_param('iisis', $id, $encounterId, $data, $user, $localTimestamp);
        $querySuccess = $insertStmt->execute();
        #debug_write($this->debug_log, "jsonUpdateData::update data: insert query is $insertQuery propId:{$id} encounter $encounterId, data:{$data} success is $querySuccess\n");
        $insertStmt->close();
    
        if ($querySuccess){
          // The insert succeeded. Update the modfied string for logging. Mark the encounter active.
          $modString = "Property:{$varName}, Data:{$data}";
          if (!$active){
            $activateQuery = "UPDATE json_encounter SET active = 1, provider = ? WHERE id = ?";
            $activateStmt = $link->prepare($activateQuery);
            $activateStmt->bind_param('ii', $user, $encounterId);
            $querySuccess = $activateStmt->execute();
            $activateStmt->close();
          }
        }
        else {
          // The insert failed. Log the error.
          debug_write($this->debug_log, "jsonUpdateData::update data: insert failed -- property:{$varName} propId:{$id} data:{$data}\n");
        }
    
        // If we modified the date_of_service, propagate the change to to encounter anchor for consistency.
        if (strcmp($varName, "scheduled_date") === 0){
          $updateQuery = "UPDATE json_encounter SET date_of_service = ? WHERE id = ?";
          $updateStmt = $link->prepare($updateQuery);
          $updateStmt->bind_param('si', $data, $encounterId);
          $querySuccess = $updateStmt->execute();
          if (!$querySuccess){
            debug_write($this->debug_log, "jsonUpdateData::update data failed to update encounter anchor:{$encounterId} for varname:{$varName} with data:{$data}\n");
          }
          $updateStmt->close();
        }
      }
      // If we didn't get a property id for the field value, this is a fail, return 400.
      else {
        $propStmt->close();
        debug_write($this->debug_log, "jsonUpdateData::update data no property id for property:{$varName}\n");
        header("HTTP/1.0 400 Bad Request");
        return;
      }
    }
    
    // If modString is set, update the access log.
    if (isset($modString))
    {
      $modQuery = "INSERT INTO access_log SET action = ?, data_accessed = ?, patient_id = ?, user_id = ?, local_timestamp = ?, case_id = ?, encounter_id = ?";
      $stmt = $link->prepare($modQuery);
      $stmt->bind_param('ssiisii', $actionString, $modString, $patientId, $user, $localTimestamp, $caseId, $encounterId);
      $logSuccess = $stmt->execute();
    
      $stmt->close();
    }
    
    header("HTTP/1.0 200 Accepted");
    #debug_write($this->debug_log, "jsonUpdateData::update data -- normal exit, returning 200\n\n");
  }
}

  // Script action: parse the input, and execute method processRequest on it. 
  $rawData = $_POST['data'];
  $parsedObject = json_decode($rawData, 0);
  // Get the request type for this object, because we need it in authentication.
  $this->requestType = sanitize($this->link, $parsedObject->request_type);

  $this->caseId = $parsedObject->caseId;
  $this->locationId = $parsedObject->locationId;
  $this->parsedObject = $parsedObject;
  $this->this->debug_log = $debug_log;

  $theObject = new jsonExample();
  $theObject->authenticate($parsedObject, $debug_log);
  $theObject->processRequest();
?>:
