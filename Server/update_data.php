<?php
/* Summary : This script gets the required POST variables, decodes them and update them into the database.
 * Required POST variable : WLANDATA
 * Required POST variable : BLDG
 * Required POST variable : FLOOR
 * Required POST variable : SECTION
 * Returns : None
 */

//TODO: BLDG, FLOOR and SECTION information should also be transported in JSON format.
 
//Collect POST data
$WLAN_DATA = $_POST['WLANDATA'];
$BLDG = $_POST['BLDG'];
$FLOOR = $_POST['FLOOR'];
$SECTION = $_POST['SECTION'];

//Decode JSON notation to array
$Wlan_Array = json_decode(stripslashes($WLAN_DATA),true);

/*
//Store value to a file
$filename = "files/data.txt";
$file = fopen($filename,"a");
fwrite($file, $Wlan_Array[0]['BSSID']." ".$Wlan_Array[1]['BSSID']." ".$Wlan_Array[2]['BSSID']." ".$Wlan_Array[3]['BSSID']." ".
					$Wlan_Array[0]['RSSI']." ".$Wlan_Array[1]['RSSI']." ".$Wlan_Array[2]['RSSI']." ".$Wlan_Array[3]['RSSI']." ".
					$BLDG." ".$FLOOR." ".$SECTION."\n");
fclose($file);
echo $filename;
*/

//Connect to database and store values

//include the database funtions
include 'db_sql.php';

//Get ID  from the AREA table given building, floor and section information.
$Area_ID = getID_AREA($BLDG,$FLOOR,$SECTION);

//update the database tables with the collected POST WLANDATA by calling the update function in db_sql.php
updateData($Wlan_Array[0]['BSSID'],$Wlan_Array[1]['BSSID'],$Wlan_Array[2]['BSSID'],$Wlan_Array[3]['BSSID'],
			$Wlan_Array[0]['RSSI'],$Wlan_Array[1]['RSSI'],$Wlan_Array[2]['RSSI'],$Wlan_Array[3]['RSSI'],
			$Area_ID[0]['ID']);

//Close the connection
closeDB();
?>