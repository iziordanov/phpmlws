<?php
/******************************* HEAD_BEG **************************************
 *
 * Project                	: sm
 * Module 			: lib.common
 * Responsible for module 	: IordIord
 *
 * Filename               	: Connect.php
 *
 * Database System        	: ORCL, MySQL
 * Created from			: IordIord
 * Date Creation		: 15.12.2016
 * -----------------------------------------------------------------------------
 *                        Description
 * -----------------------------------------------------------------------------
 * @TODO Insert some description.
 * 	 
 * -----------------------------------------------------------------------------
 *                        History
 * -----------------------------------------------------------------------------
 * HISTORY:
 * <br>--- $Log: Connect.php,v $
 * <br>---
 * <br>--- 
 *
 ********************************* HEAD_END ************************************
 */

date_default_timezone_set('Europe/Helsinki');
//mb_internal_encoding("UTF-8"); 
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
//This is a server using Windows
    $delim = ";";
    $slash = "\\";
} else {
//This is a server not using Windows!
    $delim = ":";
    $slash = "/";
}

define('APP_HOME', dirname(dirname(dirname(dirname(__FILE__)))));
define('SLASH', $slash);
$ams_home = str_replace('sm/web', 'ams', APP_HOME);
define('AMS_HOME', $ams_home);
//echo '\nAMS_HOME:'.AMS_HOME.' \n';


ini_set('include_path', ini_get('include_path') . $delim . APP_HOME . '/cfg/' .
        $delim . AMS_HOME . '/lib/' . $delim . AMS_HOME . '/lib/log4php/' .
        $delim . AMS_HOME . '/lib/log4php/php/' . $delim . APP_HOME . '/lib/etzaro/' .
        $delim . APP_HOME . '/lib/etzaro/common/' . $delim . APP_HOME . '/lib/etzaro/ml' .
        $delim . APP_HOME . '/lib/etzaro/user' .
        $delim . APP_HOME . '/lib/etzaro/ams' . $delim . APP_HOME . '/lib/etzaro/vlb/' .
        $delim . APP_HOME . '/lib/etzaro/reports/' . $delim . APP_HOME . '/lib/etzaro/email/' . $delim . ' ');

//echo '\n'.ini_get('include_path').' \n';    
//display_errors = On
ini_set("display_errors", "1");
ob_start();

header('Cache-control: private');
header("Content-Type: text/html; charset=utf-8");
session_start();
require_once("connect.inc.php");
require_once("Log.class.php");
require_once("Functions.php");

class Connect {

    public $connection = null;

    //establish db connection
    public function __construct() {
        $MN = "common:Connect::__construct()";
        //$ST = logBegin($MN);
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        //$this->connection->query("SET lower_case_table_names 1");
        //$this->connection->set_charset("usf8");
        $this->connection->query("SET NAMES 'utf8' COLLATE 'utf8_unicode_ci'");


        // Will not affect $mysqli->real_escape_string();
        $this->connection->query("SET CHARACTER SET utf8");

        // But, this will affect $mysqli->real_escape_string();
        $this->connection->set_charset('utf8');

        $charset = $this->connection->character_set_name();
        //logDebug($MN, "db charset=".$charset);
        if (mysqli_connect_errno()) {
            logDebug("$MN", "Database connect Error : "
                    . mysqli_connect_error($this->connection));
            
            header('Location: /dberror.html');
            //die();
            //header("Location: ".$url);
            ob_flush();
        }
        //logEndST($MN, $ST);
    }

    /**
     * ***************************************************************************
     * Methods Declarations
     * ***************************************************************************
     */

    /**
     * store mysqli object
     * 
     * @return <type> 
     */
    public function dbConnect() {
        //connect to the database
//        $link = db_connect(DB_HOST,DB_USER,DB_PASS) or die (db_error());
//		/*mysql_query ('SET CHARACTER SET UTF-8');
//		mysql_query ('SET NAMES UTF-8');
//		mysql_query ('SET  CHARSET UTF-8');*/
//
//        db_select_db(DB_NAME) or die (db_error());
//        return $link;
        $conn = new Connect();
        return $conn->connection;
    }

