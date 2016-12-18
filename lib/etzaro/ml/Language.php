<?php

/* * ****************************** HEAD_BEG ***********************************
 *
 * Project                	: phpmlws
 * Module 			: ml
 * Responsible for module 	: i_z_iordanov@yahoo.com
 *
 * Filename               	: Language.php
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
 * <br>--- $Log: Language.php,v $
 * <br>---
 * <br>--- 
 *
 * ******************************** HEAD_END ***********************************
 */

class Language {

    /**
     * *************************************************************************
     * Methods Declarations
     * *************************************************************************
     */
    function setFromArray($aLangArray) {
        if (!$aLangArray == null) {
            $this->setId($aLangArray[0]);
            $this->setLngName($aLangArray[1]);
            $this->setLngAbbr($aLangArray[2]);
            $this->setLngDedault($aLangArray[3]);
        }
    }

    /**
     * Retreeve Language instance by abbreviation string
     * 
     * @param string Language Abreviation (BG, EN, ...) 
     */
    public function getLanguageByAbbreviation($lngAbbr) {
        $MN = "ml:Language.getLanguageByAbbreviation()";
        $ST = logBegin($MN);
        //logDebug($MN, "lngAbbr = ".$lngAbbr);

        if ($lngAbbr == null && $lngAbbr == '')
            $lngAbbr = "bg";
        $strSQL = "SELECT * FROM " . Language::TABLE_NAME;
        $strSQL .= " WHERE " . Language::COL_LNG_ABBR_NAME . " = '" . $lngAbbr . "' ";

        //$language = new Language();
        $aLangArray = Connect::dbExecuteSQL($strSQL);

        $this->setFromArray($aLangArray[0]);

        //logDebug($MN, "Language = ".$this->getLngName());
        logEnd($MN, $ST);
        return $this;
    }

    /**
     * Retreeve Language instance by abbreviation string
     * 
     * @param string Language Abreviation (BG, EN, ...) 
     */
    public function loadLanguageByID($lngId) {
        $MN = "ml:Language.getLanguageByAbbreviation()";
        $ST = logBegin($MN);
        //logDebug($MN, "lngId = ".$lngId);

        if ($lngId == null && $lngId == '')
            $lngId = "1";
        $strSQL = "SELECT * FROM " . Language::TABLE_NAME;
        $strSQL .= " WHERE " . Language::COL_LNG_ID_NAME . " = '" . $lngId . "' ";

        //$language = new Language();
        $aLangArray = Connect::dbExecuteSQL($strSQL);

        $this->setFromArray($aLangArray[0]);

        //logDebug($MN, "Language = ".$this->getLngName());
        logEnd($MN, $ST);
        return $this;
    }

    /**
     * Function for retreeve all stored in DB Languager
     * 
     * returns: Array<Language> 
     */
    public static function getAll() {
        $MN = "ml:Language.getAll()";
        $ST = logBegin($MN);

        $retArray = array();
        $strSQL = "SELECT * FROM " . Language::TABLE_NAME;
        $strSQL .= " ORDER BY  " . Language::COL_LNG_ID_NAME;

        //$language = new Language();
        $aLangArray = Connect::dbExecuteSQL($strSQL);
        //logDebug($MN, "sizeof( aLangArray ) = ".sizeof( $aLangArray ));
        for ($index = 0, $max_count = sizeof($aLangArray); $index < $max_count; $index++) {
            $row = $aLangArray[$index];
            $lhgObj = new Language();
            $lhgObj->setFromArray($row);
            $retArray[] = $lhgObj;
        }

        //logDebug($MN, "Returns sizeof( retArray ) = ".sizeof( $retArray ));
        logEnd($MN, $ST);
        return $retArray;
    }

    /**
     * 
     */
    public static function getDefaultLanguage() {
        $MN = "ml:Language.getDefaultLanguage()";
        $ST = logBegin($MN);

        $lng = new Language();
        $strSQL = "SELECT * FROM " . Language::TABLE_NAME;
        $strSQL .= " WHERE " . Language::COL_LNG_DEFAULT_NAME . " = '1' ";


        $aLangArray = Connect::dbExecuteSQL($strSQL);

        $lng->setFromArray($aLangArray[0]);

        logDebug($MN, "Language = " . $lng->getLngName());
        logEnd($MN, $ST);
        return $lng;
    }

    public function toJSON() {
        return json_encode($this);
    }

    /**
     * *************************************************************************
     * Getters and Setters Declarations
     * *************************************************************************
     */
    public function getId() {
        return $this->lng_id;
    }

    public function setId($intId) {
        $this->lng_id = $intId;
    }

    public function getLngName() {
        return $this->lng_name;
    }

    public function setLngName($strName) {
        $this->lng_name = $strName;
    }

    public function getLngAbbr() {
        return $this->lng_abbr;
    }

    public function setLngAbbr($strAbbr) {
        $this->lng_abbr = $strAbbr;
    }

    public function getLngDedault() {
        if ($this->lng_default > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function setLngDedault($bDefault) {
        if ($bDefault) {
            $this->lng_default = 1;
        } else {
            $this->lng_default = 0;
        }
    }

    /**
     * *************************************************************************
     * Parameters Declarations
     * *************************************************************************
     */
    public $lng_id;
    public $lng_name;
    public $lng_abbr;
    public $lng_default;

    /**
     * *************************************************************************
     * Constants Declarations
     * *************************************************************************
     */
    const TABLE_NAME = "sm_language";
    const COL_LNG_ID_NAME = "language_id";
    const COL_LNG_NAME_NAME = "language_key";
    const COL_LNG_ABBR_NAME = "language_abbr";
    const COL_LNG_DEFAULT_NAME = "language_default";

}

/**
 * ******************************************************************************
 *                        Iordan Iordanov 2009
 * ******************************************************************************
 * */
?>
