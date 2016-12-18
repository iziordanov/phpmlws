<?php 

/* * ****************************** HEAD_BEG ***********************************
 *
 * Project                	: phpmlws
 * Module 			: ml
 * Responsible for module 	: i_z_iordanov@yahoo.com
 *
 * Filename               	: Label.php
 *
 * Database System        	: ORCL, MySQL
 * Created from			: i_z_iordanov@yahoo.com
 * Date Creation		: 16.12.2016
 * -----------------------------------------------------------------------------
 *                        Description
 * -----------------------------------------------------------------------------
 * @TODO Insert some description.
 * 	 
 * -----------------------------------------------------------------------------
 *                        History
 * -----------------------------------------------------------------------------
 * HISTORY:
 * <br>--- $Log: Label.php,v $
 * <br>---
 * <br>--- 
 *
 * ******************************** HEAD_END ************************************
 */

require_once("Connect.php");
require_once("Language.php");

class Label {

    public static function googleTranslate($key, $lngIn, $lngOut) {
        $MN = "ml:Label.googleTranslate(key, lngIn, lngOut)";
        $ST = logBegin($MN);
        //logDebug($MN, "key=" . $key);
        //logDebug($MN, "lngOut=" . $lngOut);
        $retValue = new GoogleTranslate($key, $lngIn, $lngOut);
        //logDebug($MN, "translatedText=" . $retValue->translatedText);
        //logDebug($MN, "mb_detect_encoding=" . mb_detect_encoding($retValue->translatedText));
        $retStr = $retValue->translatedText;
        //$retStr = FixEncoding($retStr);
        /* if(mb_detect_encoding($retStr)=='UTF-8')
          {
          $retStr = iconv("UTF-8", "CP1251", $retStr);
          } */
        $retStr = trim($retStr);
        //logDebug($MN, "retStr='" . $retStr . "' ");
        //logDebug($MN, "mb_detect_encoding=" . mb_detect_encoding($retStr));
        logEnd($MN, $ST);
        return $retStr;
    }

    /**
     * ***************************************************************************
     * Static Methods Declarations
     * ***************************************************************************
     */

    /**
     * Load stored Label by key and Language abbreviation
     * 
     * return Array of Labels fields (select *)
     */
    public static function getLabelsTableData($lngAbbr, $checked) {
        $MN = "ml:Label.getLabels()";
        $ST = logBegin($MN);

        //logDebug($MN, "lngAbbr = " . $lngAbbr);
        //logDebug($MN, "checked = " . $checked);
        if ($lngAbbr == null || $lngAbbr == '') {
            $lngAbbr = Language::getDefaultLanguage()->getId();
        }
        $strSQL = "SELECT lb.* FROM " . Label::TABLE_NAME . " lb, ";
        $strSQL .= " " . Language::TABLE_NAME . " ln ";
        $strSQL .=" WHERE lb." . Label::COL_LABEL_LANGUAGE_ID . " = ln." . Language::COL_LNG_ID_NAME . " ";
        $strSQL .=" AND ln." . Language::COL_LNG_ABBR_NAME . " = '" . $lngAbbr . "' ";
        $strSQL .=" AND lb." . Label::COL_LABEL_CHECKED . " = '" . $checked . "' ";

        $aLblArray = Connect::dbExecuteSQL($strSQL);
        //logDebug($MN, "rows cnt=" . count($aLblArray));

        for ($index = 0, $max_count = sizeof($aLblArray); $index < $max_count; $index++) {
            $aLblArray[$index][2] = DbRevEncoding($aLblArray[$index][2]);
        }

        logEnd($MN, $ST);
        return $aLblArray;
    }