    public static function dbExecuteSQL($strSQL) {

        $MN = "common:Connect::dbExecuteSQL()";
        //$ST = logBegin($MN);

        $retArray = array();
        $idx = 0;
        $conn = new Connect();
        //$strSQL = strtolower($strSQL);
        logDebug("$MN", "SQL=" . $strSQL);

        $result = $conn->connection->query($strSQL);

        if ($result) {
            while ($row = $result->fetch_row()) {
                $retArray[$idx] = $row;
                $idx++;
                //logDebug($MN, "row[" . $idx . "] =" . prArr($row));
            }
            $result->close();
        }

        //logEndST($MN, $ST);
        return $retArray;
    }

    public static function dbSelectKeyValueSQL($strSQL) {

        $MN = "common:Connect::dbSelectKeyValueSQL()";
        //$ST = logBegin($MN);

        $retArray = array();
        $idx = 0;
        $conn = new Connect();
        //$strSQL = strtolower($strSQL);
        logDebug("$MN", "SQL=" . $strSQL);
        $result = $conn->connection->query($strSQL);

        if ($result) {
            while ($row = $result->fetch_row()) {
                $retArray[$row[0]] = $row[1];
                //logDebug($MN, "retArray[".$row[0]."] =".$retArray[$row[0]]);
            }
            $result->close();
        }

        //logEndST($MN, $ST);
        return $retArray;
    }

    public static function dbSelectValueSQL($strSQL) {

        $MN = "common:Connect::dbSelectValueSQL()";
        $ST = logBegin($MN);

        $retArray = array();
        $idx = 0;
        $id=-1;
        $conn = new Connect();
        logDebug("$MN", "SQL=" . $strSQL);
        //$qry = db_query($strSQL) or die (db_error());
        $result = $conn->connection->query($strSQL);

        if ($result) {
            while ($row = $result->fetch_row()) {
                $retArray[$idx++] = $row;
                $id = $row[0];
                //logDebug($MN, "row[" . $idx . "] =" . prArr($row));
                break;
            }
            $result->close();
        }


        logDebug("$MN", "ret Value=" . $id);
        logEndST($MN, $ST);
        return $id;
    }

    public static function dbInsertSQL($strSQL) {
        $MN = "common:Connect.dbInsertSQL()";
        $ST = logBegin($MN);

        logDebug($MN, "Before connect SQL=" . $strSQL);
        $conn = new Connect();
        //$conn->dbConnect();
        //$strSQL = strtolower($strSQL);
        //logDebug($MN, "SQL=" . $strSQL);
        //$q = db_query($strSQL) or die (db_error());
        $conn->connection->query($strSQL);
        //$lngID = db_insert_id();
        $lngID = $conn->connection->insert_id;
        //logDebug($MN, "q=".$q);

        //logDebug($MN, "lngId=" . $lngID);
        logEndST($MN, $ST);
        return $lngID;
    }

    public static function dbDeleteSQL($strSQL) {
        $MN = "common:Connect.dbDeleteSQL()";
        $ST = logBegin($MN);

        $conn = new Connect();
        //$conn->dbConnect();
        //$strSQL = strtolower($strSQL);
        logDebug($MN, "SQL=" . $strSQL);
        //$q = db_query($strSQL) or die (db_error());
        $conn->connection->query($strSQL);
        //$lngID = db_insert_id();
        $rows = $conn->connection->affected_rows;
        //logDebug($MN, "affected rows=" . $rows);

        logEndST($MN, $ST);
        return $rows;
    }

