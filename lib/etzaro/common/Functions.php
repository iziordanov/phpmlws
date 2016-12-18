<?php 
define('DATE_TIME_FORMAT', 'd. M Y H:i');
define('DATE_FORMAT', 'd. M Y');
define('GOOGLE_API_KEY', 'AIzaSyBI7rPC1cRKJ_h8VY1AfNVcejZx7gRFmhU');
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function validate_search_string($s) //validates $s - is it good as a search query
{
//exclude special SQL symbols
    $s = str_replace("%","",$s);
    //$s = str_replace("_","",$s);
    //",',\
    $s = stripslashes($s);
    $s = str_replace("'","\'",$s);
    return $s;

} //validate_search_string

function string_encode($s) // encodes a string with a simple algorythm
{
    $result = base64_encode($s);
    return $result;
}

function string_decode($s) // decodes a string encoded with string_encode()
{
    $result = base64_decode($s);
    return $result;
}

function checkEmail($email)
{
    $mn = "checkEmail()";
    $st = logBegin($mn);
    logDebug($mn, "email=".$email);
    // Used as callback or validate="email" shortcut

    $retValue = preg_match(
            '#^([a-zA-Z0-9_\\-\\.]+)@((\\[[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.)|(([a-zA-Z0-9\\-]+\\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\\]?)$#',
            $email
        );
   
    logDebug($mn, "retValue=".$retValue);
    logEndST($mn, $st);
    return $retValue;
}

function prArr($tmpArray) {
    $mn = "prArr()";
     $st= logBegin($mn);

    foreach ($tmpArray as $number_variable => $variable) {

        if(!is_array($variable) &&!is_array($number_variable)){
        logDebug($mn, "[".$number_variable."] ".$variable);
        }

    }
    logEndST($mn, $st);
}


 function getDateStr($objOrStr)
{
    $mn = "common:Functions.getDateStr()";
    //$st = logBegin($mn);
    $dt = new DateTime();
    $retStr = $dt->format(DATE_FORMAT);
    //$retStr = LBL_CREATE;
    $dt = $objOrStr;
    //logDebug($mn, "!isset(objOrStr)".!isset($objOrStr));
    if(!is_object($objOrStr))
    {
        if(isset($objOrStr) && (!$objOrStr==""))
        {
           try
            {
                $dt = new DateTime($objOrStr);
                $retStr = $dt->format(DATE_FORMAT);
            }
            catch(Exception $ex)
            {
                $retStr="";
            }
        }
    }
    else
    {
         $retStr = $objOrStr->format(DATE_FORMAT);
    }

    //logEndST($mn, $st);
    return $retStr;
}

 function getDateTimeStr($objOrStr)
{
    $mn = "common:Functions.getDateTimeStr()";
    //$st = logBegin($mn);

    $retStr = "";
    $dt = $objOrStr;
    //logDebug($mn, "!isset(objOrStr)".!isset($objOrStr));
    if(!is_object($objOrStr))
    {
        if(isset($objOrStr) && (!$objOrStr==""))
        {
            $dt = new DateTime($objOrStr);
            $retStr = $dt->format(DATE_TIME_FORMAT);
        }
    }
    else
    {
         $retStr = $objOrStr->format(DATE_TIME_FORMAT);
    }

    //logEndST($mn, $st);
    return $retStr;
}

function DateTimeDbStr($objOrStr)
{
    $mn = "common:Functions.DateTimeDbStr()";
    //$st = logBegin($mn);

    $retStr = null;
    $dt = $objOrStr;
    //logDebug($mn, "!isset(objOrStr)".!isset($objOrStr));
    if(isset($objOrStr) && $objOrStr!=null && $objOrStr!="")
    {
        if(!is_object($objOrStr))
        {
            if(isset($objOrStr) && (!$objOrStr==""))
            {
                $dt = new DateTime($objOrStr);
                $retStr = $dt->format("Y-m-d H:i:s");
            }
        }
        else
        {
             $retStr = $objOrStr->format("Y-m-d H:i:s");
        }   
    }
    //logDebug($mn, "retStr=".$retStr);
    //logEndST($mn, $st);
    return $retStr;
}

function CurrenDateTime()
{
    $mn = "common:Functions.CurrenDateTime()";
    $st = logBegin($mn);
    $retValue = date("Y-m-d H:i:s", time()) ;
     logDebug($mn, "$retValue=".$retValue);
    logEndST($mn, $st);
    return $retValue;
}


function createDir($dirName) {
    $mn = "common:Functions.createDir()";
    $st = logBegin($mn);
    logDebug($mn, "dirName=".$dirName);
    
    $path = APP_HOME.SLASH.$dirName;
    logDebug($mn, "path=".$path);
    logDebug($mn, "is_dir($path)=".@is_dir($path));
    $mode = 0755;
    if (!@is_dir($path)) {
        logDebug($mn, "Create Dir:".$path);
        !@mkdir($path, $mode);
    }
    logEndST($mn, $st);
    return $path;
}

function StrToDateObj($data) {
    $retValue = $data;
    if (isset($data) && $data != null) {
        if (is_object($data))
            $retValue = $data;
        else
            $retValue = new DateTime($data);
    }
    return $retValue;
}

