<?php
/*
 * @desc
 * Sample methods from pdfGen.php
 * Class pdfGen creates and returns a PDF from a JSON objecta . The PDF is generated using FPDF
 * with a location-specific background image, and variables printed on the page.  Queries are 
 * prepared statements to a mysql database * using the MySQLi extension.
 *
 */
 
require_once('jsonDefinitions.php');
#require_once("/usr/share/php/fpdf/fpdf.php");

class jsonPdf
{
  public $caseId;
  public $locationId;
  public $link;
  public $pdf;
  public $cmBgImagePath;

  // CTOR with no args.
  public function __construct($caseId)
  {
      $this->link = $this->getConnection();
      $this->caseId = $caseId;
      $this->locationId = $this->getLocation($caseId, $link);
  }

  public function getConnection()
  {
    $link = new mysqli(MED_HOST, MED_USER, MED_PASSWORD, MED_DATABASE );
    if(mysqli_connect_errno()) 
    {
        die('Failed to connect to server: ' . mysqli_connect_error());
    }
    return $link;
  } 


  public function displayItem()
  {
    $debug_log = fopen('/var/log/httpd/eps_log_qa', 'a');
    $locationList = array("patient_name" => array("x" => 50, "y" => 66), 
                        "ob_start_time" => array("x" => 230, "y" => 66),
                        "patient_mrn" => array("x" => 455, "y" => 47), 
                        "patient_account_number" => array("x" => 50, "y" => 675),
                        "ob_diagnosis" => array("x" => 60, "y" => 96),
                        "ob_second_diagnosis" => array("x" => 60, "y" => 137),
                        "ob_procedure" => array("x" => 60, "y" => 175),
                        "ob_preop_assessment_complete" => array("x" => 43, "y" => 227),
                        "ob_consent_obtained" => array("x" => 216, "y" => 227),
                        "ob_consent_from_patient" => array("x" => 329, "y" => 227),
                        "ob_consent_from_poa" => array("x" => 404, "y" => 227),
                        "ob_consent_emergency" => array("x" => 475, "y" => 227),
                        "ob_start_time2" => array("x" => 70, "y" => 251),
                        "ob_stop_time" => array("x" => 180, "y" => 251),
                        "ob_timeout_complete"=> array("x" => 268, "y" => 249), 
                        "ob_timeout_complete_at"=> array("x" => 378, "y" => 254), 
                        "ob_preprocedure_systolic" => array("x" => 122, "y" => 484), 
                        "ob_preprocedure_diastolic" => array("x" => 122, "y" => 484), 
                        "ob_preprocedure_pulse" => array("x" => 122, "y" => 500), 
                        "ob_vitals_recorded_at" => array("x" => 116, "y" => 463), 
                        "ob_placement_time1" => array("x" => 164, "y" => 463), 
                        "ob_placement_time2" => array("x" => 210, "y" => 463), 
                        "ob_systolic"=> array("x" => 116, "y" => 484), 
                        "ob_systolic1" => array("x" => 164, "y" => 484), 
                        "ob_systolic2" => array("x" => 212, "y" => 484), 
                        "ob_diastolic"=> array("x" => 116, "y" => 484), 
                        "ob_diastolic1" => array("x" => 179, "y" => 484), 
                        "ob_diastolic2" => array("x" => 228, "y" => 484), 
                        "ob_pulse1" => array("x" => 168, "y" => 500), 
                        "ob_pulse2" => array("x" => 215, "y" => 500), 
                        "ob_touhy_needle" => array("x" => 41, "y" => 302), 
                        "ob_touhy_needle_at"  => array("x" => 228, "y" => 302),
                        "ob_sterile_prep"=> array("x" => 431, "y" => 314), 
                        "ob_lidocaine_skin_wheal"=> array("x" => 431, "y" => 338), 
                        "ob_drug_name"=> array("x" => 50, "y" => 320), 
                        "ob_drug_amount" => array("x" => 900, "y" => 585), 
                        "ob_drug_concentration" => array("x" => 900, "y" => 585), 
                        "ob_drug_unit" => array("x" => 900, "y" => 585), 
                        "ob_additive"=> array("x" => 66, "y" => 337), 
                        "ob_additive_amount"=> array("x" => 900, "y" => 585), 
                        "ob_additive_name"=> array("x" => 900, "y" => 585), 
                        "ob_catheter_placed"=> array("x" => 332, "y" => 337), 
                        "ob_test_dose"=> array("x" => 41, "y" => 354), 
                        "ob_test_dose_amount"=> array("x" => 138, "y" => 354), 
                        "ob_provider" => array("x" => 47, "y" => 652),
                        "ob_pulse"=> array("x" => 100, "y" => 585), 
                        "ob_procedure_notes"=> array("x" => 70, "y" => 582), 
                        "ob_cem_rate"=> array("x" => 50, "y" => 419), 
                        "ob_cem_additive"=> array("x" => 87, "y" => 438), 
                        "ob_cem_notes"=> array("x" => 50, "y" => 585), 
                        "ob_negative_test_dose"=> array("x" => 41, "y" => 372), 
                        "ob_notes"=> array("x" => 35, "y" => 583), 
                        "ob_procedure_other"=> array("x" => 217, "y" => 372), 
                        "patient_name2" => array("x" => 500, "y" => 685), 
                        "ob_skin_prep" => array("x" => 900, "y" => 585), 
                        "ob_cem_caesarean"=> array("x" => 43, "y" => 565), 
                        "ob_cem_caesarean_at"=> array("x" => 220, "y" => 565), 
                        "ob_cem_caesarean_for" => array("x" => 345, "y" => 565));

    $locationArray = $locationList[$item];
    $scheduledTime = "block_scheduled_time";
    $x = $locationArray['x'];
    $y = $locationArray['y'] + $offset;
    if ( (strcmp($item, "patient_name") == 0) || (strcmp($item, "patient_name2") == 0) )
      $value = getFirstNameForPatientId($patientId, $link)." ".getLastNameForPatientId($patientId, $link);
    else
      $value = getValueForItem($item, $encounterId, $link);

    #debug_write($debug_log, " for item $item, got x $x,  y $y with value $value\n");
    if (strlen($value) > 0)
    {
      if (isItemCheckbox($item) )
      {
       if (strcmp($value, 'YES') == 0)
          $value = x;
        else
          $value = '';
      }
      $pdf->Text($x, $y, $value);
    }
  }

