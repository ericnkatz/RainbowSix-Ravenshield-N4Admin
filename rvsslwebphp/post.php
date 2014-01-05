<?php
// Script (C)opyright 2003 by =TSAF=Muschel
// Released under GNU GENERAL PUBLIC LICENSE
// www.tsaf.de , muschel@tsaf.de
require("config.inc.php");
if (isset($_POST))
{
	$db=mysql_connect("$dbHost", "$dbUser", "$dbPass") or die ("<CENTER>Connect-Error to MySQL!");
	mysql_select_db("$dbDatabase", $db) or die ("<CENTER>Connect-Error to Database!");
	BuildGameModeTranslateArray();
	BuildStatsTablesArray();
	BuildPlayerAndNicksTables();
	BuildServerIdentNameArrays();
	$PostedGameMode=$_POST['F1'];

	if (!isset($Statstable[$GameModeTranslate[$PostedGameMode]]))
	{
		die ("Webserver does not log this gamemode:".$PostedGameMode."!");
	}

	$writetable=$Statstable[$GameModeTranslate[$PostedGameMode]];

	$ident=$_POST['ident'];
	if(!in_array($ident,$servernameident))
	{
		die ("Ident ".$ident." not known by Webserver!");
	}
	$map=$_POST['E1'];

	$Nick=explode("/",substr($_POST['L1'],1));
	$Ubi=explode("/",substr($_POST['UB'],1));
	$Kills=explode("/",substr($_POST['O1'],1));
	$Deaths=explode("/",substr($_POST['DE'],1));
	$Fired=explode("/",substr($_POST['RF'],1));
	$Hits=explode("/",substr($_POST['HI'],1));
	$StartUbi=explode("/",substr($_POST['US'],1));
	$StartDeaths=explode("/",substr($_POST['DS'],1));
	$StartFired=explode("/",substr($_POST['RS'],1));
	$StartHits=explode("/",substr($_POST['HS'],1));
	
	$counter=0;

	while (isset($StartUbi[$counter]))
	{
		$StartUbiArray[$StartUbi[$counter]]['startdeaths']=$StartDeaths[$counter];
		$StartUbiArray[$StartUbi[$counter]]['startfired']=$StartFired[$counter];
		$StartUbiArray[$StartUbi[$counter]]['starthits']=$StartHits[$counter];
		$counter++;
	}

	$counter=0;

	while (isset($Ubi[$counter]))
	{
		$look="SELECT id FROM ".$Playertable." WHERE serverident='".$ident."' and ubiname='".$Ubi[$counter]."'";
		$res=mysql_query($look);
		if (mysql_num_rows($res)==0)
		{
			$add="INSERT INTO ".$Playertable." VALUES('','".$ident."','".$Ubi[$counter]."')";
			$res = mysql_query($add);
			$look="SELECT id FROM ".$Playertable." WHERE serverident='".$ident."' and ubiname='".$Ubi[$counter]."'";
			$res=mysql_query($look);
		}
		$dbrow=mysql_fetch_array($res);
		$dbubiid=$dbrow['id'];

		$look="SELECT id FROM ".$Nicktable." WHERE fromid='".$dbubiid."' and nick='".$Nick[$counter]."'";
		$res = mysql_query ($look);
		if (mysql_num_rows($res)==0)
		{
			$add="INSERT INTO ".$Nicktable." VALUES ('','".$dbubiid."','".$Nick[$counter]."')";
			mysql_query($add);
		}

		if (isset($StartUbiArray[$Ubi[$counter]]['startdeaths']))
		{

			$look="SELECT * FROM ".$writetable." WHERE fromid='".$dbubiid."' and map='".$map."'";
			$res = mysql_query ($look);

			if (mysql_num_rows($res)==0)
			{
				$add="INSERT INTO ".$writetable." VALUES ('','".$dbubiid."','".$Kills[$counter]."','".$Deaths[$counter]."','".$map."','1','".$Fired[$counter]."','".$Hits[$counter]."')";
				mysql_query($add);
			}
			elseif (mysql_num_rows($res)==1)
			{
				$dbrow=mysql_fetch_array($res, MYSQL_ASSOC);

				$newkills=$Kills[$counter]+$dbrow['kills'];
				$newdeaths=$Deaths[$counter]+$dbrow['deaths']-$StartUbiArray[$Ubi[$counter]]['startdeaths'];
				$newfired=$Fired[$counter]+$dbrow['fired']-$StartUbiArray[$Ubi[$counter]]['startfired'];
				$newhits=$Hits[$counter]+$dbrow['hits']-$StartUbiArray[$Ubi[$counter]]['starthits'];
				$newrounds=$dbrow['roundsplayed']+1;

				$update="UPDATE ".$writetable." SET";
				$update.=" kills='".$newkills."',";
				$update.=" deaths='".$newdeaths."',";
				$update.=" roundsplayed='".$newrounds."',";
				$update.=" fired='".$newfired."',";
				$update.=" hits='".$newhits."'";
				$update.=" WHERE id='".$dbrow['id']."'";
				mysql_query($update);
			}
		}

		$counter++;
	}// end of while
	echo "Gamemode:".$PostedGameMode."<br>";
	echo "The web server received the Data!";
}
else
{
	echo "no data!";
}
?>
