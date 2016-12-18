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

$MN = "HouseTable_UpdateData.php";
logBegin($MN);


  $id = $_REQUEST['id'] ;
  $value = $_REQUEST['value'] ;
  $column = $_REQUEST['columnName'] ;
  $columnPosition = $_REQUEST['columnPosition'] ;
  $columnId = $_REQUEST['columnId'] ;
  $rowId = $_REQUEST['rowId'] ;
  
  $retValue = -1;
  logDebug($MN, "id=".$id);
  logDebug($MN, "value=".$value);
  logDebug($MN, "column=".$column);
  logDebug($MN, "columnPosition=".$columnPosition);
  logDebug($MN, "columnId=".$columnId);
  logDebug($MN, "rowId=".$rowId);
  
  if(isset($id))
  {
      
      $item = new Label();
      $item->loadById($id);
      logDebug($MN, "Label=".$item->toString());
      switch ($columnId) {
          case 1:
              $item->setLabelKey($value);
              break;
          case 2:
              $item->setLabel($value);
              break;
          case 3:
              $item->setChecked($value);
              break;
          
          default:
              logDebug($MN, "default");
              break;
      }
      logDebug($MN, "Label=".$item->toString());
      
      $item->save();
      logDebug($MN, "saved");
      logDebug($MN, "Label=".$item->toString());
      switch ($columnId) {
          case 1:
              $retValue = $item->getLabelKey();
              break;
          case 2:
              $retValue = $item->getLabel();
              break;
          case 3:
              $retValue = ($item->getChecked()==1?"on":"off");
              break;
          
          default:
              $retValue = $value;
              break;
      }
      
  }
  
  logDebug($MN, "retValue=".$retValue);
  
  logEnd($MN);
echo $retValue;

?>
