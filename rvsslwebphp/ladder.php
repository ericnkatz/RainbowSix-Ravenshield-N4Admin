<?php
// Script (C)opyright 2003 by =TSAF=Muschel
// Released under GNU GENERAL PUBLIC LICENSE
// www.tsaf.de , muschel@tsaf.de
error_reporting(2047);
foreach ($_GET as $key => $value){${$key}=$value;}
require("config.inc.php");
ConnectTheDBandGetDefaults();
BuildStatsTablesArray();
BuildLadderTablesArray();
BuildPlayerAndNicksTables();
BuildGameMode();
BuildServerIdentNameArrays();
require('language/'.$customlanguage.'.inc.php');
require("header.php");
if (isset($server))
{
 if ($server=="All"){$serverident="%";}
 else {$serverident=$servernameident[$server];}
}
else
{
	die ("Missing server in URL!");
}

if (!isset($mode))
{
	$mode="teamsurvival";
}
else{if (!isset($GameModeArray[$mode]))
{
	$mode="teamsurvival";}
}
$modeid=$GameModeArray[$mode];

$updatetable=$dbtable6."Update";

$writeladdertable=$Laddertable[$mode];

$getscoretable=$Statstable[$mode];

echo "<LINK rel='stylesheet' HREF=\"".$css."\" TYPE='text/css'>";
echo "<body class=body><center>";
?>
<script language="javascript">
<!--
if (document.images) { on = new Image(); on.src = "images/indicator.gif"; off = new Image(); off.src ="images/clear.gif"; }
function mi(n) { if (document.images) {document[n].src = eval("on.src");}}
function mo(n) { if (document.images) {document[n].src = eval("off.src");}}
// -->
</script>
<?php
echo "<table border=0 cellspacing=0 width=".$swidth."><form name=\"rvsstats\" action=\"playerstats.php\"><tr><td align=left class=bigheader background=\"images/".$design[$dset]."_header.gif\">";
ShowFlags("ladder.php","&mode=".$mode."&server=".$server);
echo $text_laddertitle."</td><td align=right align=left class=bigheader background=\"images/".$design[$dset]."_header.gif\"><img src=\"images/clear.gif\" width=10 height=10 name=\"back\"><a class=nav2 href=\"playerstats.php\" onMouseOver=\"mi('back')\" onMouseOut=\"mo('back')\"><b>".$text_back."&nbsp;</a></td></tr><tr><td colspan=2><hr></td></tr></table>";
echo "<table border=0 cellspacing=0 width=".$swidth."><tr>";
echo "<tr><td align=center class=bigheader background=\"images/".$design[$dset]."_header.gif\">";
echo $text_gamemodetext.": ".$text_gamemode[$mode]."</td></tr>";
echo "</td></tr>";
echo "<tr><td align=center class=randende>";
$i=0;
foreach ($Statstable as $modeofstats => $unused)
{
	$i++;
	echo "<a class=nav href=\"ladder.php?mode=".$modeofstats."&server=".$server."\">".$text_gamemode[$modeofstats]."</a>&nbsp;&nbsp;&nbsp;&nbsp;";
	if ($i==4){$i=0;echo "<br>";}
}
echo "</td></td></tr>";
echo "<td align=center class=bigheader background=\"images/".$design[$dset]."_header.gif\">";
echo "Server: ";
if ($serverident=="%")
{
	echo $text_all;
}
else{echo $server;}
echo "</td><tr>";
echo "<tr><td align=center class=randende>";
$i=0;
foreach ($servernameident as $servers => $notused)
{
	$i++;
	if ($i==4){$i=1;echo "<br>";}
	echo "<a class=nav href=\"ladder.php?mode=".$mode."&server=".$servers."\">".$servers."</a>&nbsp;&nbsp;&nbsp;&nbsp;";
}
echo "<br><a class=nav href=\"ladder.php?mode=".$mode."&server=All\">".$text_all."</a>";
echo "</td></tr>";
require("ladder_update.php");
$countrows="Select ".$writeladdertable.".id from ".$writeladdertable." LEFT JOIN ".$Playertable." ON (".$writeladdertable.".fromid = ".$Playertable.".id) WHERE ".$Playertable.".serverident LIKE '".$serverident."' and ".$Playertable.".ubiname<>'' ";
$countedrows=mysql_query ($countrows);
$ranked=mysql_num_rows($countedrows);
if ($ranked>0)
{
	if (!isset($site))
	{
		$site=1;
	}
	echo "<tr><td class=randende>";
	echo $text_site." (".$site.") :&nbsp;";
	$i=($site*$displayglobalranksonsite)-$displayglobalranksonsite+1;
	$sitecount=$ranked/$displayglobalranksonsite+0.99999999;
	$x=$i+$displayglobalranksonsite-1;
	if ($x<$ranked){$j=$x;}else{$j=$ranked;}

	for ($si=1;$si<=$sitecount;$si++)
	{
		echo "<a class=nav href=\"ladder.php?mode=".$mode."&server=".$server."&site=".$si."\">-".$si."-</a> ";
	}
	echo "</td></tr>";
	echo "<tr><td><hr></td></tr></table>";

	echo "<table border=0 cellspacing=0 width=".$swidth."><tr>";
	echo "<td width=\"10%\" align=center class=bigheader background=\"images/".$design[$dset]."_header.gif\">";
	echo $text_rank."</td><td width=\"15%\" align=center class=bigheader background=\"images/".$design[$dset]."_header.gif\">";
	echo $text_score."</td><td width=\"30%\" align=center class=bigheader background=\"images/".$design[$dset]."_header.gif\">";
	echo "Ubi</td>";
	if ($serverident=="%")
	{
		echo "<td align=center class=bigheader background=\"images/".$design[$dset]."_header.gif\">";
		echo $text_onserver."</td></tr></table>";
	}

	$i=$displayglobalranksonsite*($site-1);

	echo "<table border=0 cellspacing=0 width=".$swidth."><tr>";
	$look="Select ".$writeladdertable.".score,".$Playertable.".ubiname,".$Playertable.".serverident FROM ".$writeladdertable." LEFT JOIN ".$Playertable." ON (".$writeladdertable.".fromid = ".$Playertable.".id) WHERE ".$Playertable.".serverident LIKE '".$serverident."' and ".$Playertable.".ubiname<>'' ORDER BY score DESC LIMIT ".$displayglobalranksonsite*($site-1).",".$displayglobalranksonsite."";
	$looked=mysql_query ($look);
	if ($serverident=="%"){$smode="rand";}else{$smode="randende";}
	while ($place=mysql_fetch_array($looked, MYSQL_ASSOC))
	{
		$i++;
		echo "<td class=rand width=\"10%\" align=center>";
		echo $i;
		echo "</td><td class=rand width=\"15%\" align=center>";
		echo $place['score'];
		echo "</td><td class=".$smode." width=\"30%\">";
		echo "<img src=\"images/clear.gif\" width=10 height=10 name=\"rank".$i."\">";
		echo "<a onMouseOver=\"mi('rank".$i."')\" onMouseOut=\"mo('rank".$i."')\" class=nav href=\"playerstats.php?mode=".$mode."&server=".$serveridentname[$place['serverident']]."&playersearch=".$place['ubiname']."\"><b>".$place['ubiname']."</b></a>";
		echo "</td>";
		if ($serverident=="%")
		{
			echo "<td class=randende align=center>";
			echo $serveridentname[$place['serverident']];
			echo "</td>";
		}
		echo "</tr>";
	}
}
else
{
	echo "<tr><td class=randende colspan=3 align=center>No Data!</td></tr>";
}

echo "</table><br><a class=nav href=\"playerstats.php\">".$text_back."</a>";
echo Copyrightextstats();
echo "</form></center></body>";
?>
