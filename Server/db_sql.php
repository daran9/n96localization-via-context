<?php
/* Summary : The script has functions with SQL query commands that create the tables, 
 * insert values into the tables, select values from the table and update values in the table. 
 * The database currently consists of these three tables : BSSID, RSSI and AREA. 
 */


// Open the sqlite3 database file and create a handler
// Both the folder and the file should have read and write permssions.
$db = new PDO("sqlite:files/db_mobilocate.sqlite");

//Only need to be called once to create the tables BSSID, RSSI and AREA
//createTable();


/*
 ****************************************************************************************************
 * FUNCTIONS
 ****************************************************************************************************
 */

/* Summary : This function is called once to create the tables BSSID, RSSI and AREA
 * and the unique ID is auto incremented so it will be the same across all  the tables.
 * The tables BSSID and RSSI has 4 values. The AREA table has BLDG, FLOOR and SECTION. 
 * Parameters : None
 * Return : None
 */
function createTable()
{
	
	global $db;
	$db->exec("CREATE TABLE BSSID (
							ID INTEGER PRIMARY KEY, 
							BSSID1 VARCHAR(255),
							BSSID2 VARCHAR(255),
							BSSID3 VARCHAR(255),
							BSSID4 VARCHAR(255))") or die(print_r($db->errorInfo()));
											
	$db->exec("CREATE TABLE RSSI (
							ID INTEGER PRIMARY KEY,
							RSSI1 INTEGER,
							RSSI2 INTEGER,
							RSSI3 INTEGER,
							RSSI4 INTEGER)") or die(print_r($db->errorInfo()));
	
	$db->exec("CREATE TABLE AREA (
							ID INTEGER PRIMARY KEY,
							BLDG VARCHAR(255),
							FLOOR INTEGER,
							SECTION VARCHAR(255))") or die(print_r($db->errorInfo()));
							
	/*$db->exec("CREATE TABLE PHOTO (
							ID INTEGER PRIMARY KEY,
							PHOTO_URL VARCHAR(255))") or die(print_r($db->errorInfo()));*/
							
	/*$db->exec("CREATE TABLE ACCEL (
							ID INTEGER PRIMARY KEY,
							X_AXIS INTEGER,
							Y_AXIS INTEGER,
							Z_AXIS INTEGER)") or die(print_r($db->errorInfo()));*/
}

/* Summary : This function inserts BSSID and RSSI values together with the building, floor and section information 
 * to their respective tables.
 * Parameters : BSSID1, BSSID2, BSSID3, BSSID4, RSSI1, RSSI2, RSSI3, RSSI4, BLDG, FLOOR, SECTION
 * Return : None
 */