    /**
     * Load stored Label by key and Language abbreviation
     * 
     * return Label object
     */
    public static function GetLanguageArray($strLngAbbr) {
        $MN = "ml:Label.GetLanguageArray()";
        $ST = logBegin($MN);
        $aLblArray = array();
        //logDebug($MN, "strLngAbbr = " . $strLngAbbr);
        $label = new Label();
        if (!$strLngAbbr == null && !$strLngAbbr == '') {
            $strSQL = "SELECT lb." . Label::COL_LABEL_KEY . ", lb." . Label::COL_LABEL_TEXT . " FROM " . Label::TABLE_NAME . " lb, ";
            $strSQL .= " " . Language::TABLE_NAME . " ln ";
            $strSQL .=" WHERE lb." . Label::COL_LABEL_LANGUAGE_ID . " = ln." . Language::COL_LNG_ID_NAME . " ";
            $strSQL .=" AND ln." . Language::COL_LNG_ABBR_NAME . " = '" . $strLngAbbr . "' ";


            $aLblArray = Connect::dbSelectKeyValueSQL($strSQL);
            //logDebug($MN, "rows cnt=" . count($aLblArray));

            /*
              for ($index = 0, $max_count = sizeof( $aLblArray ); $index < $max_count; $index++)
              {
              $aLblArray[$index][2] = DbRevEncoding($aLblArray[ $index ][2]);

              }
             */
        }

        logEnd($MN, $ST);
        return $aLblArray;
    }

    /**
     * Load stored Label by key and Language abbreviation
     * 
     * return Label object
     */
    public static function loadByKeyLanguage($key, $strLngAbbr) {
        $MN = "ml:Label.loadByKeyLanguage()";
        $ST = logBegin($MN);
        //logDebug($MN, "key = " . $key);
        //logDebug($MN, "strLngAbbr = " . $strLngAbbr);
        $label = new Label();
        if (!$key == null && !$key == '' && !$strLngAbbr == null && !$strLngAbbr == '') {
            $strSQL = "SELECT lb." . Label::COL_LABEL_ID . " FROM " . Label::TABLE_NAME . " lb, ";
            $strSQL .= " " . Language::TABLE_NAME . " ln ";
            $strSQL .=" WHERE lb." . Label::COL_LABEL_LANGUAGE_ID . " = ln." . Language::COL_LNG_ID_NAME . " ";
            $strSQL .=" AND ln." . Language::COL_LNG_ABBR_NAME . " = '" . $strLngAbbr . "' ";
            $strSQL .=" AND lb." . Label::COL_LABEL_KEY . " = '" . $key . "' ";

            $lblId = Connect::dbSelectValueSQL($strSQL);

            if (!$lblId == null && !$lblId == '') {
                $label = new Label();
                $label->loadById($lblId);
                //logDebug($MN, "Return=" . $label->toString());
            }
        }

        logEnd($MN, $ST);
        return $label;
    }

    /**
     * Load stored Label by key and Language abbreviation
     * 
     * return Label object
     */
    public static function checkExisting($key, $strLngAbbr) {
        $MN = "ml:Label.checkExisting()";
        $ST = logBegin($MN);

        //logDebug($MN, "key = " . $key);
        //logDebug($MN, "strLngAbbr = " . $strLngAbbr);
        if (!$key == null && !$key == '' && !$strLngAbbr == null && !$strLngAbbr == '') {
            $strSQL = "SELECT count(lb." . Label::COL_LABEL_ID . ") FROM " . Label::TABLE_NAME . " lb, ";
            $strSQL .= " " . Language::TABLE_NAME . " ln ";
            $strSQL .=" WHERE lb." . Label::COL_LABEL_LANGUAGE_ID . " = ln." . Language::COL_LNG_ID_NAME . " ";
            $strSQL .=" AND ln." . Language::COL_LNG_ABBR_NAME . " = '" . $strLngAbbr . "' ";
            $strSQL .=" AND lb." . Label::COL_LABEL_KEY . " = '" . $key . "' ";

            //$language = new Language();
            $cnt = Connect::dbSelectValueSQL($strSQL);
            //logDebug($MN, "cnt=" . $cnt);
            if ($cnt > 0) {
                return true;
            }
        }

        logEnd($MN, $ST);
        return false;
    }