    /**
     *  run a prepared query
     * 
     * @param <type> $query
     * @param <type> $params_r
     * @return <type> 
     */
    public function runPreparedQuery($query, $bind_params_r) {
        $MN = "common:Connect.runPreparedQuery()";
        $ST = logBegin($MN);

        logDebug($MN, "SQL=" . $query);
        //logDebug($MN, "get_server_info=".$this->connection->get_server_info());
        if(!$stmt = $this->connection->prepare($query))
        {
            echo $query;
            echo"<br/>";
            echo "Prepare failed: (" . $this->connection->errno . ") " . $this->connection->error;
            return 0;
        }
        //logDebug($MN, "stmt=".$stmt);
        
        $this->bindParameters($stmt, $bind_params_r);

        //logDebug($MN, "after bindParameters");
        if ($stmt->execute()) 
            {
            logEndST($MN, $ST);
            return $stmt;
        } 
        else {
            logError($MN, mysqli_error($this->connection));
            logDebug("$MN", "Error in query: " . $query);
            logDebug("$MN", "Error: " . mysqli_error($this->connection));
            echo "Error: " . mysqli_error($this->connection);
            logEndST($MN, $ST);
            return 0;
        }
    }

    /**
     *  To run a select statement with bound parameters and bound results.
     * Returns an associative array two dimensional array which u can easily
     * manipulate with array functions.
     */
    public function preparedSelect($query, $bind_params_r) {
        $MN = "common:Connect.preparedSelect()";
        $ST = logBegin($MN);

        $select = $this->runPreparedQuery($query, $bind_params_r);
        $fields_r = $this->fetchFields($select);
       
        foreach ($fields_r as $field) {
            $bind_result_r[] = &${$field};
        }

        $this->bindResult($select, $bind_result_r);

        $result_r = array();
        $i = 0;
        while ($select->fetch()) {

            foreach ($fields_r as $field) {

                $result_r[$i][$field] = DbConvEncoding($$field);
                /*
                  if(mb_detect_encoding($$field) === "UTF-8") {
                  $result_r[$i][$field] = $$field;
                  }
                  else{
                  $result_r[$i][$field] =  utf8_encode( $$field);
                  } */
            }
            $i++;
        }
        $select->close();
        //logDebug($MN, "result_r=".prArr($result_r));
        //logEndST($MN, $ST);
        return $result_r;
    }

