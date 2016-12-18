<?php 
ob_start();
header("Content-Type: text/html; charset=utf-8");

//display_errors = On
ini_set("display_errors", "0");
session_start();
header('Cache-control: private');
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Easy set variables
     */
require_once("../common/Connect.php");
require_once("../common/Functions.php");
require_once("../user/User.class.php");
require_once("Language.php");
require_once("Label.php");

$MN = "LabelTable_DeleteData.php";
   

$id = $_REQUEST['id'];
logDebug($MN, "id=" . $id);
$retValue = "Error";
try {
    if (isset($id)) {
        
        $rowsAffected = Label::deleteById($id);
        logDebug($MN, "rowsAffected=" . $rowsAffected);
        if (isset($rowsAffected) && $rowsAffected > 0)
            $retValue = "ok";
    }
    else
        $retValue = "Incorect parameter:[" . $id . "]";

    logDebug($MN, "retValue=" . $retValue);
} catch (Exception $ex) {
    $retValue = $ex->getMessage();
}
logEnd($MN);
echo $retValue;
?>