function insertData($BSSID1, $BSSID2, $BSSID3, $BSSID4,
							$RSSI1, $RSSI2, $RSSI3, $RSSI4, 
							$BLDG, $FLOOR, $SECTION)
{
	
	global $db;
	$db->exec("INSERT INTO BSSID (BSSID1,BSSID2,BSSID3,BSSID4) 
							VALUES ('{$BSSID1}', '{$BSSID2}', '{$BSSID3}', '{$BSSID4}')")or die(print_r($db->errorInfo()));
							
	$db->exec("INSERT INTO RSSI (RSSI1,RSSI2,RSSI3,RSSI4) 
							VALUES ('{$RSSI1}', '{$RSSI2}', '{$RSSI3}', '{$RSSI4}')")or die(print_r($db->errorInfo()));
							
	$db->exec("INSERT INTO AREA (BLDG,FLOOR,SECTION) 
							VALUES ('{$BLDG}', '{$FLOOR}', '{$SECTION}')")or die(print_r($db->errorInfo()));
}


/* Summary : This function updates BSSID and RSSI values for the particular ID 
 * Parameters : BSSID1, BSSID2, BSSID3, BSSID4, RSSI1, RSSI2, RSSI3, RSSI4, ID
 * Return : None
 */
function updateData($BSSID1, $BSSID2, $BSSID3, $BSSID4,
							$RSSI1, $RSSI2, $RSSI3, $RSSI4, 
							$ID)
{
	
	global $db;
	$db->exec("UPDATE BSSID
								SET BSSID1='{$BSSID1}',BSSID2='{$BSSID2}',BSSID3='{$BSSID3}',BSSID4='{$BSSID4}'
								WHERE ID='{$ID}'")or die(print_r($db->errorInfo()));
	
	$db->exec("UPDATE RSSI
								SET RSSI1='{$RSSI1}',RSSI2='{$RSSI2}',RSSI3='{$RSSI3}',RSSI4='{$RSSI4}'
								WHERE ID='{$ID}'")or die(print_r($db->errorInfo()));						
							
}


/* Summary : This Function returns an array all the possible IDs for the particluar BSSID value
 * Parameters : BSSID
 * Return : Array of IDs
 */
function getID_BSSID($BSSID)
{
	global $db;	
	$ID = $db->query("SELECT ID FROM BSSID WHERE BSSID1='{$BSSID}' OR BSSID1='{$BSSID}' OR BSSID1='{$BSSID}' OR BSSID1='{$BSSID}'")or die(print_r($db->errorInfo()));
	return $ID->fetchAll(); 

}

/* Summary : This funtion returns the row in the BSSID table for the given ID
 * Parameters : ID
 * Return : Array of BSSIDs
 */
function getBSSID($ID)
{
	global $db;	
	$table_BSSID = $db->query("SELECT BSSID1, BSSID2, BSSID3, BSSID4 FROM BSSID WHERE ID='{$ID}'")or die(print_r($db->errorInfo()));
	return $table_BSSID->fetchAll();
}


/* Summary : This funtion returns the row in the RSSI table for the given ID
 * Parameters : ID
 * Return :
 */
function getRSSI($ID)
{
	global $db;	
	$table_RSSI = $db->query("SELECT RSSI1, RSSI2, RSSI3, RSSI4 FROM RSSI WHERE ID='{$ID}'")or die(print_r($db->errorInfo()));
	return $table_RSSI->fetchAll();
}

/* Summary : This funtion returns the row in the AREA table for the given ID
 * Parameters : ID
 * Return : Array of BLDG, FLOOR and SECTION
 */
function getAREA($ID)
{
	global $db;	
	$table_AREA = $db->query("SELECT BLDG, FLOOR, SECTION FROM AREA WHERE ID='{$ID}'")or die(print_r($db->errorInfo()));
	return $table_AREA->fetchAll();

}

/* Summary : This function returns the ID from the AREA table for the building, floor and section
 * Parameters : BLDG, FLOOR and SECTION
 * Return : ID
 */
function getID_AREA($BLDG, $FLOOR, $SECTION)
{
	
	global $db;
	$ID = $db->query("SELECT ID FROM AREA WHERE BLDG='{$BLDG}' AND FLOOR='{$FLOOR}' AND SECTION='{$SECTION}'")or die(print_r($db->errorInfo()));
	return $ID->fetchAll();

}

/*
//This function returns an array
function getBSSIDs(){
	global $db;
	$table_BSSID = $db->query("SELECT BSSID1, BSSID2, BSSID3, BSSID4 FROM BSSID")or die(print_r($db->errorInfo()));
	return $table_BSSID->fetchAll();
}

//This function returns an array
function getRSSIs(){
	global $db;	
	$table_RSSI = $db->query("SELECT RSSI1, RSSI2, RSSI3, RSSI4 FROM RSSI")or die(print_r($db->errorInfo()));
	return $table_RSSI->fetchAll();
}
*/


/* Summary : This function closes the database connection
 * Parameters : None
 * Return : None
 */
function closeDB()
{
	global $db;	
	$db= null;
}   
?>