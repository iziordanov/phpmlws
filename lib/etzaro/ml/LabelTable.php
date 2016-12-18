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

    /* Array of database columns which should be read and sent back to DataTables. Use a space where
     * you want to insert a non-database field (for example a counter or static image)
     */
     require_once("../common/Connect.php");
     require_once("../common/Functions.php");

     require_once("../user/User.class.php");
     require_once("Language.php");
     require_once("Label.php");
     require_once("House.php");

    
     $MN = "LabelTable.php";
     $aColumns =  Label::getArrayColumns();


    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = Label::COL_LABEL_ID; 
    logDebug($MN, "start current_user_id=".$_SESSION["current_user_id"]);
    logDebug($MN, "DB_USER=".DB_USER);
    /* DB table to use */
    $sTable = Label::TABLE_NAME;

    /* Database connection information DB_HOST,DB_USER,DB_PASS, DB_NAME*/
    $gaSql['user']       = DB_USER;
    $gaSql['password']   = DB_PASS;
    $gaSql['db']         = DB_NAME;
    $gaSql['server']     = DB_HOST;

   
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * If you just want to use the basic configuration for DataTables with PHP server-side, there is
     * no need to edit below this line
     */

    /*
     * Local functions
     */
    function fatal_error ( $sErrorMessage = '' )
    {
        header( $_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error' );
        die( $sErrorMessage );
    }


    /*
     * MySQL connection
     */
    if ( ! $gaSql['link'] = mysql_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) )
    {
        fatal_error( 'Could not open connection to server' );
    }
    mysql_set_charset( 'utf8', $gaSql['link']);
    mysql_query("SET character_set_results = 'utf8', 
        character_set_client = 'utf8', character_set_connection = 'utf8', 
        character_set_database = 'utf8', character_set_server = 'utf8'", $gaSql['link']);
    if ( ! mysql_select_db( $gaSql['db'], $gaSql['link'] ) )
    {
        fatal_error( 'Could not select database ' );
    }


    /*
     * Paging
     */
    $sLimit = "";
    if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
    {
        $sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
            mysql_real_escape_string( $_GET['iDisplayLength'] );
    }

    /**
     * Parameters
     */
$lngAbbr = $_SESSION["current_language"];	
logDebug($MN, "current_language=".$lngAbbr);
$language = new Language();
$language->getLanguageByAbbreviation($lngAbbr);
    //--- HOUSE ID
$current_user_id = null;
global $current_user;
global $current_house;
global $current_revision;

if (isset($_SESSION["current_user_id"]) && $_SESSION["current_user_id"] > -1) {
    $current_user_id = $_SESSION["current_user_id"];
    logDebug($MN, "current_user_id=" . $current_user_id);

    if (!isset($current_user)) {
        $current_user = new User();
        $current_user->loadById($_SESSION["current_user_id"]);
    }
    logDebug($MN, "current_user=" . $current_user->toString());
}

if(!isset($current_user) || !$current_user->isAdmin())
{
    logDebug($MN, "isset(current_user)=".(isset($current_user)?"true":"false"));
    logDebug($MN, "isAdmin=".(!$current_user->isAdmin()?"true":"false"));
    header("Location: ../index.php?contentPage=error&error_msg=ERROR_INCORECT_ACCESS");
    ob_flush();
}

    /*
     * Ordering
     */
    $sOrder = "";
    if ( isset( $_GET['iSortCol_0'] ) )
    {
        $sOrder = "ORDER BY  ";
        for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
        {
            if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
            {
                $sOrder .= "`".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."` ".
                    mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
            }
        }

        $sOrder = substr_replace( $sOrder, "", -2 );
        if ( $sOrder == "ORDER BY" )
        {
            $sOrder = "";
        }
    }


    /*
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    $sWhere = "";
    
    if(isset($language))
    {
        //$sWhere = " WHERE (". Person::COL_NAME_HOUSE_ID."=".$current_house->getId().") ";
        $sWhere .= " WHERE (". Label::COL_LABEL_LANGUAGE_ID ."=".$language->getId().") ";
    } 
    
    
    if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
    {
        if($sWhere == "")
            $sWhere = "WHERE (";
        else {
            $sWhere.=" AND (";
        }
        
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            $sWhere .= "`".$aColumns[$i]."` LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
        }
        $sWhere = substr_replace( $sWhere, "", -3 );
        $sWhere .= ')';
    }

    /* Individual column filtering */
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
        {
            if ( $sWhere == "" )
            {
                $sWhere = "WHERE ";
            }
            else
            {
                $sWhere .= " AND ";
            }
            $sWhere .= "`".$aColumns[$i]."` LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
        }
    }


    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "
        SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $aColumns))."`
        FROM   $sTable
        $sWhere
        $sOrder
        $sLimit
        ";
    logDebug($MN, "sQuery=".$sQuery);
    $rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );

    /* Data set length after filtering */
    $sQuery = "
        SELECT FOUND_ROWS()
    ";
     logDebug($MN, "sQuery2=".$sQuery);
    $rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];

    /* Total data set length */
    $sQuery = "
        SELECT COUNT(`".$sIndexColumn."`)
        FROM   $sTable
    ";
    logDebug($MN, "sQuery3=".$sQuery);
    $rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultTotal = mysql_fetch_array($rResultTotal);
    $iTotal = $aResultTotal[0];


    /*
     * Output
     */
    $output = array(
        "sEcho" => intval($_GET['sEcho']),
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );
    logDebug($MN, "rez array size=".  sizeof($rResult));
    while ( $aRow = mysql_fetch_array( $rResult ) )
    {
        $item = new Label();
        $item->loadFromArray($aRow);
        $retArray[] = $item;
        //logDebug($MN, "Add Item =" . $item->toJSON());
        $output['aaData'][] = $item;
        
       
    }
    $retValue = json_encode( $output );
    logDebug($MN, "retValue=".$retValue);
    echo json_encode( $output );
    
    
?>
