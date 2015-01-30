<?php 
include('polyPoint.php'); 

$dbHost = '';
$dbUser = ''; 
$dbPassword = '';
$dbName = ''; 
$dbTable = '';
$dbTable2 = '';

mysql_connect($dbHost, $dbUser, $dbPassword) or die('Could not connect to Database'); 
mysql_select_db($dbName) or die('Could not select database'); 

$dbTable3 = 'googleMapsLookup';
	$createTable = "CREATE TABLE IF NOT EXISTS ".$dbTable3."(";
	$createTable .= " mapId text NOT NULL, ";
	$createTable .= " inclusion text )";

	mysql_query($createTable) or die(mysql_error() );

$truncate = 'TRUNCATE TABLE googleMapsLookup';
mysql_query($truncate) or die(mysql_error() );

$points = array(); 
$polygon = array(); 

$select = 'SELECT * FROM '.$dbTable;
$result = mysql_query($select) or die(mysql_error()); 

$select2 = 'SELECT * FROM '.$dbTable2;
$result2 = mysql_query($select2) or die( mysql_error() );

while($row2 = mysql_fetch_array($result2) )
{
	array_push($points, $row2['latlong']); 
	//array_push($points, '39.990082 -86.17381999999998' );
}
while($row = mysql_fetch_array($result) )
{
	reset($polygon);
	$regionName = $row['location'];
	//echo $row['latLong'];
	$polygon = explode(",",$row['latLong']);
	points($regionName, $polygon, $points);
}

function points($name, $polygona, $pointsa)
{
	print_r($pointsa);
	print_r($polygona);
	$pointLocation = new pointLocation(); 

	foreach($pointsa as $key => $point)
	{
		//echo $point; 
		$included = $pointLocation->pointInPolygon($point, $polygona); 
		//echo "point " . ($key+1) . " ($point): " . $pointLocation->pointInPolygon($point, $polygona) . "<br>";

		if ( $included == 'inside')
		{
			$insert = "INSERT INTO googleMapsLookup VALUES ('".$point."','".addslashes($name)."')";
			mysql_query($insert) or die(mysql_error() );
		}
		else { 
			//print_r($array );
			//only add those points that are either inside or or on the polygon 
			//echo $name.' '.$point.' '.$included.'<br/>';
			//$insert = "INSERT INTO googleMapsLookup VALUES ('".$point."','".addslashes($name)."')";
			//echo $insert; 
		}
		//echo "point " . ($key+1) . " ($point): " . $pointLocation->pointInPolygon($point, $polygon) . "<br>";
	} 
}
?>