    /**
     * This method is involved to cteate a new records in Label DB Table for the 
     * provided key value; It do nor retur any value. It retreevs all RLanguages 
     * rows definad in Language table and for every one language creats an new 
     * Label record with coresponding key value. Bu default the checked is false 
     * and the label text is equal tyo label key with appendix of labguage 
     * abbreviation value.
     * 
     */
    public static function createLabels($key) {
        $MN = "ml:Label.createLabels()";
        $ST = logBegin($MN);

        //logDebug($MN, "key=" . $key);


        $languages = Language::getAll();
        //logDebug($MN, "Staring insert labels for key=" . $key);
        if ($languages != null && count($languages) > 0) {
            foreach ($languages as $lang) {
                //Che for existing value in table
                $exist = Label::checkExisting($key, $lang->getLngAbbr());
                //logDebug($MN, "exist=" . $exist);
                if ($exist) {
                    continue;
                }
                // Set appropriate column values
                $newRow = new Label();
                $newRow->setLanguage($lang);
                $newRow->setLabelKey($key);
                $labelText = Label::googleTranslate($key, "en", strtolower($lang->getLngAbbr()));
                if ($labelText) {
                    $newRow->setLabel($labelText);
                    $newRow->setChecked(false);
                } else {
                    //$newRow->setLabel($key."_".$lang->getLngAbbr());
                    $newRow->setLabel($key);
                    $newRow->setChecked(false);
                }

                // INSERT the new row to the database
                $newRow->save();
                //logDebug($MN, "Succesfully Inserted " . $newRow->toString());
            }
        }

        logEnd($MN, $ST);
    }

    /**
     * ***************************************************************************
     * Methods Declarations
     * ***************************************************************************
     */
    function setFromArray($aLangArray) {
        $MN = "ml:Label.setFromArray()";
        $ST = logBegin($MN);
        //logDebug($MN, "aLangArray = " . count($aLangArray));

        if (!$aLangArray == null) {
            $this->setId($aLangArray[0]);
            $this->setLabelKey($aLangArray[1]);
            //$this->setLabel($aLangArray[ 2 ]);

            $this->setLabel(DbRevEncoding($aLangArray[2]));
            $this->setChecked($aLangArray[3]);
            $this->setLanguageByLanguageID($aLangArray[4]);
        }
        //logDebug($MN, "Label key = " . $this->getLabelKey());
        logEnd($MN, $ST);
        return $this;
    }
    
    function loadFromArray($anArray) {
        $MN = "ml:Label.loadFromArray()";
        $ST = logBegin($MN);
        //logDebug($MN, "anArray: " . prArr($anArray));

        if (!$anArray == null) {
            $this->setId($anArray[Label::COL_LABEL_ID]);

            $this->setLabelKey($anArray[Label::COL_LABEL_KEY]);
            ////logDebug($MN, "Label key1 = " . $this->getLabelKey());
            $this->setLabel($anArray[Label::COL_LABEL_TEXT]);
            $this->setChecked($anArray[Label::COL_LABEL_CHECKED]);

            $this->setLanguageByLanguageID($anArray[Label::COL_LABEL_LANGUAGE_ID]);
            //logDebug($MN, "Label key = " . $this->getLabelKey());
        }
        logDebug($MN, "id = " . $this->getId());
        logEnd($MN, $ST);
        return $this;
    }

    /**
     * Load stored Label by ID
     * 
     * return Label object
     */
    public function loadById($labelId) {
        $MN = "ml:Label.loadById()";
        $ST = logBegin($MN);
        logDebug($MN, "lngId = " . $labelId);

        if (!$labelId == null && !$labelId == '') {
            $strSQL = "SELECT ".Label::getColumns()." FROM " . Label::TABLE_NAME;
            $strSQL .=" WHERE " . Label::COL_LABEL_ID . " = ?";
            //$language = new Language();
            //$aLblArray = Connect::dbExecuteSQL($strSQL);
            $bound_params_r = array('i', $labelId);
            $conn = new Connect();
            $result_r = $conn->preparedSelect($strSQL, $bound_params_r);
            logDebug($MN, "count(result_r)=" . count($result_r));
            $data = $result_r;
            if (count($result_r) == 1) {
                $data = $result_r[0];
            }
            logDebug("$MN", "ret Value=" . prArr($data));

            $this->loadFromArray($data);
            //$this->setFromArray($aLblArray[0]);
        }


        logDebug($MN, $this->toString());
        logEnd($MN, $ST);
        return $this;
    }
    
