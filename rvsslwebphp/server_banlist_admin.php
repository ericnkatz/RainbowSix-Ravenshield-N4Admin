<?php
// Script (C)opyright 2003 by =TSAF=Muschel
// Released under GNU GENERAL PUBLIC LICENSE
// www.tsaf.de , muschel@tsaf.de
foreach ($_GET as $key => $value){${$key}=$value;}
error_reporting(2047);
if (!isset($HTTP_COOKIE_VARS["RVSsessionip"]) or !isset($HTTP_COOKIE_VARS["RVSsessionport"])){die ("cookie for ip+port error!");}
$ip=$HTTP_COOKIE_VARS["RVSsessionip"];
$port=$HTTP_COOKIE_VARS["RVSsessionport"];
$cookiename="RVS".$port.crc32($ip);
if (!isset($HTTP_COOKIE_VARS[$cookiename])){die("error, no pw for this server in cookie!");}
$pw=base64_decode($HTTP_COOKIE_VARS[$cookiename]);
require("config.inc.php");
ConnectTheDBandGetDefaults();
require('language/'.$customlanguage.'.inc.php');

if (isset($delban)){$send="ADMIN ".$pw." DELBAN ".$delban;}
if (isset($addban)){$send="ADMIN ".$pw." ADDBAN ".$addban;}
if (!isset($sendit)){$sendit="nosend";}
$wassend=False;
$failed=True;
$mess="";
if (!isset($Submit)){$Submit="";}

if ($sendit=="send")
{
$fpc=fsockopen("udp://".$ip,$port,$error,$error2);
if (is_resource($fpc))
{
socket_set_timeout($fpc,$socket_timeout);
fwrite($fpc,$send,strlen($send));
if ($socket_blocking_use==True) {socket_set_blocking($fpc,True);}
$serverrepeat=fread($fpc,1);
$bytes_left=socket_get_status($fpc);
if ($bytes_left['unread_bytes']>0){$serverrepeat.=fread($fpc,$bytes_left['unread_bytes']); }
$mess=$serverrepeat;
unset($delban);
unset($addban);
$wassend=True;
}
}
if (isset($delban))
{
$mess='<a class=nav href="server_banlist_admin.php?delban='.$delban.'&sendit=send">>>> Confirm remove Banned-ID: ('.$delban.') <<<</a>';
}
elseif (isset($tobanid))
{
if (strlen($tobanid)<>32)
{$mess="BanID is not 32 Chars long!";}
elseif (ctype_alnum($tobanid)<>True)
{$mess="BanID is not alphanumeric!";}
else
{$mess='<a class=nav href="server_banlist_admin.php?addban='.$tobanid.'&sendit=send">>>> Confirm add Ban-ID ('.$tobanid.') <<<</a>';}
}

$fp=fsockopen("udp://".$ip,$port,$error,$error2);
if (is_resource($fp))
{
socket_set_timeout($fp,$socket_timeout);
fwrite($fp,"BANLIST",7);
if ($socket_blocking_use==True) {socket_set_blocking($fp,True);}
$serverrepeat=fread($fp,1);
$bytes_left=socket_get_status($fp);
if ($bytes_left['unread_bytes']>0){$serverrepeat.=fread($fp,$bytes_left['unread_bytes']);$failed=False;}


 $numbercheck=explode(" ¶",$serverrepeat);
 foreach ($numbercheck as $item)
  {$numbercheckarray[substr($item,0,2)]=substr($item,3,strlen($item)-3);}
 if (isset($numbercheckarray['NP']))
 {

 for ($i=1;$i<$numbercheckarray['NP'];$i++)
 {
 $failed=True;
 fwrite($fp,"BANLIST",7);
 $serverrepeat.=fread($fp,1);
 $bytes_left=socket_get_status($fp);
 if ($bytes_left['unread_bytes']>0){$serverrepeat.=fread($fp,$bytes_left['unread_bytes']);$failed=False;}
 }

 }
 fclose($fp);
}

if ($failed==False)
{
$dataarray=explode(" ¶",$serverrepeat);

if ($wassend==True) {fclose($fpc);}
foreach ($dataarray as $item)
{
if (substr($item,0,2)=="BL")
{
$spchr=strpos($item," ");
$ausgabe[substr($item,2,$spchr-2)]=substr($item,$spchr,strlen($item)-$spchr);
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
<table border=0 cellspacing=0 width="<?=$awidth?>"><form name="listadm" action="server_banlist_admin.php">
<tr><td align=left class=oben background="images/<?=$design[$dset]?>_header.gif">
<?=ShowFlags("server_banlist_admin.php","")?>Admin Server-BanList / <?=$ip?>:<?=$port?>[SrBnPt]</td>
<td align=right class=oben background="images/<?=$design[$dset]?>_header.gif"><img src="images/clear.gif" width=10 height=10 name="back"><a class=nav2 href="server_admin.php" onMouseOver="mi('back')" onMouseOut="mo('back')"><?=$text_back?>&nbsp;</a></td></tr>
</tr>
<tr><td colspan=2><hr></td></tr></table>
<table  border=0 cellspacing=0 class=frame width="<?=$awidth?>">
<tr><td background="images/<?=$design[$dset]?>_header.gif" colspan=2 class=bigheader align=center>FeedbackBox</td>
</tr><tr>
<td class=randende colspan=2 align=center><?=$mess?></td>
</tr>
<tr><td background="images/<?=$design[$dset]?>_header.gif" colspan=2 class=bigheader align=center>Add Ban</td>
</tr><tr>
<td class=randende colspan=2 align=center>
<input type="text" class="textfield" name="tobanid" size="60" maxlength="32" value="BanID" title="Enter BanID to ban here">
&nbsp;<input type="submit" class="button" name="Submit" value="AddBanID"><input type="hidden" class="button" name="Submit" value="AddBanID"></td>
</tr>
<tr>
<td align=left class=oben background="images/<?=$design[$dset]?>_header.gif" width=20>&nbsp;<b><?=$text_number?></td>
<td align=center class=oben background="images/<?=$design[$dset]?>_header.gif">Banned-ID (Click to remove)</td></tr>
<?php
if (isset($ausgabe['0']))
{
sort ($ausgabe);
$i=0;
foreach ($ausgabe as $bannr => $item)
{
$item=trim($item);
echo "<tr><td align=left class=rand>&nbsp;".$bannr."</td>";
echo "<td align=center class=randende><img src=\"images/clear.gif\" width=10 height=10 name=\"ban".$i."\"><a class=nav href=\"server_banlist_admin.php?delban=".$item."\" onMouseOver=\"mi('ban".$i."')\" onMouseOut=\"mo('ban".$i."')\">".$item."</a></td></tr>";
$i++;
}
}
else
{
echo "<tr><td align=center colspan=2 class=randende>offline or not supported!</td></tr>";
echo "</table><br><br><br><a class=nav href=\"javascript:location.reload()\">Refresh</a><br><br>";}
?>
</table><center><br><font class="normal" ><a class=nav href="server_admin.php"><?=$text_back?></a>
<?=Copyrightext()?></center></body></html>

