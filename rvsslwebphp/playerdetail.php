<?php
// Script (C)opyright 2003 by =TSAF=Muschel
// Released under GNU GENERAL PUBLIC LICENSE
// www.tsaf.de , muschel@tsaf.de
error_reporting(2047);
foreach ($_GET as $key => $value){${$key}=$value;}
if (!isset($nick) or !isset($Ubi) or !isset($PWpn) or !isset($SWpn) or !isset($SWpn) or !isset($SWpnG) or !isset($Hits) or !isset($Fired) or !isset($Kills)  or !isset($Deaths)  or !isset($Acc)) {die ("missing value in url error!");}
require("config.inc.php");
ConnectTheDBandGetDefaults();
require('language/'.$customlanguage.'.inc.php');
if(!$PWpn)
{
	$PWpn="non";
}
if(!$SWpn)
{
	$SWpn="non";
}
if(!$PWpnG)
{
	$PWpnG="non";
}
if(!$SWpnG)
{
	$SWpnG="non";
}
if($Acc>100)
{
	$Acc=100;
}
?>
<html>
<title><?=$text_pd?></title>
<LINK rel='stylesheet' HREF="<?=$css?>" TYPE='text/css'>
<body class=body>
<table border="0" align="center" cellspacing="0" width="268">
<tr>
<td colspan=2 align=center class=bigheader background="images/<?=$design[$dset]?>_header.gif">
<b><?=$text_pd?></b></td>
</tr><tr>
<tr>
<td colspan=2 align=center class=randende>
<?=ShowFlags("playerdetail.php","&nick=".$nick."&Ubi=".$Ubi."&PWpn=".$PWpn."&SWpn=".$SWpn."&PWpnG=".$PWpnG."&SWpnG=".$SWpnG."&Hits=".$Hits."&Fired=".$Fired."&Kills=".$Kills."&Deaths=".$Deaths."&Acc=".$Acc)?>
</td></tr>
<td class=rand align=left width="20%">&nbsp;<?=$text_pdnick?>:</td>
<td class=randende align=left>&nbsp;<?=base64_decode($nick)?></td>
</tr><tr>
<td class=rand align=left>&nbsp;UBI:</td>
<td class=randende align=left>&nbsp;<?=base64_decode($Ubi)?></td>
</tr><tr>
<td class=rand align=left>&nbsp;<?=$text_pdkills?>:</td>
<td class=randende align=left>&nbsp;<img width="<?=$Kills?>%" height="8" border="0" src="images/hitgreen.gif">&nbsp;<?=$Kills?></td>
</tr><tr>
<td class=rand align=left>&nbsp;<?=$text_pddeaths?>:</td>
<td class=randende align=left>&nbsp;<img width="<?=$Deaths?>%" height="8" border="0" src="images/hitred.gif">&nbsp;<?=$Deaths?></td>
</tr></tr>
<tr>
<td colspan=2   align="center" class=randende ><?=$Fired?> <?=$text_pdbfired?><br><?=$Hits?> <?=$text_pdbhits?>
<br><?=$text_pdacc?> <?=$Acc?>%<br>
<img width="<?=$Acc-1?>%" height="8" border="0" src="images/hitgreen.gif"><img width="<?=99-$Acc?>%" height="8" border="0" src="images/hitred.gif"></td>
</tr><tr>
<td class=randende align=left valign=top colspan=2 height=127 background="images/wpnback.gif">
<img width="30" height="60" border="0" src="images/clear.gif"><img border ="0" src="weaponicons/<?=$PWpn?>.gif" title="<?=$PWpn?>"><img width="20" height="1" border="0" src="images/clear.gif"><img border ="0" src="gadgeticons/<?=$PWpnG?>.gif" title="<?=$PWpnG?>"><br>
<img width="70" height="50" border="0" src="images/clear.gif"><img border ="0" src="weaponicons/<?=$SWpn?>.gif" title="<?=$SWpn?>"> <img width="45" height="1" border="0" src="images/clear.gif"><img border ="0" src="gadgeticons/<?=$SWpnG?>.gif" title="<?=$SWpnG?>">
</td></tr></table></body></html>
