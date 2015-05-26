<?php
/* Summary : This script retrieves the google map static image of the given coordiantes and sends the image file to the phone client.
 * Required GET variable : lat
 * Required GET variable : long
 * Return : png image file
 */
 
 
// The Google Static Maps API is used with the given latitude and longitude to retrieve the png image of the lcoation.
// The key identifies the Maps API key for the domain on which this URL request takes place.
// For more information please check the API at http://code.google.com/apis/maps/documentation/staticmaps/ 
$lat = $_GET['lat'];
$long = $_GET['long'];
$key=ABQIAAAApuATBs78kFadt72RDfGevRQ8e4Fk11SlO0hOsVhsd4fiCAg6BRT71P4hhbvh_6zlWwfaIAu_FMQa_g;
$image = 'http://maps.google.com/staticmap?center=' . $lat . ',' . $long . '&markers=' . $lat . ',' . $long . '&format=png32&zoom=15&size=240x320&key=' . $key . '';

//The png image is saved as a file at the following location in the server
$filename = 'files/imageMap.png';
copy($image, $filename);

//the png image file is sent back to the phone client.
$file = file_get_contents ($filename);
echo $file; 
?>