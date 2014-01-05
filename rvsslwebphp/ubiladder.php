<?php
// Script (C)opyright 2003 by =TSAF=Muschel
// Released under GNU GENERAL PUBLIC LICENSE
// www.tsaf.de , muschel@tsaf.de
error_reporting(2047);
$artoverall="Overall";
$text_enterubiname="Enter a Ubiname:";
foreach ($_GET as $key => $value){${$key}=$value;}
require("config.inc.php");
if (!isset($mode)){$mode="0";}
if (!isset($ubi)){$ubi="";}
ConnectTheDBandGetDefaults();
require('language/'.$customlanguage.'.inc.php');
$modetext['0']=$artoverall;
$modetext['1']=$text_gamemode['teamsurvival'];
$modetext['2']=$text_gamemode['survival'];
$modetext['3']=$text_gamemode['bomb'];
$modetext['4']=$text_gamemode['hostage'];
$modetext['5']=$text_gamemode['pilot'];
?>
<html>
<title>UBI-Ladder</title>
<LINK rel='stylesheet' HREF="<?=$css?>" TYPE='text/css'>
<body class=body>
<script language="javascript">
<!--
if (document.images) { on = new Image(); on.src = "images/indicator.gif"; off = new Image(); off.src ="images/clear.gif"; }
function mi(n) { if (document.images) {document[n].src = eval("on.src");}}
function mo(n) { if (document.images) {document[n].src = eval("off.src");}}
// -->
</script>
<table align="left" valign="top" border="0" cellpadding="0" cellspacing="0" width="350">
<td>
<table border=0 cellpadding=0 cellspacing=0 width=350 align=center valign=top>
<tr><td width="100%" class="tabfarbe-3"><table border=0 cellspacing=1 width="100%">
<tr><td width="100%" class=oben colspan=2 align=center background="images/<?=$design[$dset]?>_header.gif">
<?=$text_playerdetails?> (<?=$modetext[$mode]?>)</td></tr>
<tr><td width="100%" class=results colspan=2 align=center>
<?=ShowFlags("ubiladder.php","&ubi=".$ubi."&mode=".$mode)?></td></tr>
<?php
if ($ubi!="")
{
$location="http://ladder.ubi.com/index.asp?gamename=RAVENSHIELD&ladderid=0&modeid=".$mode."&lan=en&FILTERCHANGE=1&SELECT_ALIAS=EXACT&INPUT_ALIAS=".$ubi."&SELECT_COUNTRY=&SELECT_RATING=EQUAL&INPUT_RATING=&SELECT_KILL=EQUAL&INPUT_KILL=&SELECT_DEATH=EQUAL&INPUT_DEATH=&SELECT_COMBAT=EQUAL&INPUT_COMBAT=&x=0&y=0";
$url=fopen ($location,"rb");
if (!$url)
{echo "<tr><td colspan=2 class=results width=\"100%\" align=center><b>".$text_ladderoff."</b></td></tr>";
echo "<tr><td colspan=2 class=results width=\"100%\" align=center><b><a class=nav href=\"".$location."\" target=\"_blank\">".$text_laddtry."</a></b></td></tr>";}
else
{
$i=0;
while (!feof($url) )
{$row[$i++]=fgets($url,500);}
fclose($url);
$count=0;
$maintenance=0;
for ($j=2;$j<20;$j++)
{
if (strstr($row[$j],"aintenance"))
{
$maintenance=1;
echo "<tr><td colspan=2 class=results width=\"100%\" align=center><b>Ubiladder in Maintenance!.</b></td>";
}
if (strstr($row[$j],"ladder</TITLE>"))
{
$time=$row[$j];
$len=strlen($time);
$time=strstr ($time,"(");
$pos=strpos ($time,")");
$time=substr($time,0,$pos+1);
}
}
if ($maintenance==0)
{
echo "<tr><td colspan=2 class=results width=\"100%\" align=center><b>$ubi</b></td>";
$line=0;
for ($i=510;$i<527;$i++){
if (strstr($row[$i],"results\">"))
{
$line++;
if ($line==2){echo "<tr><td class=results width=150> $text_ubilglobal:</td>$row[$i]</tr>";}
if ($line==5){echo "<tr><td class=results width=150> $text_ubilskill:</td>$row[$i]</tr>";}
if ($line==6){echo "<tr><td class=results width=150> $text_ubilkills:</td>$row[$i]</tr>";}
if ($line==7){echo "<tr><td class=results width=150> $text_ubildeaths:</td>$row[$i]</tr>";}
if ($line==8){echo "<tr><td class=results width=150> $text_ubilkdratio:</td>$row[$i]</tr>";}
if ($line==9){echo "<tr><td class=results width=150> $text_ubilroundsplayed:</td>$row[$i]</tr>";}
if ($line==10){echo "<tr><td class=results width=150> $text_ubiltimeplayed:</td>$row[$i]</tr>";}
}
if (ereg("No Result",$row[$i]))
{echo "<tr><td colspan=2 class=results width=\"100%\" align=center><b>$text_notranked</b></td>";}
}
echo "<tr><td colspan=2 class=results width=\"100%\" align=center><b>$time</b></td>";
?>
</table></table><br>
<table border=0 cellpadding=0 cellspacing=0 width=160 align=center>
<tr><td width="100%" class="tabfarbe-3"><table border="0" cellspacing=1 width="100%">
<tr><td width="100%" class="oben" align="center" colspan="1" background="images/<?=$design[$dset]?>_header.gif"><?=$text_changegamemode?></td></tr>
<tr><td class=results><img src="images/clear.gif" width=10 height=10 name="m0" align=center><a class=nav href="ubiladder.php?ubi=<?=$ubi?>&mode=0" onMouseOver="mi('m0')" onMouseOut="mo('m0')"><?=$modetext[0]?></td></tr>
<tr><td class=results><img src="images/clear.gif" width=10 height=10 name="m1" align=center><a class=nav href="ubiladder.php?ubi=<?=$ubi?>&mode=1" onMouseOver="mi('m1')" onMouseOut="mo('m1')"><?=$modetext[1]?></td></tr>
<tr><td class=results><img src="images/clear.gif" width=10 height=10 name="m2" align=center><a class=nav href="ubiladder.php?ubi=<?=$ubi?>&mode=2" onMouseOver="mi('m2')" onMouseOut="mo('m2')"><?=$modetext[2]?></td></tr>
<tr><td class=results><img src="images/clear.gif" width=10 height=10 name="m3" align=center><a class=nav href="ubiladder.php?ubi=<?=$ubi?>&mode=3" onMouseOver="mi('m3')" onMouseOut="mo('m3')"><?=$modetext[3]?></td></tr>
<tr><td class=results><img src="images/clear.gif" width=10 height=10 name="m4" align=center><a class=nav href="ubiladder.php?ubi=<?=$ubi?>&mode=4" onMouseOver="mi('m4')" onMouseOut="mo('m4')"><?=$modetext[4]?></td></tr>
<tr><td class=results><img src="images/clear.gif" width=10 height=10 name="m5" align=center><a class=nav href="ubiladder.php?ubi=<?=$ubi?>&mode=5" onMouseOver="mi('m5')" onMouseOut="mo('m5')"><?=$modetext[5]?></td></tr>
</table></table>
<?php
}
}
}
else
{
echo "<form name=\"rvsubiladder\" action=\"ubiladder.php\"><tr><td colspan=2 class=results align=center><br>$text_enterubiname<br><br>";
echo "<input type=text class=textfield name=ubi size=30 maxlength=30 title=\"Ubiname\"><br>&nbsp;</td></td></form>";

}
?>
</table></table></td>
</tr></table></body></html>