    public static function dbExecuteSQLJson($strSQL) {

        $MN = "common:Connect::dbExecuteSQL()";
        $ST = logBegin($MN);

        $retArray = array();
        $idx = 0;
        $conn = new Connect();
        //$strSQL = strtolower($strSQL);
        logDebug("$MN", "SQL=" . $strSQL);

        $result = $conn->connection->query($strSQL);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $retArray[$idx] = $row;
                $idx++;
                //logDebug($MN, "row[" . $idx . "] =" . prArr($row));
            }
            $result->close();
        }

        logEndST($MN, $ST);
        return $retArray;
    }
    
    public function SelectJson($query, $bind_params_r) {
        $MN = "common:Connect.SelectJson()";
        $ST = logBegin($MN);
        
        
        $statement = $this->runPreparedQuery($query, $bind_params_r);
        $statement->execute();
        $statement->store_result();
        $result_r = $this->fetch($statement);
//        $result_r = array();
//        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
//            $result_r[] = $row;
//        }
//        while($row = $result -> fetch_assoc()) {
//            $result_r[]= $row;    
//        }
        $i = 0;
//        while($assoc_array = $this->fetchAssocStatement($statement))
//        {
//            // logDebug($MN, "i=".i);
//            //logDebug($MN, "assoc_array=".prArr($assoc_array));
//            $result_r[$i] = $assoc_array;
//            //array_push($arr, $assoc_array);
//            $i++;
//        }
        $statement->close();
        //logDebug($MN, "result_r=".prArr($result_r));
        logEndST($MN, $ST);
        return $result_r;
    }
    
    public function fetch($result)
    {    
        $array = array();

        if($result instanceof mysqli_stmt)
        {
            $result->store_result();

            $variables = array();
            $data = array();
            $meta = $result->result_metadata();

            while($field = $meta->fetch_field())
                $variables[] = &$data[$field->name]; // pass by reference

            call_user_func_array(array($result, 'bind_result'), $variables);

            $i=0;
            while($result->fetch())
            {
                $array[$i] = array();
                foreach($data as $k=>$v)
                    $array[$i][$k] = $v;
                $i++;

                // don't know why, but when I tried $array[] = $data, I got the same one result in all rows
            }
        }
        elseif($result instanceof mysqli_result)
        {
            while($row = $result->fetch_assoc())
                $array[] = $row;
        }

        return $array;
    }

    public function fetchAssocStatement($statement) {
        $MN = "common:Connect.fetchAssocStatement()";
        $ST = logBegin($MN);
         $result = array();
         logDebug($MN, "num_rows=".$statement->num_rows);
        if($statement->num_rows>0)
        {
            $md = $statement->result_metadata();
            $params = array();
            while($field = $md->fetch_field()) {
                //logDebug($MN, "fetch_field=".$field->name);
                $params[] = &$result[$field->name];
            }
            call_user_func_array(array($statement, 'bind_result'), $params);
            if ($statement->fetch()) {
                //return $result;
            }
        }
        //logDebug($MN, "result=".prArr($result));
        logEndST($MN, $ST);
        return $result;
    }
    
    /**
     *  To run a select statement with bound parameters and bound results.
     * Returns an associative array two dimensional array which u can easily
     * manipulate with array functions.
     */
    public function preparedInsert($query, $bind_params_r) {
        $MN = "common:Connect.preparedInsert()";
        //$ST = logBegin($MN);

        $select = $this->runPreparedQuery($query, $bind_params_r);

        $id = $this->connection->insert_id;

        $select->close();
        //logDebug($MN, "Insert id=".$id);
        //logEndST($MN, $ST);
        return $id;
    }

    public function preparedDelete($query, $bind_params_r) {
        $MN = "common:Connect.preparedDelete()";
        //$ST = logBegin($MN);
        $rowsAffected = -1;
        $select = $this->runPreparedQuery($query, $bind_params_r);
        $rowsAffected = $this->connection->affected_rows;
        $select->close();
        //logDebug($MN, "rowsAffected=".$rowsAffected);
        //logEndST($MN, $ST);
        return $rowsAffected;
    }

    public function preparedUpdate($query, $bind_params_r) {
        $MN = "common:Connect.preparedUpdate()";
        //$ST = logBegin($MN);
        $rowsAffected = -1;
        $select = $this->runPreparedQuery($query, $bind_params_r);
        $rowsAffected = $this->connection->affected_rows;
        $select->close();
        //logDebug($MN, "rowsAffected=".$rowsAffected);
        //logEndST($MN, $ST);
        return $rowsAffected;
    }

    /**
     * takes in array of bind parameters and binds them to result of
     * executed prepared stmt
     *
     * @param <type> $obj
     * @param <type> $bind_params_r
     */
    private function bindParameters(&$stmt, &$bind_params_r) {
        $MN = "common:Connect.bindParameters()";
        $ST = logBegin($MN);
        $tmp = array();
        foreach ($bind_params_r as $key => $value) {
            $tmp[$key] = &$bind_params_r[$key];
            //logDebug($MN, "key=" . $key . " value=" . $value);
        }
        
        if (!call_user_func_array([$stmt, "bind_param"], $tmp))
        {
            $msgStr = "Param Bind failed, [" . implode(",", $bind_params_r) . "]:" . $bind_params_r[0] . " (" . $stmt->errno . ") " . $stmt->error;
            logDebug($MN, $msgStr);
            echo $msgStr;
        }
        
        			
			
        logEndST($MN, $ST);
    }

    /**
     *
     * @param <type> $obj
     * @param <type> $bind_result_r
     */
    private function bindResult(&$obj, &$bind_result_r) {
        $MN = "common:Connect.bindResult()";
        //$ST = logBegin($MN);
        call_user_func_array(array($obj, "bind_result"), $bind_result_r);
        //logEndST($MN, $ST);
    }

    /**
     * returns a list of the selected field names
     * @param <type> $selectStmt
     * @return <type>
     */
    private function fetchFields($selectStmt) {
        $MN = "common:Connect.fetchFields()";
        //$ST = logBegin($MN);
        $metadata = $selectStmt->result_metadata();
        $fields_r = array();
        while ($field = $metadata->fetch_field()) {
            $fields_r[] = $field->name;
        }
        //logEndST($MN, $ST);
        return $fields_r;
    }
    
    private function fetchMysqliResult($stmt) {
        $MN = "common:Connect.fetchMysqliResult()";
        $ST = logBegin($MN);
        $stmt->execute();
        $result  = mysqli_stmt_get_result($stmt);
        logEndST($MN, $ST);
        return $result;
    }

    /**
     * ***************************************************************************
     * Getters and Setters Declarations
     * ***************************************************************************
     */
    public static function getQueryCharSet() {
        $MN = "common:Connect.getQueryCharSet()";
        //$ST = logBegin($MN);

        $i = 0;
        //$db = Connect::dbConnect();
        $conn = new Connect();

        //$charset = mysql_client_encoding();
        $charset = $conn->connection->character_set_name();
        //logDebug($MN, "charset=".$charset);
        //logEnd($MN, $ST);
        return $charset;
    }

    /**
     * ***************************************************************************
     * Property Declarations
     * ***************************************************************************
     */
    /**
     * ***************************************************************************
     * Constants Declarations
     * ***************************************************************************
     */
}

