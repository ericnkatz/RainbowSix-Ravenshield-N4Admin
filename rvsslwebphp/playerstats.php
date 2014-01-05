<?php
// Script (C)opyright 2003 by =TSAF=Muschel
// Released under GNU GENERAL PUBLIC LICENSE
// www.tsaf.de , muschel@tsaf.de
error_reporting(2047);
foreach ($_GET as $key => $value){${$key}=$value;}

require("config.inc.php");
ConnectTheDBandGetDefaults();
BuildStatsTablesArray();
BuildPlayerAndNicksTables();
BuildServerIdentNameArrays();
require('language/'.$customlanguage.'.inc.php');
require("header.php");

function decodenick ($before)
{
	$after=str_replace("^-^","&",$before);
	return $after;
}
function nicksearchencode ($before)
{
	$after=str_replace("&","^-^",$before);
	return $after;
}

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

if (!isset($site))
{
	$site=1;
}
if (!isset($mode))
{
	$mode="";
}
if (!isset($nicksearch))
{
	$nicksearch="";
}
if (!isset($server))
{
	$server="";
}

if (!isset($playersearch) or $playersearch=="") {$searchaddstring="";$playersearch="%";}
else {$searchaddstring=" and ".$Playertable.".ubiname like '".$playersearch."'";}

if ($server=="")
{
	echo "<table border=0 cellspacing=0 width=".$swidth."><form name=\"rvsstats\" action=\"playerstats.php\"><tr><td align=left class=bigheader background=\"images/".$design[$dset]."_header.gif\">";
	ShowFlags("playerstats.php","&mode=".$mode."&server=".$server."&playersearch=".$playersearch."&nicksearch=".$nicksearch);

	echo $text_statstitle."</td><td align=right class=bigheader background=\"images/".$design[$dset]."_header.gif\"><img src=\"images/clear.gif\" width=10 height=10 name=\"back\"><a class=nav2 href=\"main.php\" onMouseOver=\"mi('back')\" onMouseOut=\"mo('back')\"><b>".$text_mainmenu."&nbsp;</a></td></tr><tr><td colspan=2><hr></td></tr></table>";

	echo "<table border=0 cellspacing=0 width=".$swidth."><tr><td align=center class=bigheader colspan=2 background=\"images/".$design[$dset]."_header.gif\">".$text_statschooseserver."</td></tr>";

	if (isset($servernameident))
	{
		foreach ($servernameident as $servers => $notused)
		{
			echo "<tr><td class=randende align=center><a class=nav href=\"playerstats.php?mode=teamsurvival&server=".$servers."\">".$servers."</a></td></tr>";
			}
		echo "<tr>";
		echo "<td class=randende align=center><a class=nav href=\"playerstats.php?mode=14&server=%\">".$text_all."</a></td>";
	}
	echo "<tr><td height=2></td></tr>";
	echo "<tr><td align=center class=bigheader colspan=2 background=\"images/".$design[$dset]."_header.gif\">".$text_statschooseladder."</td></tr>";
	if (isset($servernameident))
	{
		foreach ($servernameident as $servers => $notused)
		{
			echo "<tr><td class=randende align=center><a class=nav href=\"ladder.php?server=".$servers."\">".$servers."</a></td>";
		}
		echo "<tr><td class=randende align=center><a class=nav href=\"ladder.php?server=All\">".$text_all."</a></td></tr>";
	}
	echo "<tr><td height=2></td></tr>";
	echo "<tr><td align=center class=bigheader colspan=2 background=\"images/".$design[$dset]."_header.gif\">".$text_overallstats."</td></tr>";
	if (isset($servernameident))
	{
		foreach ($servernameident as $servers => $notused)
		{
			echo "<tr><td class=randende align=center><a class=nav href=\"serverstats.php?server=".$servers."&mode=teamsurvival\">".$servers."</a></td>";
		}
		echo "<tr><td class=randende align=center><a class=nav href=\"serverstats.php?server=%&mode=teamsurvival\">".$text_all."</a></td>";
	}
	echo "</table>";
	Copyrightextstats();
	echo "</center></form></body>";
	die;
}
echo "<table border=0 cellspacing=0 width=".$swidth."><form name=\"rvsstats\" action=\"playerstats.php\"><tr><td align=left class=bigheader background=\"images/".$design[$dset]."_header.gif\">";
ShowFlags("playerstats.php","&mode=".$mode."&server=".$server."&playersearch=".$playersearch."&nicksearch=".$nicksearch);

