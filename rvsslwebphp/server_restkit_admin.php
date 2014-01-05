<?php
// Script (C)opyright 2003 by =TSAF=Muschel
// Released under GNU GENERAL PUBLIC LICENSE
// www.tsaf.de , muschel@tsaf.de
foreach ($_GET as $key => $value){${$key}=$value;}
error_reporting(2047);
if (!isset($HTTP_COOKIE_VARS["RVSsessionip"]) or !isset($HTTP_COOKIE_VARS["RVSsessionport"])){die ("cookie for ip+port error!");}
$ip=$HTTP_COOKIE_VARS["RVSsessionip"];
$port=$HTTP_COOKIE_VARS["RVSsessionport"];
if (!$ip) {echo "No IP error!";}
if (!$port) {echo "No Beaconport error!";}
$cookiename="RVS".$port.crc32($ip);
$pw=base64_decode($HTTP_COOKIE_VARS[$cookiename]);
require("config.inc.php");
ConnectTheDBandGetDefaults();
require('language/'.$customlanguage.'.inc.php');
$sendit=False;
$respond="";
if (isset($Submit)){

switch ($Submit)
{
case "changerestkit";
$packetstring="ADMIN ".$pw." CHANGERESTRICTION ".$restdata;
$sendit=True;
break;
}

if ($sendit==True and isset($packetstring))
{
$fp=fsockopen("udp://".$ip,$port,$error,$error2);
if (is_resource($fp)) {
fwrite($fp,$packetstring,strlen($packetstring));
if ($socket_blocking_use==True) {socket_set_blocking($fp,True);}
$respond=fread($fp,1);
$bytes_left=socket_get_status($fp);
if ($bytes_left['unread_bytes']>0){$respond.=fread($fp,$bytes_left['unread_bytes']);}
fclose($fp);
}
}
}
require("header.php");
?>
<script language="javascript">
<!--
if (document.images) { on = new Image(); on.src = "images/indicator.gif"; off = new Image(); off.src ="images/clear.gif"; }
function mi(n) { if (document.images) {document[n].src = eval("on.src");}}
function mo(n) { if (document.images) {document[n].src = eval("off.src");}}
// -->
</script>
<link rel="stylesheet" type="text/css" href="<?=$css?>">
<body>
<center>
<table border=0 cellspacing=0 width="<?=$awidth?>"><form name="listadm" action="server_restkit_admin.php">
<tr><td align=left class=oben background="images/<?=$design[$dset]?>_header.gif">
<?=ShowFlags("server_restkit_admin.php","")?>Admin Server-Restriction-Kits / <?=$ip?>:<?=$port?>[SrBnPt]</td>
<td align=right class=oben background="images/<?=$design[$dset]?>_header.gif"><img src="images/clear.gif" width=10 height=10 name="back"><a class=nav2 href="server_admin.php" onMouseOver="mi('back')" onMouseOut="mo('back')"><?=$text_back?>&nbsp;</a></td></tr>
<tr><td colspan=2><hr></td></tr></table>
<table  border=0 cellspacing=0 class=frame width="<?=$swidth?>">
<tr><td background="images/<?=$design[$dset]?>_header.gif" class=bigheader align=center>FeedbackBox</td>
</tr><tr>
<td class=randende align=center><?=$respond?></td>
</tr></table>
<table border=0 cellspacing=0 width="<?=$awidth?>">
<tr>
<td align=center class=oben background="images/<?=$design[$dset]?>_header.gif" width="50%"><?=$text_restkit_tabletitleadd?> (Click to add)</td>
<td align=center class=oben background="images/<?=$design[$dset]?>_header.gif"><?=$text_restkit_tabletitle?> (Click to remove)</td></tr>
</table><table border=0 cellspacing=0 height=5 width=100><tr><td></td></tr></table>
<?php


