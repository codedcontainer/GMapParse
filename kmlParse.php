<?php
$kml = simplexml_load_file('');

$dbHost = ''; 
$dbUser = ''; 
$dbPassword = ''; 
$dbName = ''; 
$dbTable = '';

mysql_connect($dbHost, $dbUser, $dbPassword) or die('Could not connect to Database'); //Connect php mysql client to Bells server
mysql_select_db($dbName) or die('Could not select database'); //Connect php mysql client 

$createTable = "CREATE TABLE IF NOT EXISTS ".$dbTable."(";
$createTable .= " id INT(2) AUTO_INCREMENT NOT NULL PRIMARY KEY, ";
$createTable .= " location VARCHAR(10), ";
$createTable .=" latLong text )";

mysql_query($createTable) or die(mysql_error());

//get the total number polygons 
$numPoly = count( $kml->Document[0]->Folder);
$newArray = array(); 
$a = 1; 
$explodeCords = array(); 
$regionNames = array(); 
for( $i=0; $i <= $numPoly; $i++)
{

	$numPlace = count( $kml->Document[0]->Folder[$i]->Placemark); //count the number of placemarks there are 
	$namePlace = $kml->Document[0]->Folder[$i]->Placemark->name; 

	for($z = 0; $z <= $numPlace; $z++) 
	{
		$regionName = $kml->Document[0]->Folder[$i]->Placemark[$z]->name."</br>"; //grab each placemarks name 
		//get the cordinates inside of each placemark 
		$regionCords = $kml->Document[0]->Folder[$i]->Placemark[$z]->Polygon->outerBoundaryIs->LinearRing->coordinates;
		//print_r($regionCords); print "<br/><br/>";
		foreach($regionCords as $num2 => $region)
		{ //print each of the regions cordinates as a full string.
			// ex: "89.0, 39.89, 0.0 -86.254, 39.97"
			//print $a; //prints each of the 29 coordinate listings.
			//adds a new array that will be used outside of all loops to switch lat and long
			$explodeCords[$a] = array_splice( explode('|', str_replace(',0.0',' |', $regionCords) ), 0, -1 ) ;
			//gets each individual lat and long and explodes them all into larger array
			//ex: "[0] => -86.302, 39.97 [1]=> -89.09, 39.09 
		}
		$numCordinates =  count($explodeCords['coordinates'] ); //returns the number of [lat,long] cords in each cordinate set
		if ( !empty( $regionCords ) )
		{
			$a++; //counter that is utilized to display number of regions in XML 
		}
	}
}
//lat and long in polygon conversion needs to be represented as "39.374 89.987564, 0 848474, ....."
function arraySwitch($n)
{
  	$test = explode(',', $n);
  	$c = $test[1].' '.$test[0];
  	return $c;
}
foreach ( $explodeCords as $keyChain => $exploded)
{
	$explodeCords[$keyChain] = array_map("arraySwitch", $explodeCords[$keyChain]);
	$explodeCords[$keyChain] = implode(', ',$explodeCords[$keyChain]); 
	
}
	foreach ( $explodeCords as $keyChain2 => $exploded2 )
	{
		$insert = "INSERT INTO ".$dbTable." VALUES ('','','".$exploded2."');";
		mysql_query($insert) or die(mysql_error() );
	} 
?>
