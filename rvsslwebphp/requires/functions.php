<?php


function BuildGameModeTranslateArray()
{
global $GameModeTranslate,$dbtable5;

$gamemodetable=$dbtable5."GameMode";
$gamemodebeacontable=$dbtable5."GameModeInBeacon";

$searchstr="SELECT * FROM ".$gamemodetable;
$searchresult=mysql_query($searchstr);
 while ($searchgamemodes=mysql_fetch_array($searchresult, MYSQL_ASSOC))
 {
 $availgamemodesids[$searchgamemodes['text']]=$searchgamemodes['id'];
 }
  foreach ($availgamemodesids as $key => $value)
  {
	$searchbeaconstr="SELECT * FROM ".$gamemodebeacontable." WHERE fromgamemodeid='".$value."'";
	$searchbeaconresult=mysql_query($searchbeaconstr);
	while ($searchbeacontexts=mysql_fetch_array($searchbeaconresult, MYSQL_ASSOC))
	{
    $GameModeTranslate[$searchbeacontexts['beacontext']]=$key;
	}
  }
}
function TranslateBeaconGameModeToText($in)
{
global $text_gamemode,$GameModeTranslate;
if (isset($GameModeTranslate[$in]))
{
if (isset($text_gamemode[$GameModeTranslate[$in]]))
{
$out=$text_gamemode[$GameModeTranslate[$in]];
}
else
{
$out="unknown";
}
}
else
{
$out="unknown";
}
return $out;
}
function BuildStatsTablesArray()
{
global $Statstable,$dbtable4,$dbtable5;

$search="SELECT * FROM ".$dbtable5."GameMode";
$searchstatstableresult=mysql_query ($search);
 while ($searchstatstable=mysql_fetch_array($searchstatstableresult, MYSQL_ASSOC))
 {
 if ($searchstatstable['statstablename']!="")
  {
  $Statstable[$searchstatstable['text']]=$dbtable4.$searchstatstable['statstablename'];
  }
 }
 ksort($Statstable);
}
function BuildGameMode()
{
global $GameModeArray,$dbtable5;

$search="SELECT * FROM ".$dbtable5."GameMode";
$searchgamemode=mysql_query ($search);
 while ($searchedgamemode=mysql_fetch_array($searchgamemode, MYSQL_ASSOC))
 {
  $GameModeArray[$searchedgamemode['text']]=$searchedgamemode['id'];
 }
}

function Showflags($site, $add)
{
global $language;

echo "&nbsp;";
foreach ($language as $item )
{
echo "<a href=\"".$site."?customlanguage=".$item.$add."\" title=\"".$item."\"><img border=0 src=\"language/flags/".$item.".gif\"></a>&nbsp;";
}

}

function Copyrightext()
{
echo "<center><font class=normal><br><br>v1.20as PHP-Script (C) 2003,2004 by <a class=nav href=\"http://www.tsaf.de\" target=\"_blank\">=TSAF=Muschel</a> and released under <a class=nav target=\"_blank\" href=\"gpl.txt\">GNU-GPL</a>";
echo "<br>Powered by N4Admin from <a class=nav href=\"http://www.koalaclaw.com\" target=\"_blank\">Neo4E656F</a>";
echo "<br>Updated for Athena Sword by <a class=nav href=\"http://ravenshield.theplatoon.com\" target=\"_blank\">Munkey</a>, <a class=nav href=\"http://www.smakclan.com\" target=\"_blank\">Wizard</a> and <a class=nav href=\"http://www.koalaclaw.com\" target=\"_blank\">Neo4E656F</a></font><br><br><br></center>";
}

function Copyrightextstats()
{
echo "<center><font class=normal><br><br>v1.20as PHP-Script (C) 2003,2004 by <a class=nav href=\"http://www.tsaf.de\" target=\"_blank\">=TSAF=Muschel</a> and released under <a class=nav target=\"_blank\" href=\"gpl.txt\">GNU-GPL</a>";
echo "<br>Powered by N4URLPost - Copyright 2003,2004 =TSAF=Muschel and Neo4E656F<br></font>";
}