echo $text_statstitle."</td><td align=right align=left class=bigheader background=\"images/".$design[$dset]."_header.gif\"><img src=\"images/clear.gif\" width=10 height=10 name=\"back\"><a class=nav2 href=\"playerstats.php\" onMouseOver=\"mi('back')\" onMouseOut=\"mo('back')\"><b>".$text_back."&nbsp;</a></td></tr><tr><td colspan=2><hr></td></tr></table>";

if (isset($servernameident[$server]) and $server!="%")
{
	$serverident=$servernameident[$server];
}
else 
{
	$serverident="%";
}
echo "<table  border=0 cellspacing=0 width=".$swidth."><tr><td align=center colspan=2 class=bigheader background=\"images/".$design[$dset]."_header.gif\">".$text_statsgamemode."</td></tr>";
echo "<tr><td class=randende align=center  colspan=2>";
$i=0;
foreach ($Statstable as $modeofstats => $unused)
{
	$i++;
	if ($i==5){$i=0;echo "<br>";}
	echo "<a class=nav href=\"playerstats.php?mode=".$modeofstats."&server=".$server."&playersearch=".$playersearch."\">".$text_gamemode[$modeofstats]."</a>&nbsp;&nbsp;&nbsp;&nbsp;";
}
echo "</td></tr>";
echo "<tr><td class=randende colspan=2 align=center>";
echo "<a class=nav href=\"playerstats.php?mode=".$mode."&server=".$server."\">".$text_all." ".$text_player."</a>&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<a class=nav href=\"playerstats.php?mode=".$mode."&server=%&playersearch=".$playersearch."&nicksearch=".$nicksearch."\">".$text_all." Server</a>";
echo "</td></tr>";
echo "<tr><td class=randende colspan=2 align=center>";
if (isset($servernameident))
{
	$i=0;
	foreach ($servernameident as $servers => $idents)
	{
		$i++;
		if ($i==4){$i=1;echo "<br>";}
		echo "<a class=nav href=\"playerstats.php?mode=".$mode."&server=".$servers."&playersearch=".$playersearch."&nicksearch=".$nicksearch."\">".$servers."</a>&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	echo "</td></tr>";
	echo "<form><input type=hidden name=server value=\"".$server."\"><input type=hidden name=mode value=\"".$mode."\">";
	echo "<tr><td class=rand align=left>&nbsp;".$text_textsearchforubi."</td><td class=randende align=left>&nbsp;<input type=text class=textfield name=playersearch value=\"".$playersearch."\" size=30 maxlength=40 title=\"".$helptext_statsenterubiname."\">";
	echo "&nbsp;<input class=button type=submit name=Send value=\"Search\"></td></tr></form>";
	echo "<form><input type=hidden name=server value=\"".$server."\"><input type=hidden name=mode value=\"".$mode."\">";
	echo "<tr><td class=rand align=left>&nbsp;".$text_textsearchfornick."</td><td class=randende align=left>&nbsp;<input type=text class=textfield name=nicksearch value=\"".$nicksearch."\" size=30 maxlength=40 title=\"".$helptext_statsenternickname."\">";
	echo "&nbsp;<input class=button type=submit name=Send value=\"Search\"></td></tr></form>";
}
echo "<tr><td colspan=2><hr></td></tr></table>";

if ($nicksearch!="")
{
	$nicksearchencoded=nicksearchencode($nicksearch);
	echo "<table  border=0 cellspacing=0 width=".$swidth."><tr><td class=bigheader colspan=2 background=\"images/".$design[$dset]."_header.gif\">".$text_onserver.": ";
	if ($server=="%")
	{
		echo $text_all;
	}
	else
	{
		echo $server;
	}
	echo "</td><td align=right class=bigheader background=\"images/".$design[$dset]."_header.gif\">".$text_site.":".$site."&nbsp;</td></tr>";

	$searchnick=mysql_query ("SELECT ".$Nicktable.".nick as nick FROM ".$Nicktable." LEFT JOIN ".$Playertable." ON (".$Nicktable.".fromid = ".$Playertable.".id) WHERE ".$Nicktable.".nick like '".$nicksearchencoded."' and ".$Playertable.".ubiname <> '' and ".$Nicktable.".nick <>'' and ".$Playertable.".serverident like '".$serverident."' ORDER BY nick");
	$nickcount=mysql_num_rows($searchnick);
	$i=($site*$displaynickononesite)-$displaynickononesite;
	$sitecount=$nickcount/$displaynickononesite+0.99999999;

	echo "<tr><td class=randende colspan=3>".$text_site.": ";
	for ($si=1;$si<=$sitecount;$si++)
	{
		echo "<a class=nav href=\"playerstats.php?mode=".$mode."&server=".$server."&nicksearch=".$nicksearch."&site=".$si."\">-".$si."-</a> ";
	}
	echo "</td></tr>";
	echo "<tr height=2 colspan=3><td></td></tr>";
	echo "<tr><td class=header background=\"images/".$design[$dset]."_middle.gif\"><b>Nickname</td><td class=header background=\"images/".$design[$dset]."_middle.gif\"><b>Used by Ubiname</td><td class=header background=\"images/".$design[$dset]."_middle.gif\" align=right><b>".$text_onserver."&nbsp;</td></tr></td>";

	$searchnick=mysql_query ("SELECT ".$Playertable.".ubiname as ubiname , ".$Playertable.".serverident as serverident, ".$Nicktable.".nick as nick FROM ".$Nicktable." LEFT JOIN ".$Playertable." ON (".$Nicktable.".fromid = ".$Playertable.".id) WHERE ".$Nicktable.".nick like '".$nicksearchencoded."' and ".$Playertable.".ubiname <> '' and ".$Nicktable.".nick <>'' and ".$Playertable.".serverident like '".$serverident."' ORDER BY nick LIMIT ".$i.",".$displaynickononesite);
	$i=0;
	while ($searchednicks=mysql_fetch_array($searchnick, MYSQL_ASSOC))
	{
		echo "<tr><td class=rand>".htmlentities(decodenick($searchednicks['nick']))."</td>";
		echo "<td class=rand><img src=\"images/clear.gif\" width=10 height=10 name=\"s".$i."nick\"><a class=nav href=\"playerstats.php?mode=".$mode."&server=".$serveridentname[$searchednicks['serverident']]."&playersearch=".$searchednicks['ubiname']."\" onMouseOver=\"mi('s".$i."nick')\" onMouseOut=\"mo('s".$i."nick')\"><b>".$searchednicks['ubiname']."</b></a></td>";
		echo "<td class=randende align=right>".$serveridentname[$searchednicks['serverident']]."&nbsp;</td>";
		echo "</tr>";
		$i++;
	}
	echo "</table>";
	echo "<br><a class=nav href=\"playerstats.php\">".$text_statsmain."</a>";
	Copyrightextstats();
	echo "</form></center></body>";
	die;
}

if (!($server=="%"))
{
	if (!isset($servernameident[$server]))
	{
		die ("no Data for this Server!");
	}
	else
	{
		$serveridents[$server]=$servernameident[$server];
	}
}
else
{
	$serveridents=$servernameident;
}

foreach ($serveridents as $server => $serverident)
{
	if (isset($Statstable[$mode]))
{
$resubinames = mysql_query ("SELECT ".$Playertable.".id as id FROM ".$Playertable." INNER JOIN ".$Statstable[$mode]." ON (".$Statstable[$mode].".fromid = ".$Playertable.".id) WHERE ".$Playertable.".serverident='".$serverident."'".$searchaddstring." and ".$Playertable.".ubiname<>'' GROUP by ".$Playertable.".id");
$ubicounted=mysql_num_rows($resubinames);
echo "<table  border=0 cellspacing=0 width=".$swidth."><tr><td colspan=4 class=bigheader background=\"images/".$design[$dset]."_header.gif\">".$text_onserver.": ".$server."</td><td colspan=3 align=right class=bigheader background=\"images/".$design[$dset]."_header.gif\">".$text_gamemode[$mode]."&nbsp;</td></tr>";
echo "<tr><td class=randende colspan=7>".$text_site." (".$site.") : ";
$sitecount=$ubicounted/$displayubiononesite+0.99999999;
for ($c=1;$c<=$sitecount;$c++)
{
	echo "<a class=nav href=\"playerstats.php?mode=".$mode."&server=".$server."&playersearch=".$playersearch."&site=".$c."\">-".$c."- </a>";
}
echo "</td></tr><tr><td height=2></td></tr>";
echo "<tr><td class=header background=\"images/".$design[$dset]."_middle.gif\"><b>Ubiname</td><td colspan=6 class=header background=\"images/".$design[$dset]."_middle.gif\"><b>".$text_usednicks."</td></tr></td></tr><tr height=2><td></td></tr></table>";
$i=(($site-1)*$displaynickononesite);

$resubinames = mysql_query ("SELECT ".$Playertable.".id as id , ".$Playertable.".ubiname as ubiname FROM ".$Playertable." INNER JOIN ".$Statstable[$mode]." ON (".$Statstable[$mode].".fromid = ".$Playertable.".id) WHERE ".$Playertable.".serverident='".$serverident."'".$searchaddstring." and ".$Playertable.".ubiname<>'' GROUP by ".$Playertable.".id ORDER BY ubiname LIMIT ".$i.",".$displayubiononesite);

$i=0;
while ($ubinames=mysql_fetch_array($resubinames, MYSQL_ASSOC))
{
	$fromid=$ubinames['id'];
	$ubiandnick = "<table  border=0 cellspacing=0 width=".$swidth."><tr><td class=header>";
	$ubiandnick.= "<img src=\"images/clear.gif\" width=10 height=10 name=\"".$i."ubi\"><a class=nav href=\"playerstats.php?mode=".$mode."&server=".$server."&playersearch=".$ubinames['ubiname']."\" onMouseOver=\"mi('".$i."ubi')\" onMouseOut=\"mo('".$i."ubi')\">";
	$ubiandnick.= "<b>".$ubinames['ubiname']."</a></td><td colspan=6 class=header><b>";
	$i++;
	$resnick = mysql_query ("SELECT * FROM $Nicktable WHERE fromid='$fromid' and nick <>''");
	$j=0;
	while ($nicks=mysql_fetch_array($resnick, MYSQL_ASSOC))
	{
		if ($j++>0)
		{
			$ubiandnick.= ", ";
		}
		$ubiandnick.= htmlentities(decodenick($nicks['nick']));
	}
	$ubiandnick.= "</td></tr>";

	$teamstatsavailable=False;
	$killcount=0;
	$deathcount=0;
	$roundcount=0;
	$firedcount=0;
	$hitscount=0;

	if (strstr($playersearch,"%"))
	{
	$scoreview=False;
	$nostatsshow=False;
	$resstats = mysql_query ("SELECT sum(kills) as sumkills, sum(deaths) as sumdeaths ,sum(roundsplayed) as sumrounds,sum(fired) as sumfired,sum(hits) as sumhits FROM $Statstable[$mode] WHERE fromid='$fromid'");

	while ($stats=mysql_fetch_array($resstats, MYSQL_ASSOC))
	{
		echo $ubiandnick;
		$scoreview=True;
		$teamstatsavailable=True;
		echo "<tr><td class=rand>&nbsp;</td><td class=rand align=center width=\"10%\"><b>".$text_pdkills."</b></td><td class=rand align=center width=\"10%\"><b>".$text_pddeaths."</b></td><td class=rand align=center width=\"10%\"><b>Fired</b></td><td class=rand align=center width=\"10%\"><b>Hits</b></td><td class=rand align=center width=\"10%\"><b>".$text_pdacc."%</b></td><td class=randende align=center width=\"10%\"><b>".$text_rounds."</b></td></tr>";
		if ($stats['sumhits']==0){$hitfix=0.00001;}else{$hitfix=0;}
		$killcount=$stats['sumkills'];
		$deathcount=$stats['sumdeaths'];
		$roundcount=$stats['sumrounds'];
		$firedcount=$stats['sumfired'];
		$hitscount=$stats['sumhits'];
	}
}
else
{
	echo $ubiandnick;
	$scoreview=True;
	$nostatsshow=True;
	$resstats = mysql_query ("SELECT * FROM $Statstable[$mode] WHERE fromid='$fromid' ORDER BY map");
	while ($stats=mysql_fetch_array($resstats, MYSQL_ASSOC))
	{
		if (!$teamstatsavailable)
		{
			$teamstatsavailable=True;
			echo "<tr><td class=rand><b>".$text_map."</b></td><td class=rand align=center width=\"10%\"><b>".$text_pdkills."</b></td><td class=rand align=center width=\"10%\"><b>".$text_pddeaths."</b></td><td class=rand align=center width=\"10%\"><b>Fired</b></td><td class=rand align=center width=\"10%\"><b>Hits</b></td><td class=rand align=center width=\"10%\"><b>".$text_pdacc."%</b></td><td class=randende align=center width=\"10%\"><b>".$text_rounds."</b></td></tr>";
		}
		if ($stats['fired']==0 and $stats['hits']>0)
		{
			$stats['fired']=1;
		}
		if ($stats['hits']==0)
		{
			$hitfix=0.00001;}else{$hitfix=0;
		}
		echo "<tr><td class=rand>".$stats['map']."</td><td class=rand align=right>".$stats['kills']."&nbsp;</td><td class=rand align=right>".$stats['deaths']."&nbsp;</td><td class=rand align=right>".$stats['fired']."&nbsp;</td><td class=rand align=right>".$stats['hits']."&nbsp;</td><td align=right class=rand>".(int)(($stats['hits']/($stats['fired']+$hitfix))*100)."%&nbsp;</td><td class=randende align=right>".$stats['roundsplayed']."&nbsp;</td></tr>";

		$killcount=$killcount+$stats['kills'];
		$deathcount=$deathcount+$stats['deaths'];
		$roundcount=$roundcount+$stats['roundsplayed'];
		$firedcount=$firedcount+$stats['fired'];
		$hitscount=$hitscount+$stats['hits'];
	}
}

if (!$teamstatsavailable and $nostatsshow==True)
{
	echo "<tr><td align=center colspan=7 class=randende>".$text_nostatslogged['0'].$text_gamemode[$mode].$text_nostatslogged['1']."</td></tr><tr><height=2></td></tr></table>";
}
elseif ($scoreview==True)
{
	if ($firedcount==0)
	{
		$hitfix=0.00001;
	}
	else
	{
		$hitfix=0;
	}
	echo "<tr><td class=rand><b>Overall</b></td><td class=rand align=right><b>".$killcount."&nbsp;</b></td><td class=rand align=right><b>".$deathcount."&nbsp;</b></td><td class=rand align=right><b>".$firedcount."&nbsp;</b></td><td class=rand align=right><b>".$hitscount."&nbsp;</b></td><td align=right class=rand><b>".(int)(($hitscount/($firedcount+$hitfix))*100)."%&nbsp;</b></td><td class=randende align=right><b>".$roundcount."&nbsp;</b></td>";
	echo "<tr><td class=rand><b>Summary:</b></td><td class=rand colspan=4 width=\"32%\"><b>&nbsp;".$text_ubilkdratio.": ";
	if ($deathcount==0)
	{
		$deathcount=1;
	}
	echo sprintf("%01.2f",$killcount/$deathcount);
	$score=CalcScore($killcount,$deathcount,$hitscount,$firedcount+$hitfix,$roundcount);
	echo "</b></td><td class=randende colspan=2 width=\"25%\"><b>&nbsp;Score: ".$score."</b></td></tr>";
	echo "<tr><td height=2></td></tr></table>";
}
}

}
echo "<br>";
} // end foreach

echo "<a class=nav href=\"playerstats.php\">".$text_back."</a>";

?>
<?=Copyrightextstats()?></form></center></body>
