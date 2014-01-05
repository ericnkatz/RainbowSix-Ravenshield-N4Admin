<?php
//**************************************************************************
// Begin of editable Area:

//Database config
$dbHost   	=   	"localhost"; 	// Hostname of the MySQL-Database
$dbUser   	=   	"DBUser";		// MySQL Username
$dbPass   	=   	"DBPass"; 	// MySQL Password
$dbDatabase =   	"DBName"; 	// MySQL Database Name

// Display Width
$swidth		=	600; 		// External width
$awidth		=	600; 		// Adminarea width

//stats
$displayglobalranksonsite=50;
$displaynickononesite=50;
$displayubiononesite=50;

// End of editable Area
//***************************************************************************
// Advanced config

$language['0']="english";
$language['1']="german";
$language['2']="french";
$language['3']="spanish";
$language['4']="dutch";
$language['5']="portuguese";

$design['0']="blue";
$design['1']="grey";
$design['2']="tsaf";
$design['3']="dj";
$design['4']="red";

$pic_Alive['0']="alive.gif";
$pic_Alive['1']="wounded.gif";
$pic_Alive['2']="dead.gif";
$pic_Alive['3']="clear.gif";

$dbtable1="RVS_ServerlistServers";
$dbtable2="RVS_ServerlistConfig";
$dbtable3="RVS_ServerlisteLinks";
$dbtable4="RVS_ServerStats_";
$dbtable5="RVS_ServerListStats_";
$dbtable6="RVS_ServerLadder_";

$socket_timeout="1";         //Timeout in Seconds
$socket_blocking_use=False;  //False~PHP-Default, True sets blocking to True

$ladder_update_time=300; //in seconds

// End of advanced config
//***************************************************************************
// don´t edit !
require ("requires/functions.php");
?>