function Copyrightextmain()
{
echo "<center><font class=normal><br><br>v1.20as PHP-Script (C) 2003,2004 by <a class=nav href=\"http://www.tsaf.de\" target=\"_blank\">=TSAF=Muschel</a> and released under <a class=nav target=\"_blank\" href=\"gpl.txt\">GNU-GPL</a>";
echo "<br>Powered by N4Admin from <a class=nav href=\"http://www.koalaclaw.com\" target=\"_blank\">Neo4E656F</a> and N4URLPost";
echo "<br>Updated for Athena Sword by <a class=nav href=\"http://ravenshield.theplatoon.com\" target=\"_blank\">Munkey</a>, <a class=nav href=\"http://www.smakclan.com\" target=\"_blank\">Wizard</a> and <a class=nav href=\"http://www.koalaclaw.com\" target=\"_blank\">Neo4E656F</a></font><br><br><br></center>";
}

function ConnectTheDBandGetDefaults()
{
global $dset,$css,$design,$dbHost,$dbUser,$dbPass,$db,$dbDatabase,$dbtable2,$language,$HTTP_COOKIE_VARS,$customlanguage;

$db = mysql_connect($dbHost,$dbUser,$dbPass) or die ("<CENTER>Connect-Error to MySQL! Check $dbHost, $dbUser and $dbPass in config.inc.php!");
mysql_select_db($dbDatabase,$db) or die ("<CENTER>Connect-Error to Database! Check $dbDatabase in config.inc.php!");
$res=mysql_query("SELECT * FROM ".$dbtable2." WHERE id='1'");
$dbrow=mysql_fetch_array($res);
$lset=$dbrow['language'];
$dset=$dbrow['css'];
if (isset($customlanguage)){setcookie ("RVScustomlanguage",$customlanguage);}
else {
if (isset($HTTP_COOKIE_VARS["RVScustomlanguage"])){$customlanguage=$HTTP_COOKIE_VARS["RVScustomlanguage"];}
}
if (!isset($customlanguage)) {$customlanguage=$language[$lset];}
$css="css/".$design[$dset]."_css.css";
}

function CalcScore ($killcount,$deathcount,$hitscount,$firedcount,$roundcount)
{
$ratio=$killcount/$deathcount;
$acc=$hitscount/$firedcount;
$roundsadds=$roundcount/500;
if ($roundcount<10){$lowroundpen=0.1;}
elseif ($roundcount<20){$lowroundpen=0.2;}
elseif ($roundcount<30){$lowroundpen=0.4;}
elseif ($roundcount<50){$lowroundpen=0.6;}
elseif ($roundcount<100){$lowroundpen=0.8;}
 else {$lowroundpen=1;}
$score=(int)(((($ratio*1000*$roundsadds))+($acc*50*$roundsadds)+($ratio*1000)+($acc*40)+($roundcount))*$lowroundpen);
return $score;
}

function DefDiffLevels()
{
global $diff;
$diff['1']="Rekrut";
$diff['2']="Veteran";
$diff['3']="Elite";
}
function BuildLadderTablesArray()
{
global $Laddertable,$dbtable5,$dbtable6;

$search="SELECT * FROM ".$dbtable5."GameMode";
$searchladdertableresult=mysql_query ($search);
 while ($searchladdertable=mysql_fetch_array($searchladdertableresult, MYSQL_ASSOC))
 {
 if ($searchladdertable['laddertablename']!="")
  {
  $Laddertable[$searchladdertable['text']]=$dbtable6.$searchladdertable['laddertablename'];
  }
 }
}
function BuildPlayerAndNicksTables()
{
global $Playertable,$Nicktable,$dbtable4;

$Playertable=$dbtable4."Player";
$Nicktable=$dbtable4."Nicks";
}
function BuildServerIdentNameArrays()
{
global $servernameident,$serveridentname,$dbtable4;

$search="SELECT * FROM ".$dbtable4."ServerIdentsNames";
$searchservernameidentresult=mysql_query ($search);
 while ($searchservernameidententry=mysql_fetch_array($searchservernameidentresult, MYSQL_ASSOC))
 {
  $servernameident[$searchservernameidententry['servername']]=$searchservernameidententry['serverident'];
  $serveridentname[$searchservernameidententry['serverident']]=$searchservernameidententry['servername'];
 }
}






?>
