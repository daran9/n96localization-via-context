<?php
/* Summary : This script gets the JPEG photo file form the phone client and saves it on the servers. Timestamps can be added to the filename.
 * Required : JPEG photo file
 * Returns : None 
 */

$chunk = file_get_contents('php://input');
$handle = fopen('files/uploadPhoto.jpg', 'wb');
fputs($handle, $chunk, strlen($chunk));
fclose($handle); 
?>