     public static function deleteById($id) {
        $MN = "ml:Label.deleteById()";
        $ST = logBegin($MN);
        logDebug($MN, "id = " . $id);
        $retValue = 0;
        try {
            //$this->setId($id);
            $sql = "DELETE " .
                    " FROM " . Label::TABLE_NAME . " " .
                    " WHERE " . Label::COL_LABEL_ID . "=?";
            $bound_params_r = array("i", $id);
            $conn = new Connect();
            $retValue = $conn->preparedDelete($sql, $bound_params_r);
        } catch (Exception $ex) {
            $retValue = $ex->getMessage();
        }
        logDebug($MN, "retValue = " . $retValue);
        logEndST($MN, $ST);
        return $retValue;
    }

    public function save() {
        $MN = "ml:Label.save()";
        $ST = logBegin($MN);

        logDebug($MN, "is_object(this)=" . is_object($this));
        logDebug($MN, "ID=" . $this->getId());
        if (is_object($this)) {
            logDebug($MN, "ID=" . $this->getId());
            if ($this->getId() == null || $this->getId() == "") {
                logDebug($MN, "Insert ");
                $strSQL = "INSERT INTO " . Label::TABLE_NAME . " (" . Label::getColumnsNoID() . ") ";
                $strSQL .=" VALUES( '" . $this->getLabelKey() . "' ,  '";
                $strSQL .= DbConvEncoding($this->getLabel()) . "', ";

                $strSQL .= $this->getChecked() . ", " .
                        $this->getLanguage()->getId() . " ) ";

                //$language = new Language();
                $lngID = Connect::dbInsertSQL($strSQL);

                $this->LoadById($lngID);
            } else {
                logDebug($MN, "Update ");

                $strSQL = "UPDATE " . Label::TABLE_NAME .
                        " SET " . Label::COL_LABEL_TEXT . "= ? " .
                        ", " . Label::COL_LABEL_CHECKED . "= ? ";
                $strSQL .=" WHERE " . Label::COL_LABEL_ID . "= ? ";

                $textLbl = DbConvEncoding($this->getLabel());
                $bound_params_r = array("sii",
                    $textLbl,
                    $this->getChecked(),
                    $this->getId());

                $conn = new Connect();
                $id = $conn->preparedDelete($strSQL, $bound_params_r);

                $this->LoadById($this->getId());
            }
        }

        logEnd($MN, $ST);
        return $this;
    }

    public function toString() {
        $strRetValue = "";
        $strRetValue .= "Label[" . $this->getId() . ", '" . $this->getLabelKey();
        //$strRetValue .= "', " . ($this->getLanguage()!=null?$this->getLanguage()->getLngAbbr():"");
        $strRetValue .= ", '" . $this->getLabel() . "', ";
        $strRetValue .= ", " . $this->getChecked() . "]";

        return $strRetValue;
    }
    
    public function toJSON() {
        return json_encode($this);
    }

    /**
     * ***************************************************************************
     * Getters and Setters Declarations
     * ***************************************************************************
     */
    public function getId() {
        return $this->label_id;
    }

    public function setId($intId) {
        $this->label_id = $intId;
        $this->DT_RowId = $intId;
    }

    public function getLabelKey() {
        return $this->label_key;
    }

    public function setLabelKey($strKey) {
        $this->label_key = $strKey;
    }

    public function getLabel() {
        return $this->label_text;
    }

    public function setLabel($strLabel) {
        $this->label_text = $strLabel;
    }

