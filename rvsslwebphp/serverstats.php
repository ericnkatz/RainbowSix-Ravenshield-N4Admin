<?php
// Script (C)opyright 2003 by =TSAF=Muschel
// Released under GNU GENERAL PUBLIC LICENSE
// www.tsaf.de , muschel@tsaf.de
error_reporting(2047);
foreach ($_GET as $key => $value){${$key}=$value;}
$text_serverstatstitle="Raven Shield Server overall Stats";
$text_choose="Choose";
require("config.inc.php");
ConnectTheDBandGetDefaults();
BuildStatsTablesArray();
BuildPlayerAndNicksTables();
BuildServerIdentNameArrays();
require('language/'.$customlanguage.'.inc.php');
require("header.php");

echo "<LINK rel='stylesheet' HREF=\"".$css."\" TYPE='text/css'>";
echo "<body class=body><center>";
?>
<script language="javascript">
<!--
if (document.images) { on = new Image(); on.src = "images/indicator.gif"; off = new Image(); off.src ="images/clear.gif"; }
function mi(n) { if (document.images) {document[n].src = eval("on.src");}}
function mo(n) { if (document.images) {document[n].src = eval("off.src");}}
function mim(n) { document["mappic"].src = n;}
function mom(n) { document["mappic"].src = "images/defaultmap.jpg";}
// -->
</script>
<?php


if (!isset($mode)){$mode="";}
if (!isset($server)){$server="%";}

echo "<table border=0 cellspacing=0 width=".$swidth."><form name=\"rvsstats\" action=\"serverstats.php\"><tr><td align=left class=bigheader background=\"images/".$design[$dset]."_header.gif\">";
ShowFlags("serverstats.php","&mode=".$mode."&server=".$server);

echo $text_serverstatstitle."</td><td align=right class=bigheader background=\"images/".$design[$dset]."_header.gif\"><img src=\"images/clear.gif\" width=10 height=10 name=\"back\"><a class=nav2 href=\"playerstats.php\" onMouseOver=\"mi('back')\" onMouseOut=\"mo('back')\"><b>".$text_back."&nbsp;</a></td></tr><tr><td colspan=2><hr></td></tr></table>";

if (isset($servernameident[$server]) and $server!="%"){$serverident=$servernameident[$server];}
else {$serverident="%";}
echo "<table  border=0 cellspacing=0 width=".$swidth."><tr><td align=center colspan=2 class=bigheader background=\"images/".$design[$dset]."_header.gif\">".$text_choose."</td></tr>";
echo "<tr><td class=rand align=center colspan=1>";
$i=0;
foreach ($Statstable as $modeofstats => $unused)
{
$i++;
echo "<a class=nav href=\"serverstats.php?mode=".$modeofstats."&server=".$server."\">".$text_gamemode[$modeofstats]."</a>&nbsp;&nbsp;&nbsp;&nbsp;";
if ($i==4){$i=0;echo "<br>";}
}
echo "</td>";
echo "<td class=rand align=center colspan=1 rowspan=3><img src=\"images/defaultmap.jpg\" width=75 hight=50 name=\"mappic\"></td></tr>";
echo "<tr><td class=rand colspan=1 align=center>";
echo "<a class=nav href=\"serverstats.php?mode=".$mode."&server=%\">".$text_all." Server</a>";
echo "</td></tr>";
echo "<tr><td class=rand colspan=1 align=center>";
if (isset($servernameident))
{
$i=0;
foreach ($servernameident as $servers => $idents)
{
$i++;
if ($i==4){$i=1;echo "<br>";}
echo "<a class=nav href=\"serverstats.php?mode=".$mode."&server=".$servers."\">".$servers."</a>&nbsp;&nbsp;&nbsp;&nbsp;";
}
echo "</td></tr>";
echo "<form><input type=hidden name=server value=\"".$server."\"><input type=hidden name=mode value=\"".$mode."\">";

}
echo "<tr><td colspan=2><hr></td></tr></table>";

if (!($server=="%"))
{
if (!isset($servernameident[$server])){die ("no Data for this Server!");}
else {$serveridents[$server]=$servernameident[$server];}
}
else
{
$serveridents=$servernameident;
}

if (isset($Statstable[$mode]))
{

echo "<table border=0 cellspacing=0 width=".$swidth.">";
echo "<tr><td align=center colspan=5 class=bigheader background=\"images/".$design[$dset]."_header.gif\">Server:";
if ($server=="%"){echo $text_all;}
else {echo $server;}
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Gamemode:".$text_gamemode[$mode]."</td></tr>";
echo "<tr><td class=rand>&nbsp;<b>".$text_map."</b></td><td class=rand align=center width=\"10%\"><b>".$text_pdkills."</b></td><td class=rand align=center width=\"10%\"><b>".$text_pddeaths."</b></td><td class=rand align=center width=\"10%\"><b>Fired</b></td><td class=randende align=center width=\"10%\"><b>Hits</b></td></tr>";

$resstats = mysql_query ("SELECT ".$Statstable[$mode].".map as map, sum(".$Statstable[$mode].".kills) as sumkills, sum(".$Statstable[$mode].".deaths) as sumdeaths ,sum(".$Statstable[$mode].".fired) as sumfired,sum(".$Statstable[$mode].".hits) as sumhits  FROM ".$Statstable[$mode]." INNER JOIN ".$Playertable." ON (".$Statstable[$mode].".fromid = ".$Playertable.".id) WHERE ".$Playertable.".serverident like '".$serverident."' and ".$Playertable.".ubiname<>'' GROUP by ".$Statstable[$mode].".map ORDER BY ".$Statstable[$mode].".map ");
$sumkills=0;
$sumdeaths=0;
$sumfired=0;
$sumhits=0;

while ($stats=mysql_fetch_array($resstats, MYSQL_ASSOC))
{
$mapimage="mapimages/".strtolower($stats['map']).".jpg";
if (!file_exists($mapimage)){$mapimage="images/defaultmap.jpg";}

echo "<tr onMouseOver=\"mim('".$mapimage."')\" onMouseOut=\"mom('mappic')\"><td class=rand>&nbsp;".$stats['map']."</td>";
echo "<td class=rand align=right>".$stats['sumkills']."&nbsp;</td><td class=rand align=right>".$stats['sumdeaths']."&nbsp;</td><td class=rand align=right>".$stats['sumfired']."&nbsp;</td><td class=randende align=right>".$stats['sumhits']."&nbsp;</td></tr>";
$sumkills=$sumkills+$stats['sumkills'];
$sumdeaths=$sumdeaths+$stats['sumdeaths'];
$sumfired=$sumfired+$stats['sumfired'];
$sumhits=$sumhits+$stats['sumhits'];
}
echo "<tr><td class=rand align=left>&nbsp;<b>Overall</b></td><td class=rand align=right><b>".$sumkills."</b>&nbsp;</td><td class=rand align=right><b>".$sumdeaths."</b>&nbsp;</td><td class=rand align=right><b>".$sumfired."</b>&nbsp;</td><td class=randende align=right><b>".$sumhits."</b>&nbsp;</td></tr>";

}

echo "</table><br><br><a class=nav href=\"playerstats.php\">".$text_back."</a>";

?>
<?=Copyrightextstats()?></form></center></body>