$fp=fsockopen("udp://".$ip, $port,$error,$error2);
if (is_resource($fp))
{
fwrite($fp,"RESTKIT",7);
socket_set_blocking($fp,True);
$serverrepeat=fread($fp,1);
$bytes_left=socket_get_status($fp);
if ($bytes_left['unread_bytes']>0){$serverrepeat.=fread($fp,$bytes_left['unread_bytes']); }

 $numbercheck=explode (" ¶", $serverrepeat);
 foreach ($numbercheck as $item)
  {$numbercheckarray[substr($item,0,2)]=substr($item,3,strlen($item)-3);}

 for ($i=1;$i<$numbercheckarray['NP'];$i++)
 {
 fwrite($fp,"RESTKIT",7);
 $serverrepeat.=fread($fp,1);
 $bytes_left=socket_get_status($fp);
 if ($bytes_left['unread_bytes']>0){$serverrepeat.=fread($fp,$bytes_left['unread_bytes'])." ¶";}
 }

$fp2=fsockopen("udp://".$ip, $port,$error,$error2);
if (is_resource($fp2))
{
fwrite($fp2,"AVAILABLEKIT",12);
socket_set_blocking($fp2,True);
$serverrepeatadd=fread($fp2,1);
$bytes_left=socket_get_status($fp2);
if ($bytes_left['unread_bytes']>0){$serverrepeatadd.=fread($fp2, $bytes_left['unread_bytes']);$failed=False; }

 $numbercheckadd=explode(" ¶",$serverrepeatadd);
 foreach ($numbercheckadd as $item){$numbercheckarrayadd[substr($item,0,2)]=substr($item,3,strlen($item)-3);}
 if (isset($numbercheckarray['NP']))
 {
 for ($i=1;$i<$numbercheckarrayadd['NP'];$i++)
 {
 $failed=True;
 fwrite($fp2,"AVAILABLEKIT",12);
 $serverrepeatadd.=fread($fp2,1);
 $bytes_left=socket_get_status($fp2);
 if ($bytes_left['unread_bytes']>0){$serverrepeatadd.=fread($fp2,$bytes_left['unread_bytes'])." ¶";$failed=False;}
 }
 }
 else {$failed=True;}
fclose($fp2);
}
fclose($fp);

if (strstr($serverrepeat,"/")){
if (strstr($serverrepeatadd,"/")){
$restkittemp=explode (" ¶",$serverrepeatadd);
 foreach ($restkittemp as $item)
 {
  $PosTmp=strpos($item,"/");
  $marker=substr($item,0,$PosTmp);

  if (isset($restkitarray[$marker]))
  {$restkitarray[$marker].=substr($item,$PosTmp,strlen($item)-$PosTmp);}
  else
  {$restkitarray[$marker]=substr($item,$PosTmp,strlen($item)-$PosTmp);}
 }
 for ($i=0;$i<10;$i++){
  echo "<table border=0 cellspacing=0 width=\"".$swidth."\"><tr><td colspan=2 class=header background=\"images/".$design[$dset]."_middle.gif\" align=center><b>".$kittype[$i]."</td></tr>";
  $tempkit=explode("/",$restkitarray["A".$i]);
  $allrest="";
  $allrested="";
  $hitx=0;
  foreach ($tempkit as $show){
   if ($show and $show<>"R6Description.R6DescPistol92FS" )
   {
    if (strstr($serverrepeat,$i."/".$show)==False)
     {
     if ($weapons[$show])
     {
     echo "<tr><td class=rand align=center width=\"50%\"><b><img src=\"images/clear.gif\" width=10 height=10 name=\"rest1".$hitx."1\">";
     echo "<a class=nav href=\"server_restkit_admin.php?Submit=changerestkit&restdata=".$i."/".$show."/1\" onMouseOver=\"mi('rest1".$hitx."1')\" onMouseOut=\"mo('rest1".$hitx."1')\">".$weapons[$show]."</a></td><td class=randende>&nbsp;";
     }
     else
     {
     echo "<tr><td class=rand align=center width=\"50%\"><b><img src=\"images/clear.gif\" width=10 height=10 name=\"rest1".$hitx."2\">";
     echo "<a class=nav href=\"server_restkit_admin.php?Submit=changerestkit&restdata=".$i."/".$show."/1\" onMouseOver=\"mi('rest1".$hitx."2')\" onMouseOut=\"mo('rest1".$hitx."2')\">".$show."</a></td><td class=randende>&nbsp;";
     }
     $allrest.= $i."/".$show."/1 ";$hitx++;
    }
    else
    {if ($weapons[$show])
     {
     echo "<tr><td class=rand>&nbsp;</td><td class=randende align=center width=\"50%\"><b><img src=\"images/clear.gif\" width=10 height=10 name=\"rest1".$hitx."1\">";
     echo "<a class=nav href=\"server_restkit_admin.php?Submit=changerestkit&restdata=".$i."/".$show."/0\" onMouseOver=\"mi('rest1".$hitx."1')\" onMouseOut=\"mo('rest1".$hitx."1')\">".$weapons[$show]."</a>";
     }
     else
     {
     echo "<tr><td class=rand>&nbsp;</td><td class=randende align=center width=\"50%\"><b><img src=\"images/clear.gif\" width=10 height=10 name=\"rest1".$hitx."2\">";
     echo "<a class=nav href=\"server_restkit_admin.php?Submit=changerestkit&restdata=".$i."/".$show."/0\" onMouseOver=\"mi('rest1".$hitx."2')\" onMouseOut=\"mo('rest1".$hitx."2')\">".$show."</a>";
     }
    $allrested.= $i."/".$show."/0 ";$hitx++;
    }
   }
    echo "</td></tr>";
  }
  echo "<tr><td class=rand align=center>";
  if ($allrest)  {echo "<b><img src=\"images/clear.gif\" width=10 height=10 name=\"resta".$hitx."1\"><a class=nav href=\"server_restkit_admin.php?Submit=changerestkit&restdata=".$allrest."\" onMouseOver=\"mi('resta".$hitx."1')\" onMouseOut=\"mo('resta".$hitx."1')\">".$text_all."</a>";}
  else
  {echo "&nbsp;";}
  echo "</td><td class=randende align=center>";
  $hitx++;
  if ($allrested){echo "<b><img src=\"images/clear.gif\" width=10 height=10 name=\"resta".$hitx."2\"><a class=nav href=\"server_restkit_admin.php?Submit=changerestkit&restdata=".$allrested."\" onMouseOver=\"mi('resta".$hitx."2')\" onMouseOut=\"mo('resta".$hitx."2')\">".$text_all."</a>";}
  else {echo "&nbsp;";}
  $hitx++;
 echo "</td></tr></table><table border=0 cellspacing=0 height=3 width=100><tr><td></td></tr></table>";
 }
echo "</table>";
}
else {$failed=True;}
}
else {$failed=True;}
}
else {$failed=True;}
if ($failed==True)
{echo "<table border=0 cellspacing=0 width=\"".$swidth."\"><tr><td class=header background=\"images/".$design[$dset]."_middle.gif\" align=center><b>".$text_restkit_nodata."</td></tr></table>";
echo "<br><br><br><a class=nav href=\"javascript:location.reload()\">Refresh</a><br><br>";}
?>
<br><font class="normal"><a class=nav href="server_admin.php"><?=$text_back?></a>
<?=Copyrightext()?></center></body></html>