function GetDateObj($data) {
    $retValue = null;
    if(isset($data)&& $data!=null)
    {
        if (!is_object($data))
            $retValue = new DateTime($data);
        else
            $retValue = $data;
    }
    else
    {
        logDebug("GetDateObj", "retValue=NULL");
    }
    return $retValue;
}
//////////////////////////////////////////////////////////////////////
//PARA: Date Should In YYYY-MM-DD Format
//RESULT FORMAT:
// '%y Year %m Month %d Day %h Hours %i Minute %s Seconds'        =>  1 Year 3 Month 14 Day 11 Hours 49 Minute 36 Seconds
// '%y Year %m Month %d Day'                                    =>  1 Year 3 Month 14 Days
// '%m Month %d Day'                                            =>  3 Month 14 Day
// '%d Day %h Hours'                                            =>  14 Day 11 Hours
// '%d Day'                                                        =>  14 Days
// '%h Hours %i Minute %s Seconds'                                =>  11 Hours 49 Minute 36 Seconds
// '%i Minute %s Seconds'                                        =>  49 Minute 36 Seconds
// '%h Hours                                                    =>  11 Hours
// '%a Days                                                        =>  468 Days
//////////////////////////////////////////////////////////////////////

function dateDifferenceInTime($date_1 , $date_2 )
{
    return dateDifference($date_1, $date_2, '%h:%i:%s');
}
function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' )
{
    $datetime1 = date_create($date_1);
    $datetime2 = date_create($date_2);
    
    $interval = date_diff($datetime1, $datetime2);
    
    return $interval->format($differenceFormat);
    
}

function getRequestHeaders($header_name=null)
{
    $keys=array_keys($_SERVER);

    if(is_null($header_name)) {
            $headers=preg_grep("/^HTTP_(.*)/si", $keys);
    } else {
            $header_name_safe=str_replace("-", "_", strtoupper(preg_quote($header_name)));
            $headers=preg_grep("/^HTTP_${header_name_safe}$/si", $keys);
    }

    foreach($headers as $header) {
            if(is_null($header_name)){
                    $headervals[substr($header, 5)]=$_SERVER[$header];
            } else {
                    return $_SERVER[$header];
            }
    }

    return $headervals;
}

/* Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 * @source http://gravatar.com/site/implement/images/php/
 */
function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
    $url = 'http://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$s&d=$d&r=$r";
    if ( $img ) {
        $url = '<img src="' . $url . '"';
        foreach ( $atts as $key => $val )
            $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    return $url;
}

function get_nearest_timezone($cur_lat, $cur_long, $country_code = '') {
    $timezone_ids = ($country_code) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_code)
                                    : DateTimeZone::listIdentifiers();

    if($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

        $time_zone = '';
        $tz_distance = 0;

        //only one identifier?
        if (count($timezone_ids) == 1) {
            $time_zone = $timezone_ids[0];
        } else {

            foreach($timezone_ids as $timezone_id) {
                $timezone = new DateTimeZone($timezone_id);
                $location = $timezone->getLocation();
                $tz_lat   = $location['latitude'];
                $tz_long  = $location['longitude'];

                $theta    = $cur_long - $tz_long;
                $distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat))) 
                + (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
                $distance = acos($distance);
                $distance = abs(rad2deg($distance));
                // echo '<br />'.$timezone_id.' '.$distance; 

                if (!$time_zone || $tz_distance > $distance) {
                    $time_zone   = $timezone_id;
                    $tz_distance = $distance;
                } 

            }
        }
        return new DateTimeZone($time_zone);// $time_zone;
    }
    return null;
}

function MpgToLp100Km($mpg)
{
    //U.S. Gallon = 3.785411784 Liters
    //Mile = 1.609344 Kilometers
    return ((100 * 3.785411784 )/(1.609344 *$mpg));
}

//
function NmToKm($nm)
{
    $nm_to_km = 1.852;
    return ($nm *$nm_to_km);
}

function KnotToKmPH($knot)
{
    $knot_to_kmph = 1.852;
    return ($knot *$knot_to_kmph);
}

function UsgToKg($usg)
{
    $usgToL =  3.785411784;
     $LToKg =  0.785 ;
    return (($usg *$usgToL)*0.785);
}

function AvailableCargo($useful_load_kg, $fuel_tank_usg, $nrPilots, $nrCrew, $nrPassengers, $kg_to_lbs)
{
    $fielKg = UsgToKg($fuel_tank_usg);
    $passengersKg = ($nrPilots + $nrPassengers + $nrCrew)*75;
    $leftForCargo = $useful_load_kg-($fielKg+$passengersKg);
    
    if($leftForCargo<0)
        $leftForCargo=0;
    return $leftForCargo * $kg_to_lbs;
}

function MoneyFormat($amount)
{
    setlocale(LC_MONETARY, 'en_US');
    return money_format('%.2n', $amount);
}

function FlightHoursFormat($amount)
{
    $remainder = $amount % 1;
    $minutes = 60 * $remainder;
    $hours = floor($amount);
    $retValue = number_format($hours,0,'.',' '). ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
    return $retValue;
}


/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
::                                                                             :
::  This routine calculates the distance between two points (given the         :
::  latitude/longitude of those points). It is being used to calculate         :
::  the distance between two locations using GeoDataSource(TM) Products        :
::                                                                             :
::  Definitions:                                                               :
::    South latitudes are negative, east longitudes are positive               :
::                                                                             :
::  Passed to function:                                                        :
::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)      :
::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)      :
::    unit = the unit you desire for results                                   :
::           where: 'M' is statute miles (default)                             :
::                  'K' is kilometers                                          :
::                  'N' is nautical miles                                      :
::  Worldwide cities and other features databases with latitude longitude      :
::    are available at http://www.geodatasource.com                            :
::                                                                             :
::    For enquiries, please contact sales@geodatasource.com                    :
::                                                                             :
::    Official Web site: http://www.geodatasource.com                          :
::                                                                             :
::           GeoDataSource.com (C) All Rights Reserved 2015                    :
::                                                                             :
::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
function distance($lat1, $lon1, $lat2, $lon2, $unit) {

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344);
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }
}

?>