    public function getCheckedBoolean() {
        if ($this->label_checked > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getChecked() {
        return $this->label_checked;
    }

    public function setChecked($bChecked) {
        $this->label_checked = $bChecked;
        if ($bChecked || $bChecked==1) {
            $this->label_checked = 1;
        } else {
            $this->label_checked = 0;
        }
    }

    public function getLanguage() {
        return $this->language;
    }

    public function setLanguage($lngObj) {
        $this->language = $lngObj;
    }

    public function setLanguageByLanguageID($lngId) {
        $lngObj = new Language();
        $lngObj->loadLanguageByID($lngId);
        $this->setLanguage($lngObj);
    }

    public static function getColumns() {
        return Label::COL_LABEL_ID . ", " . Label::COL_LABEL_KEY . ", " . Label::COL_LABEL_TEXT . ", " .
                Label::COL_LABEL_CHECKED . ", " . Label::COL_LABEL_LANGUAGE_ID;
    }

    public static function getColumnsNoID() {
        $strRet = Label::COL_LABEL_KEY . ", " . Label::COL_LABEL_TEXT;
        $strRet .= ", " . Label::COL_LABEL_CHECKED . ", ";
        $strRet .=Label::COL_LABEL_LANGUAGE_ID;
        return $strRet;
    }
    
      public static function getArrayColumns() {
        return array(Label::COL_LABEL_ID,
            Label::COL_LABEL_KEY,
            Label::COL_LABEL_TEXT,
            Label::COL_LABEL_CHECKED,
            Label::COL_LABEL_LANGUAGE_ID);
    }

    /**
     * ***************************************************************************
     * Property Declarations
     * ***************************************************************************
     */
    public $DT_RowId;
    public $label_id;
    public $label_key;
    public $label_text;
    public $label_checked;
    public $language;

    /**
     * ***************************************************************************
     * Constants Declarations
     * ***************************************************************************
     */

    const TABLE_NAME = "sm_label";
    const COL_LABEL_ID = "label_id";
    const COL_LABEL_KEY = "label_key";
    const COL_LABEL_TEXT = "label_text";
    const COL_LABEL_CHECKED = "label_checked";
    const COL_LABEL_LANGUAGE_ID = "language_id";

}

function ml($key, $lngAbbr) {
    $MN = "ml:Label ml()";
    $ST = logBegin($MN);

    logDebug($MN, "key = " . $key);
    logDebug($MN, "lngAbbr = " . $lngAbbr);
    $retStr = $key;
    if (!$key == null && !$key == '') {
        if ($lngAbbr == null && $lngAbbr == '') {
            $lng = Language::getDefaultLanguage();
            $lngAbbr = $lng->getLngAbbr();
        }
        $exist = Label::checkExisting($key, $lngAbbr);
        logDebug($MN, "exist=" . $exist);
        if ($exist) {
            $label = Label::loadByKeyLanguage($key, $lngAbbr);
        } else {
            logDebug($MN, "call createLabels");
            Label::createLabels($key);
            $label = Label::loadByKeyLanguage($key, $lngAbbr);
        }
        logDebug($MN, "is_object(label)=" . is_object($label));


        $retStr = $label->getLabel();
        logDebug($MN, "Translate  '" . $key . "' to " . $retStr . " lng '" . $lngAbbr . "'");
    }

    logEndST($MN, $ST);
    return $retStr;
}



/**
 * Use this function, rather than utf8_encode() alone, 
 * for fixing the encoding of unknown data
 */
function FixEncoding($in_str) {
    $MN = "ml:Label FixEncoding()";
    $ST = logBegin($MN);
    logDebug($MN, "in_str = " . $in_str);
    logDebug($MN, "mb_detect_encoding = " . mb_detect_encoding($in_str));
    logDebug($MN, "mb_check_encoding UTF-8 = " . mb_check_encoding($in_str, "UTF-8"));
    $retValue = $in_str;
    /* if(!mb_check_encoding($in_str,"UTF-8"))
      {
      $retValue = utf8_encode($in_str);
      }
      if(!mb_detect_encoding($in_str)=='UTF-8' && !mb_check_encoding($in_str,"UTF-8"))
      {
      $retValue = utf8_encode($in_str);
      } */
    logDebug($MN, "retValue = " . $retValue);
    logEndST($MN, $ST);
    return $retValue;
}

/**
 * ******************************************************************************
 *                        Iordan Iordanov 2009
 * ******************************************************************************
 * */
?>