function DbConvEncoding($in_str) {
    $MN = "common:Connect.DbConvEncoding()";
    //$ST = logBegin($MN);

    $retValue = $in_str;
    //logDebug($MN, "DB_CONV_REV = " . DB_CONV_REV);
    if (DB_CONV_REV) {
        $dbcharset = Connect::getQueryCharSet();
        //logDebug($MN, "dbcharset = " . $dbcharset);
        //logDebug($MN, "in_str = " . $in_str);
        //logDebug($MN, "mb_detect_encoding(in_str) = " . mb_detect_encoding($in_str));
        if (mb_detect_encoding($in_str) == $dbcharset) {
            $retValue = $in_str;
        } else if ($dbcharset == 'latin1') {
            //logDebug($MN, "latin1 case executed!");
            $retStr = iconv(mb_detect_encoding($in_str), "CP1251", $in_str);
            //logDebug($MN, "retStr = " . $retStr);
            $retValue = $retStr;
        } else {
            //logDebug($MN, "iconv " . mb_detect_encoding($in_str) . " to " . $dbcharset);
            $retValue = iconv(mb_detect_encoding($in_str), $dbcharset, $in_str);
        }
    }
    //logDebug($MN, "retValue = " . $retValue);
    return $retValue;
}

function DbRevEncoding($in_str) {
    $MN = "common:Connect.DbRevEncoding()";
    //$ST = logBegin($MN);

    $retValue = $in_str;
    $retValue = base64_encode(utf8_encode($in_str));
    /*
      logDebug($MN, "DB_CONV_REV = " . DB_CONV_REV);
      if (DB_CONV_REV) {
      $dbcharset = Connect::getQueryCharSet();
      logDebug($MN, "dbcharset = " . $dbcharset);
      logDebug($MN, "in_str = " . $in_str);
      logDebug($MN, "mb_detect_encoding(in_str) = " . mb_detect_encoding($in_str));
      if (mb_detect_encoding($in_str) == $dbcharset) {
      $retValue = $in_str;
      } else if ($dbcharset == 'latin1') {
      logDebug($MN, "latin1 case executed!");
      $retStr = iconv("CP1251", mb_detect_encoding($in_str), $in_str);
      logDebug($MN, "retStr = " . $retStr);
      return $retStr;
      } else {
      logDebug($MN, "iconv " . mb_detect_encoding($in_str) . " to " . $dbcharset);
      return iconv($dbcharset, mb_detect_encoding($in_str), $in_str);
      }
      }

     */
    //logDebug($MN, "retValue = " . $retValue);
    return $retValue;
}

//Connect::dbConnect();
/**
 * ******************************************************************************
 *                        Iordan Iordanov 2009
 * ******************************************************************************
 * */
?>
