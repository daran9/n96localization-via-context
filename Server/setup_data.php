<?php
/* Summary : This script gets the required POST variables, decodes them and insert them into the database.
 * Required POST variable : WLANDATA
 * Required POST variable : BLDG
 * Required POST variable : FLOOR
 * Required POST variable : SECTION
 * Return :
 */

//TODO: BLDG, FLOOR and SECTION information should also be transported in JSON format.

//Collect POST data
$WLAN_DATA = $_POST['WLANDATA'];
$BLDG = $_POST['BLDG'];
$FLOOR = $_POST['FLOOR'];
$SECTION = $_POST['SECTION'];

//Decode data in JSON notation to associative array containing BSSID and RSSI values
$WlanArray = json_decode(stripslashes($WLAN_DATA),true);

/*
//Store values to a file
$filename = "files/data.txt";
$file = fopen($filename,"a");
fwrite($file, $WlanArray[0]['BSSID']." ".$WlanArray[1]['BSSID']." ".$WlanArray[2]['BSSID']." ".$WlanArray[3]['BSSID']." ".
					$WlanArray[0]['RSSI']." ".$WlanArray[1]['RSSI']." ".$WlanArray[2]['RSSI']." ".$WlanArray[3]['RSSI']." ".
					$BLDG." ".$FLOOR." ".$SECTION."\n");
fwrite($file, $BLDG." ".$FLOOR." ".$SECTION."\n");
fclose($file);
echo $filename;
*/

//Connect to database and store values

//include the database funtions 
include 'db_sql.php';


//insert the collected POST data values into the database tables by calling the insert function in db_sql.php
insertData($WlanArray[0]['BSSID'],$WlanArray[1]['BSSID'],$WlanArray[2]['BSSID'],$WlanArray[3]['BSSID'],
				$WlanArray[0]['RSSI'],$WlanArray[1]['RSSI'],$WlanArray[2]['RSSI'],$WlanArray[3]['RSSI'],
				$BLDG,$FLOOR,$SECTION);


//Close the Connection
closeDB();
?>