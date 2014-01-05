<?php
// Script (C)opyright 2003 by =TSAF=Muschel
// Released under GNU GENERAL PUBLIC LICENSE
// www.tsaf.de , muschel@tsaf.de
error_reporting(2047);
foreach ($_GET as $key => $value){${$key}=$value;}
if (!isset($ip) or !isset($port)) {die ("No IP or ServerBeaconPort in URL !!");}
require("config.inc.php");
$db=mysql_connect("$dbHost","$dbUser","$dbPass") or die ("<CENTER>Connect-Error to MySQL!");
@mysql_select_db("$dbDatabase",$db) or die ("<CENTER>Connect-Error to Database!");
$res=@mysql_query("SELECT * FROM $dbtable2 WHERE id='1'");
$num=mysql_num_rows($res);
for ($q=1; $q<$num+1;$q++)
{
$dbrow=mysql_fetch_array($res);
$lset=$dbrow['language'];
$dset=$dbrow['css'];
}
$css="css/".$design[$dset]."_css.css";
if (isset($customlanguage)){setcookie ("RVScustomlanguage",$customlanguage);}
else
{if (isset($HTTP_COOKIE_VARS["RVScustomlanguage"])){$customlanguage=$HTTP_COOKIE_VARS["RVScustomlanguage"];}}
if (!isset($customlanguage)) {$customlanguage=$language[$lset];}
require('language/'.$customlanguage.'.inc.php');
require("header.php");
?>
<link rel="stylesheet" type="text/css" href="<?=$css?>">
<body class=body>

<center>
<table border=0 cellspacing=0 width="<?=$swidth?>">
<tr><td align=left class=oben background="images/<?=$design[$dset]?>_header.gif">
<?php
echo "&nbsp;";
foreach ($language as $item )
{
echo "<a href=\"server_restkit.php?customlanguage=".$item."&port=".$port."&ip=".$ip."&sname=".$sname."\"><img border=0 src=\"language/flags/".$item.".gif\"></a>&nbsp;";
}
?>
<?=$text_servrestkit?> / <?=$sname?></td>
<td align=right class=oben background="images/<?=$design[$dset]?>_header.gif"><img src="images/clear.gif" width=10 height=10 name="back"><a class=nav2 href="server.php?beaconport=<?=$port?>&ip=<?=$ip?>" onMouseOver="mi('back')" onMouseOut="mo('back')"><?=$text_back?>&nbsp;</a></td></tr>
</tr>
<tr><td colspan=2><hr></td></tr></table>
<table border=0 cellspacing=0 width="<?=$swidth?>">
<tr><td align=center class=oben background="images/<?=$design[$dset]?>_header.gif"><?=$text_restkit_tabletitle?></td></tr>
</table><table border=0 cellspacing=0 height=5 width=100><tr><td></td></tr></table>
<?php
$fp=fsockopen("udp://".$ip,$port,$error,$error2);
if (is_resource($fp))
{
socket_set_timeout($fp,$socket_timeout);
fwrite($fp,"RESTKIT",7);
if ($socket_blocking_use==True) {socket_set_blocking($fp,True);}
$serverrepeat=fread($fp,1);
$bytes_left=socket_get_status($fp);
if ($bytes_left['unread_bytes']>0){$serverrepeat.=fread($fp,$bytes_left['unread_bytes']); }

 $numbercheck=explode(" ¶",$serverrepeat);
 foreach ($numbercheck as $item)
  {$numbercheckarray[substr($item,0,2)]=substr($item,3,strlen($item)-3);}

 for ($i=1;$i<$numbercheckarray['NP'];$i++)
 {
 fwrite($fp,"RESTKIT",7);
 $serverrepeat.=fread($fp,1);
 $bytes_left=socket_get_status($fp);
 if ($bytes_left['unread_bytes']>0){$serverrepeat.=fread($fp,$bytes_left['unread_bytes'])." ¶";}
 }
fclose($fp);
}
if (isset($serverrepeat)){
$restkittemp=explode(" ¶",$serverrepeat);
 foreach ($restkittemp as $item){
  $PosTmp=strpos($item,"/");
  if (isset($restkitarray[substr($item,0,$PosTmp)]))
  {$restkitarray[substr($item,0,$PosTmp)].=substr($item,$PosTmp,strlen($item)-$PosTmp);}
  else
  {$restkitarray[substr($item,0,$PosTmp)]=substr($item,$PosTmp,strlen($item)-$PosTmp);}}
 for ($i=0;$i<10;$i++){
  echo "<table border=0 cellspacing=0 width=\"".$swidth."\"><tr><td class=header background=\"images/".$design[$dset]."_middle.gif\" align=center><b>".$kittype[$i]."</td></tr>";
  $tempkit=explode("/",$restkitarray["R".$i]);
  foreach ($tempkit as $show){
   if ($show){echo "<tr><td class=randende align=center>";
    if ($weapons[$show]){echo $weapons[$show];} else {echo $show;}
    echo "</td></tr>";
   }
  }
  echo "</table><table border=0 cellspacing=0 height=3 width=100><tr><td></td></tr></table>";
 }
 echo "</table>";
}
else
{echo "<table border=0 cellspacing=0 width=\"".$swidth."\"><tr><td class=header background=\"images/".$design[$dset]."_middle.gif\" align=center><b>".$text_restkit_nodata."</td></tr></table>";
echo "<br><br><br><a class=nav href=\"javascript:location.reload()\">Refresh</a><br><br>";}
?>
<br><font class="normal" ><a class=nav href="server.php?ip=<?=$ip?>&beaconport=<?=$port?>" ><?=$text_back?></a>
<?=Copyrightext()?></center></body></html>
