<?php
/* Summary : This script using the BSSID and RSSI values in WLANDATA and calculates and return the position to the phone client.
 * Required POST variable : WLANDATA
 * Return : 
 */

$WLAN_DATA = $_POST['WLANDATA'];
//Decode JSON notation to associative array containing BSSID and RSSI values
$Wlan_Array = json_decode(stripslashes($WLAN_DATA),true);

/*
//Store values to a file
$filename = "files/data.txt";
$file = fopen($filename,"a");
fwrite($file, $Wlan_Array[0]['BSSID']." ".$Wlan_Array[1]['BSSID']." ".$Wlan_Array[2]['BSSID']." ".$Wlan_Array[3]['BSSID']." ".
					$Wlan_Array[0]['RSSI']." ".$Wlan_Array[1]['RSSI']." ".$Wlan_Array[2]['RSSI']." ".$Wlan_Array[3]['RSSI']."\n");
//fclose($file);
*/

//include the database funtions
include 'db_sql.php';

// Add the AREA IDs for the BSSID value to the array
$ID_Array = array();

//for each BSSID value in the array find the IS and add it to ID_ARRAY
foreach($Wlan_Array as $value)
{
	add_ID_BSSID($value['BSSID']);
}

//Find the most appeared Area ID in the ID_array
//array_count_values return an associative array with the number of times the value occurs 
$ID_Count = array_count_values($ID_Array);
//This searches the array and get the ID that occurs most number of times
$Area_ID = array_search(max($ID_Count), $ID_Count);

//Gets the row of BSSID and RSSI values from the database table with for the given ID
$BSSID_ARRAY = getBSSID($Area_ID);
$RSSI_ARRAY = getRSSI($Area_ID);

//counts the number of matches of BSSID and RSSI values.
$count = 0;

//Position Approximation algorithm
//For each BSSID and its corresponding RSSI value, use a position calculation alogrithm and increment count 
foreach($RSSI_ARRAY as $rowRSSI )
{
	foreach($BSSID_ARRAY as $rowBSSID )
	{	
		//For that ID check if RSSI values are within +- 10
		foreach($Wlan_Array as $value)
		{		
			compare_BSSIDs_RSSIs($value['BSSID'],$value['RSSI'], $rowBSSID, $rowRSSI);
		}
	}
}


//If count is more than zero, get the results corresonding to the ID in the AREA table, decode it and return it to the phone client
//If not return null values.
if($count > 0)
{
	$AREA_Array = getAREA($Area_ID);
	foreach($AREA_Array as $row)
	{
		$results = array ('BLDG'=>$row['BLDG'],'FLOOR'=>$row['FLOOR'],'SECTION'=>$row['SECTION']);
		echo json_encode($results);
	}
}
else
{
	$results = array ('BLDG'=>'N','FLOOR'=>'N','SECTION'=>'N');
	echo json_encode($results);
}

//close the database connection
closeDB();

/*
 ****************************************************************************************************
 * FUNCTIONS
 ****************************************************************************************************
 */
 
/* Summary : This Function finds the ID corresponding to the BSSID value from the database table and adds to the ID_ARRAY
 * Parameters : BSSID value
 * Return : None
 */
function add_ID_BSSID($BSSID){
	global $ID_Array;
	$ID_BSSID = getID_BSSID($BSSID);
	foreach($ID_BSSID as $row)
	{
		$ID_Array[] = $row['ID'];
	}
}

/* Summary : This Function compares the BSSID value with each of the BSSID value in the array and if there is a match,
 * checks to see if the RSSI value and each of the RSSI values in the array is between +_ 10 and increments the count variable.
 * Parameters : BSSID, RSSI , array of BSSID, array of RSSI
 * Return : None
 */
function compare_BSSIDs_RSSIs($BSSID, $RSSI, $BSSID_Array, $RSSI_Array)
{
	global $count;		
	//Compare each of the four BSSID in BSSID_Array with BSSID and check the absolute value of the RSSI value 
	// and the RSSI value in RSSI_Araay corresponding to each BSSID value in the BSSID_ARRAY is between 0 and 10, and 
	// increment the count variable
	
	if(strcmp($BSSID, $BSSID_Array['BSSID1']) == 0)
	{								
		if( abs($RSSI_Array['RSSI1'] - $RSSI)>= 0  && abs($RSSI_Array['RSSI1'] - $RSSI) < 10 )
		{
			$count++;
		}			
	}
	if(strcmp($BSSID, $BSSID_Array['BSSID2']) == 0)
	{
		if(abs($RSSI_Array['RSSI2'] - $RSSI)>= 0 && abs($RSSI_Array['RSSI2'] - $RSSI) < 10)
		{
			$count++;				
		}
	}
	if(strcmp($BSSID, $BSSID_Array['BSSID3']) == 0)
	{
		if(abs($RSSI_Array['RSSI3'] - $RSSI)>= 0 && abs($RSSI_Array['RSSI3'] - $RSSI) < 10)
		{
			$count++;				
		}
	}
	if(strcmp($BSSID, $BSSID_Array['BSSID4']) == 0)
	{
		if(abs($RSSI_Array['RSSI4'] - $RSSI)>= 0 && abs($RSSI_Array['RSSI4'] - $RSSI) < 10)
		{
			$count++;				
		}
	}
}

?>