  public function populatePage($theObject)
  {
    $pdf->Image($theImage, 0, 0, $size[0], $size[1]);
    $pdf->SetFont('Arial', '', 10);

    // Loop through the list of fields that are labor specific, teletyping them to the page.
    $pageData = array("patient_name", "patient_mrn", "ob_provider", "ob_datetime", "patient_account_number", "ob_diagnosis", 
         "ob_second_diagnosis", "ob_procedure", "ob_preop_assessment_complete",
        "ob_consent_obtained", "ob_consent_from_patient", "ob_consent_from_poa", "ob_consent_emergency", "ob_start_time", 
        "ob_stop_time", "ob_touhy_needle", "ob_touhy_needle_at", "ob_drug_amount",
        "ob_sterile_prep", "ob_lidocaine_skin_wheal", "ob_drug_name", "ob_additive",
        "ob_preprocedure_systolic", "ob_preprocedure_pulse",
        "ob_catheter_placed", "ob_test_dose", "ob_test_dose_amount", "ob_start_time2",
        "ob_cem_rate", "ob_cem_additive", "ob_negative_test_dose", "ob_notes",
        "ob_procedure_other", "ob_cem_caesarean", "ob_cem_caesarean_at", "ob_cem_caesarean_for",
        "ob_timeout_complete", "ob_timeout_complete_at", "ob_vitals_recorded_at",
        "ob_negative_test_dose", "ob_systolic1", "ob_systolic2",
        "ob_pulse1", "ob_pulse2", "ob_preprocedure_time",
        "ob_placement_time1", "ob_placement_time2");
    foreach ($pageData as $dataItem)
    {
      displayItem($dataItem, $encounterId, $patientId, $link, $pdf, $offset);
    }
    $y = 617 + $offset;
    displaySignature($encounterId, 310, $y, $pdf, $link, $debug_log);
    displayPatientInfo($patientId, $encounterId, $pdf, $link, $debug_log);
  } 

  public function generatePage($theObject)
  {
    $self->initializePdf();
    $self->populatePage();
    $self->writeFile();
  } 
 
  public function initializePdf()
  {
    $size = array(612, 792);
    $this->pdf = new PDF_MemImage('P', 'pt', $size);
    $this->pdf->AddPage();
    $location = $this->locationId;

    // Use a background image appropriate to the location
    if ($location === 0){
        $theImage = 'images/Image1.png';
    }
    else {
        $theImage = 'images/Image2.png';
    }
    $this->cmBgImagePath = $theImage;
    $this->pdf->Image($theImage, 0, 0, $size[0], $size[1]);
    $this->pdf->SetFont('Arial', 'B', 8); 
  }
}
?>:
