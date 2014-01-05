<?php
// Script (C)opyright 2003 by =TSAF=Muschel
// Released under GNU GENERAL PUBLIC LICENSE
// www.tsaf.de , muschel@tsaf.de
error_reporting(2039);
foreach ($_GET as $key => $value){${$key}=$value;}
require("config.inc.php");
DefDiffLevels();
if (isset($ip)){setcookie ("RVSsessionip",$ip,2147483647,"/");$info="New IP from URL set!";}
else{
if (isset($HTTP_COOKIE_VARS["RVSsessionip"]))
{$ip=$HTTP_COOKIE_VARS["RVSsessionip"];}
else{die ("No IP error!");}
}
if (isset($port)){setcookie ("RVSsessionport",$port,2147483647,"/");$info=$info." New Port from URL set!";}
else{
if (isset($HTTP_COOKIE_VARS["RVSsessionport"]))
{$port=$HTTP_COOKIE_VARS["RVSsessionport"];}
else{die ("No Beaconport error!");}
}
ConnectTheDBandGetDefaults();
BuildGameModeTranslateArray();
require('language/'.$customlanguage.'.inc.php');
$cookiename="RVS".$port.crc32($ip);
if ($Submit=="Logoff"){
setcookie ($cookiename,"",time()-3600,"/");
setcookie ("RVSsessionip","",time()-3600,"/");
setcookie ("RVSsessionport","",time()-3600,"/");
echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=serverliste.php\">";}
if ($Submit=="Leave"){echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=serverliste.php\">";}
if($Submit=="Login"){
$packetstring="ADMIN ".$pw." SAY Webadmin logged in!";
$fp=fsockopen("udp://".$ip,$port,$error,$error2);
if (is_resource($fp)){
if ($socket_blocking_use==True) {socket_set_blocking($fp,True);}
fwrite($fp,$packetstring,strlen($packetstring));$sTmp=fread($fp,1);$bytes_left=socket_get_status($fp);
if ($bytes_left['unread_bytes']>0){$sTmp.=fread($fp, $bytes_left['unread_bytes']);}
fclose($fp);}
else {$beaconerror=True;}
if ($sTmp=="success"){setcookie ($cookiename,base64_encode($pw),2147483647,"/");$pwcookie=$pw;$info="Logged in and Cookies set to this Server!";}
}
else{$pwcookie=base64_decode($HTTP_COOKIE_VARS[$cookiename]);}
$ip=gethostbyname($ip);
require("header.php");
?>
<script language="javascript">
<!--
if (document.images) { on = new Image(); on.src = "images/indicator.gif"; off = new Image(); off.src ="images/clear.gif"; }
function mi(n) { if (document.images) {document[n].src = eval("on.src");}}
function mo(n) { if (document.images) {document[n].src = eval("off.src");}}
// -->
</script>
<link rel="stylesheet" type="text/css" href="<?=$css?>"><body class=body>
<center><table border=0 cellspacing=0 width="<?=$awidth?>"><form name="svradm" action="server_admin.php">
<tr><td align=left class=oben background="images/<?=$design[$dset]?>_header.gif"><?=ShowFlags("server_admin.php","")?>Raven-Shield Server-Admin</td><td align=right class=bigheader background="images/<?=$design[$dset]?>_header.gif"><img src="images/clear.gif" width=10 height=10 name="fresh"><a class="nav2" href="javascript:location.reload()" onMouseOver="mi('fresh')" onMouseOut="mo('fresh')"><?=$text_refresh?></a>&nbsp;</td>
</tr><tr><td colspan=2><hr></td></tr></table><font class=normal>
<?php
$hitx=0;
if($pwcookie){
$pw=$pwcookie;
if ($Submit){

switch ($Submit){

case "SetName";
$sendit=True;
$packetstring="ADMIN ".$pw." SETSERVEROPTION ServerName ".$newsvrname;
$info="Servername set to: ".$newsvrname." !";
$sleeping=0;
break;

case "SetAPass";
if ($newadmpass=="New Admin-Password"){$info="No Admin-Password given!";}
else
{$sendit=True;
$packetstring="ADMIN ".$pw." SETSERVEROPTION AdminPassword ".$newadmpass;
$info="Admin-Password set to: ".$newadmpass." !";
$sleeping=0;}
break;

case "SetMOTD";
$packetstring="ADMIN ".$pw." SETSERVEROPTION MOTD ".$newmotd;
$info="MOTD set to: ".$newmotd." !";
$sendit=True;$sleeping=0;
break;

case "Say";
$packetstring="ADMIN ".$pw." SAY Webadmin: ".$saymsg;
$info="Say: ".$saymsg;
$sendit=True;$sleeping=0;
break;

case "restart";
if (!$sendit){$info="restartconfirm";}
else
{$packetstring="ADMIN ".$pw." RESTART";
$info="Server Restart-Request was send!";
$sleeping=20;}
break;

case "Map";
if (!$sendit){$info = "mapchangeconfirm";
$adds="&map=".$map."&mapt=".$mapt;}
else
{$packetstring="ADMIN ".$pw." MAP ".$map;
$info="Changed to Map ".$mapt."!";
$sleeping=20;}
break;

case "RemoveMap";
if (!$sendit)
{$info="mapremoveconfirm";
$adds="&map=".$map."&mapt=".$mapt;}
else
{$packetstring="ADMIN ".$pw." REMOVEMAP ".$map;
$info="Remove Map ".$mapt." was send!";}
break;

case "RemoveMapsToEnd";
if (!$sendit)
{$info="mapremovetoendconfirm";
$adds="&map=".$map;}
else
{
$info="Remove Maps 2 to ".$map." was send!";

$fp=fsockopen("udp://".$ip,$port,$error,$error2);
if (is_resource($fp)) {
if ($socket_blocking_use==True) {socket_set_blocking($fp,True);}

for ($i=2;$i<=$map;$i++)
{
$packetstring="ADMIN ".$pw." REMOVEMAP 2";
fwrite($fp,$packetstring,strlen($packetstring));
$sanswer.=$i."/".fread($fp,1);
$bytes_left=socket_get_status($fp);
if ($bytes_left['unread_bytes']>0){$sanswer.=fread($fp,$bytes_left['unread_bytes'])." ";}
}
}

fclose($fp);
$sendit=False;
}
break;

case "InsertMap";
if ($newmapname=="" or $newmapgametype=="")
{$packetstring="AVAILABLEMAPS";
$sendit=True;
$info="List avaliable Maps!";}
else
{$packetstring="ADMIN ".$pw." ADDMAP ".$newmapname." ".$newmapgametype." ".$map;
$info="Insert Map ".$newmapname." type ".$newmapgametype." to Position ".$map." was send!";
$sendit=True;}
break;

case "Need RestartMatch for Changes";
$info="restartmatchconfirm";
break;

case "SendPBC";
if ($pbc=="PB_SV_Enable")
{$packetstring="ADMIN ".$pw." PBENABLE";
$sleeping=10;}
elseif ($pbc=="PB_SV_Disable")
{$packetstring="ADMIN ".$pw." PBDISABLE";
$sleeping=10;}
elseif ($pbc=="PB_SV_GetSs" and ($pbadd<>"" or $pbadd64<>""))
{if ($pbadd64) {$pbadd=base64_decode($pbadd64);}
$packetstring="ADMIN ".$pw." PBCOMMAND ".$pbc.' "'.$pbadd.'"';}
else
{$packetstring="ADMIN ".$pw." PBCOMMAND ".$pbc." ".$pbadd;}
$sendit=True;
$info="PB-Command was send: ".$pbc." ".htmlspecialchars($pbadd)." !";
break;

case "restartmatch";
if (!$sendit)
{$info="restartmatchconfirm";}
else
{$packetstring="ADMIN ".$pw." RESTARTMATCH";
$info="Map restarted! Settings active.";
$sleeping=10;}
break;

case "restartround";
if (!$sendit)
{$info="restartroundconfirm";}
else
{$packetstring="ADMIN ".$pw." RESTARTROUND";
$info="Round restarted!";
$sleeping=4;}
break;

case "Kick";
if (!$sendit)
{$info="kickconfirm";
$adds="&ubi=".$ubi;}
else
{$packetstring="ADMIN ".$pw." KICKUBI ".$ubi;
$info="UBI:".$ubi." was kicked!";
$sleeping=3;}
break;

case "Ban";
if (!$sendit)
{$info="banconfirm";
$adds="&ubi=".$ubi;}
else
{$packetstring="ADMIN ".$pw." BANUBI ".$ubi;
$info="UBI:".$ubi." was banned!";
$sleeping=3;}
break;

case "optionswitch";
if ($old=="0")
{$packetstring="ADMIN ".$pw." SETSERVEROPTION ".$type." True";
$info="Option ".$type." switched to True";}
else
{$packetstring="ADMIN ".$pw." SETSERVEROPTION ".$type." False";
$info= "Option ".$type." switched to False";}
$sendit=true;
break;

case "diffset";
$packetstring="ADMIN ".$pw." SETSERVEROPTION DiffLevel ".$diffset;
$sendit=true;
$info="Option DiffLevel set to ".$diff[$diffset];
break;

case "SetST";
$packetstring="ADMIN ".$pw." SETSERVEROPTION SpamThreshold ".$spam;
$sendit=true;
$info="Option SpamThreshold set to ".$spam;
break;

case "SetCLD";
$packetstring="ADMIN ".$pw." SETSERVEROPTION ChatLockDuration ".$cld;
$sendit=true;
$info="Option ChatLockDuration set to ".$cld;
break;

case "SetVBMF";
$packetstring="ADMIN ".$pw." SETSERVEROPTION VoteBroadcastMaxFrequency ".$vote;
$sendit=true;
$info= "Option VoteBroadcastMaxFrequency set to ".$vote;
break;

case "SetBT";
if ($newbt<"30" or $newbt>"60")
{$info= "Bombtime only 30-60 s!";}
else
{$packetstring="ADMIN ".$pw." SETSERVEROPTION BombTime ".$newbt;
$sendit=true;
$info= "Bombtime set to ".$newbt." s!";}
break;

case "SetTbR";
if ($newtbr<"0" or $newtbr>"99")
{$info="Time between Round only 0-99 s!";}
else
{$packetstring="ADMIN ".$pw." SETSERVEROPTION BetweenRoundTime ".$newtbr;
$sendit=true;
$info="Time between Round set to ".$newtbr." s!";}
break;

case "SetTC";
if ($newtc<"5" or $newtc>"40")
{$info= "Terrorcount only 5-40 Terrors!";}
else
{$packetstring="ADMIN ".$pw." SETSERVEROPTION NbTerro ".$newtc;
$sendit=true;
$info= "Terrorcount set to ".$newtc." Terrors!";}
break;

case "SetRds";
if ($newrds<"1" or $newrds>"20" )
{$info= "Only 1-20 Rounds!";}
else
{$packetstring="ADMIN ".$pw." SETSERVEROPTION RoundsPerMatch ".$newrds;
$sendit=true;
$info="Rounds set to ".$newrds." Rounds!";}
break;

case "SetRT";
if ($newrt<"60" or $newrt>"3600")
{$info="Roundtime only 60-3600 seconds!";}
else
{$packetstring="ADMIN ".$pw." SETSERVEROPTION RoundTime ".$newrt;
$sendit=true;
$info="Roundtime set to ".$newrt." seconds!";}
break;

case "SetText0";
$messtext0=str_replace("\'","'",$messtext0);
$messtext0=str_replace("\\\\","\\",$messtext0);
$packetstring="ADMIN ".$pw." MESSTEXT0 ".$messtext0;
$sendit=true;
$info="MessengerText[0] set to: ".$messtext0." !";
break;

case "SetText1";
$messtext1=str_replace("\'","'",$messtext1);
$messtext1=str_replace("\\\\","\\",$messtext1);
$packetstring="ADMIN ".$pw." MESSTEXT1 ".$messtext1;
$sendit=true;
$info="MessengerText[1] set to: ".$messtext1." !";
break;

case "SetText2";
$messtext2=str_replace("\'","'",$messtext2);
$messtext2=str_replace("\\\\","\\",$messtext2);
$packetstring="ADMIN ".$pw." MESSTEXT2 ".$messtext2;
$sendit=true;
$info="MessengerText[2] set to: ".$messtext2." !";
break;

case "Messenger";
$packetstring="ADMIN ".$pw." MESSENGER";
$sendit=true;
$info="switched Messenger!";
break;

case "SendCommand";
if ($ccommand=="" or $ccommand=="Enter Console Command here"){$info="No Command given!";}
else
{$packetstring="ADMIN ".$pw." CONSOLE ".$ccommand;
$info="Command:".$ccommand." was send!";
$sendit=true;$sleeping=1;}
break;

case "LoadINI";
if ($inifile=="" or $inifile=="file to load"){$info="No Filename given!";}
else
{$packetstring="ADMIN ".$pw." LOADSERVER ".$inifile;
$info="Loadserver File:".$inifile.".ini was send!";
$sendit=true;$sleeping=14;}
break;

case "SaveINI";
if ($inisavefile=="" or $inisavefile=="file to save"){$info="No Filename given!";}
else
{$packetstring="ADMIN ".$pw." SAVESERVER ".$inisavefile;
$sendit=true;
$info="Saveserver File:".$inisavefile.".ini was send!";}
break;


case "SetMax";
if ($newmaxpl<"1" or $newmaxpl>$maxedpl){$info="Maxplayer only 1-".$maxedpl." Players!";}
else
{$packetstring="ADMIN ".$pw." SETMAXPLAYERS ".$newmaxpl;
$sendit=true;
$info="Maxplayer set to ".$newmaxpl." Players!";}
break;

case "PWoff";
$packetstring="ADMIN ".$pw." LOCKSERVER";
$sendit=true;
$info="Game-Password is disabled!";
break;

case "PWon";
if ($gpw<>"NewPassword" and ctype_alnum($gpw)<>False)
{$packetstring="ADMIN ".$pw." LOCKSERVER ".$gpw;
$sendit=true;
$info="Game-Password (".$gpw.") is enabled!";}
else{$info="None or not alphanumeric Game-Password entered!";}
break;

case "Cancel";$info="canceled!";break;

}

if ($sendit)
{
$fp=fsockopen("udp://".$ip,$port,$error,$error2);
if (is_resource($fp)) {
if ($socket_blocking_use==True) {socket_set_blocking($fp,True);}
fwrite($fp,$packetstring,strlen($packetstring));
$sanswer=fread($fp,1);
$bytes_left=socket_get_status($fp);
if ($bytes_left['unread_bytes']>0){$sanswer.=fread($fp,$bytes_left['unread_bytes']);}
fclose($fp);
sleep ($sleeping);
}
}
if ($packetstring=="AVAILABLEMAPS")
{
$numbercheck=explode(" �", $sanswer);
foreach ($numbercheck as $item){$itemlen=strlen($item);$numbercheckarray[substr($item,0,2)]=substr($item,3,$itemlen-3);}
$sanswernext="";
$fp=fsockopen("udp://".$ip,$port,$error,$error2);
if (is_resource($fp)){
if ($socket_blocking_use==True) {socket_set_blocking($fp,True);}
fwrite($fp,$packetstring,strlen($packetstring));
for ($i=1;$i<=$numbercheckarray['NP'];$i++)
{

$sanswernext.=fread($fp,1);
$bytes_left=socket_get_status($fp);
if ($bytes_left['unread_bytes']>0){$sanswernext.=fread($fp,$bytes_left['unread_bytes'])." �";}
}
fclose($fp);

$numbercheck=explode(" �",$sanswernext);
foreach ($numbercheck as $item){$itemlen=strlen($item);
$numbercheckarray[substr($item,0,2)]=$numbercheckarray[substr($item,0,2)]." �".substr($item,3,$itemlen-3);}

$availmaps=explode(" �",$numbercheckarray['AM']);
?>
<center>
<table  border=0 cellspacing=0 class=frame width="<?=$swidth?>">
<tr><td background="images/<?=$design[$dset]?>_header.gif" colspan=2 class=bigheader align=center>Click Map & Mode to add</td>
</tr><tr>
<td class=header background="images/<?=$design[$dset]?>_middle.gif" align=center><b>Map</td>
<td class=header background="images/<?=$design[$dset]?>_middle.gif" align=center><b>Gamemodes</td>
</tr>
<?php
$hitx=1;
foreach ($availmaps as $item){$expl=explode(":",$item);$availablemaps[$expl['0']]=$expl['1'];}
ksort ($availablemaps);
foreach ($availablemaps as $mapname => $availmodes)
 {
 echo "<tr><td class=rand align=center>".$mapname."</td><td class=randende align=left>";
 $availmode=explode("/",$availmodes);

 foreach ($availmode as $item)
 {
 if ($item!="")
 {
 echo "<img src=\"images/clear.gif\" width=10 height=10 name=\"maps".$hitx."add\"><a class=nav href=\"server_admin.php?Submit=InsertMap&newmapname=".$mapname."&map=".$map."&newmapgametype=".$item."\" onMouseOver=\"mi('maps".$hitx."add')\" onMouseOut=\"mo('maps".$hitx."add')\">".TranslateBeaconGameModeToText($item)."</a> |";
 $hitx++;
 }
 }
 echo "&nbsp;</td></tr>";
 }
?>
</table>
<br>
<input type="submit" class="button" name="Submit" value="Cancel">
<br><br><br>
<?php
}
}
elseif ($Submit=="punks")
{
?>
<center>
<table  border=0 cellspacing=0 class=frame width="<?=$swidth?>">
<tr><td background="images/<?=$design[$dset]?>_header.gif" colspan=3 class=bigheader align=center>Punkbuster Commands</td>
</tr><tr>
<td class=header background="images/<?=$design[$dset]?>_middle.gif" align=center width="110"><b>Command</td>
<td class=header background="images/<?=$design[$dset]?>_middle.gif" align=center><b>Option</td>
<td class=header background="images/<?=$design[$dset]?>_middle.gif" align=center width="80"><b>Send</td>
</tr><tr><form>
<td class=rand align=center ><b>PB_SV_Enable<input type="hidden" name="pbc" value="PB_SV_Enable"></td>
<td class=rand align=center><b>---</td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="Enables the PunkBuster Server Software "></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_Disable<input type="hidden" name="pbc" value="PB_SV_Disable"></td>
<td class=rand align=center><b>---</td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="Disables the PunkBuster Server Software - the disabling does not take effect until the game server is exited and restarted"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_Update<input type="hidden" name="pbc" value="PB_SV_Update"></td>
<td class=rand align=center><b>---</td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="Forces the PB Server to attempt a PB software update even if no players are currently connected "></td>
</form></tr><tr><form>
<td class=rand align=center ><b>PB_SV_Restart<input type="hidden" name="pbc" value="PB_SV_Restart"></td>
<td class=rand align=center><b>---</td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="Restarts the PunkBuster software."></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_Load<input type="hidden" name="pbc" value="PB_SV_Load"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="70" value="File Name" title="File Name"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="Loads the specified PunkBuster configuration file which can contain PunkBuster commands and/or setting changes"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_WriteCfg<input type="hidden" name="pbc" value="PB_SV_WriteCfg"></td>
<td class=rand align=center><b>(to pbsv.cfg)</td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="Writes the current values of the PunkBuster Server settings to the local hard drive (creating or overwriting a file called pbsv.cfg) in such a way that they will be loaded automatically the next time the PunkBuster Server starts; server admins who wish to manage multiple config files for different situations will usually not use this command at all "></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_GetSs<input type="hidden" name="pbc" value="PB_SV_GetSs"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="70" title="Playernick, empty for all Players"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="Sends a request to all applicable connected players asking for a screen shot to be captured and sent to the PB Server; to specify a player name or substring (as opposed to slot #), surround the text with double-quote marks"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_Ban<input type="hidden" name="pbc" value="PB_SV_Ban"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="255" title="[name or slot #] [displayed_reason] | [optional_private_reason]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="Removes a player from the game and permanently bans that player from the server based on the player's guid (based on the cdkey); the ban is logged and also written to the pbbans.dat file in the pb folder"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_BanGuid<input type="hidden" name="pbc" value="PB_SV_BanGuid"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="255" title="[guid] [player_name] [IP_Address] [reason]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="Adds a guid directly to PB's permanent ban list; if the player_name or IP_Address are not known, we recommend using ???"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_AutoSs<input type="hidden" name="pbc" value="PB_SV_AutoSs"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="1" title="[0/1]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="Set to 1 (default is 0) if you want the PB server to regularly retrieve screen shots from connected players"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_AutoSsFrom<input type="hidden" name="pbc" value="PB_SV_AutoSsFrom"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="10" title="[Seconds]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="Minimum number of seconds (default is 60) PB will wait before requesting a screen shot after the previous request from each player"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_AutoSsTo<input type="hidden" name="pbc" value="PB_SV_AutoSsTo"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="10" title="[Seconds]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="Maximum number of seconds (default is 1200 = 20 minutes) PB will wait before requesting a screen shot after the previous request from each player"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_ChangePeriod<input type="hidden" name="pbc" value="PB_SV_ChangePeriod"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="3" title="[1-999]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="This setting works in combination with pb_sv_changemax. It defines a period of time (in seconds) during which a player may do up to pb_sv_changemax name changes. Default is 999 which means disabled."></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_ChangeMax<input type="hidden" name="pbc" value="PB_SV_ChangeMax"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="2" title="[1-50]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="This setting works in combination with pb_sv_changeperiod. This setting defines how many name changes can be done over a specified period of seconds (pb_sv_changeperoid). If the player does more name changes during this period the player will be kicked. "></td>
<input type="hidden" name="ip" value="<?=$ip?>" >
<input type="hidden" name="port" value="<?=$port?>" >
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_CQC<input type="hidden" name="pbc" value="PB_SV_CQC"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="1" title="[0/1]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="CQC means Client Query Capability - setting this to 0 (default is 1) means that connected players cannot use PB to check the value of game server cvars (we recommend leaving this set to the default of 1 to promote goodwill); NOTE that PB doesn't let players see the value of any server-side cvars that include the text 'pass' nor any PB settings"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_ExtChar<input type="hidden" name="pbc" value="PB_SV_ExtChar"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="3" title="[0/1]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="The default setting of 0 tells PunkBuster to disallow extended ASCII Characters in player names; for the purposes of this command, characters that cannot be easily entered with simple keystrokes are considered extended"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_KickLen<input type="hidden" name="pbc" value="PB_SV_KickLen"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="10" title="[Minutes]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="The number of minutes (default is 2) a player will be kept from being able to rejoin after getting kicked by PunkBuster"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_SsCeiling<input type="hidden" name="pbc" value="PB_SV_SsCeiling"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="10" title="[Number]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="The highest serial number (default is 100) that PB will use in numbering Screenshot (PNG) files obtained from players before starting over at the PB_SV_SsFloor value"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_SsDelay<input type="hidden" name="pbc" value="PB_SV_SsDelay"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="10" title="[Seconds]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="When this is non-zero (default is 0), then each PB client will wait a random number of seconds up to the value of this setting after receiving the request before actually capturing a screen image for sending back to the server"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_SsFloor<input type="hidden" name="pbc" value="PB_SV_SsFloor"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="10" title="[Number]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="The lowest serial number (default is 1) that PB will use in numbering Screenshot (PNG) files obtained from players"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_SsHeight<input type="hidden" name="pbc" value="PB_SV_SsHeight"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="4" title="[Pixels]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="The requested height (default is 240 pixels) of images captured by PunkBuster Clients for sending to the PB Server"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_SsNext<input type="hidden" name="pbc" value="PB_SV_SsNext"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="10" title="[Number]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="The next serial number that PB will use to name a PNG screen shot image file"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_SsSRate<input type="hidden" name="pbc" value="PB_SV_SsSRate"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="1" title="[Number]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="The sample rate (default is 1) used for capturing screen shots, specifies how many pixels get skipped in the processing of the image to keep file sizes down; if set to 2, then only every 2nd pixel is taken (in both horizontal and vertical directions); if set to 4, then only every 4th pixel is taken"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_SsWidth<input type="hidden" name="pbc" value="PB_SV_SsWidth"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="4" title="[Pixels]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="The requested width (default is 320 pixels) of images captured by PunkBuster Clients for sending to the PB Server"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_SsXPct<input type="hidden" name="pbc" value="PB_SV_SsXPct"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="3" title="[Percentage]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="The percentage across the screen (default is 50%) where the center of the requested screenshot should be captured from"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_SsYPct<input type="hidden" name="pbc" value="PB_SV_SsYPct"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="3" title="[Percentage]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="The percentage down the screen (default is 50%) where the center of the requested screenshot should be captured from"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_UpdateGrace <input type="hidden" name="pbc" value="PB_SV_UpdateGrace "></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="10" title="[Seconds]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="Holds the number of seconds (default is 600) that PunkBuster allows for a player to successfully update to the version of PunkBuster currently in use at the server"></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_File<input type="hidden" name="pbc" value="PB_SV_File"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="255" title="[filename] [o/s] [game_version] [filesize] [md5] ... [md5]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="syntax: pb_sv_file [filename] [o/s] [game_version] [filesize] [md5] ... [md5]  *  the [o/s] parameter can be w (for win32), l (for linux), m (for mac), or a (for all)  *  multiple md5 hashes can be specified separated by spaces to signify multiple 'official' releases for a given file / game version combination. example: pb_sv_file r6weapons.dll w 1.41.323 57344 7a16bce701d6bf83a6c3d028136b7803 "></td>
</form></tr><tr><form>
<td class=rand align=center><b>PB_SV_AutoUpdBan<input type="hidden" name="pbc" value="PB_SV_AutoUpdBan"></td>
<td class=rand align=center><b><input type="text" class="textfield" name="pbadd" size="70" maxlength="1" title="[0/1]"></td>
<td class=randende align=center><b><input type="submit" class="button" name="Submit" value="SendPBC" title="Set to 1 (defaults to 0) if you want PB to automatically update the permanent ban file (pbbans.dat) after each change to the banlist in memory"></td>
</form></tr></table>
<br><form>
<input type="submit" class="button" name="Submit" value="Cancel">
<br><br><br></form>
<?php
}
else {echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=server_admin.php?info=".$info."&respond=".$sanswer.$adds."\">";}
}
else
{
	$beaconconnect=fsockopen("udp://".$ip,$port,$errno,$errstr);
	if (is_resource($beaconconnect))
	{
	socket_set_timeout($beaconconnect,$socket_timeout);
	fwrite($beaconconnect,"REPORTEXT",9);
	if ($socket_blocking_use==True) {socket_set_blocking($beaconconnect,True);}
	$antworta=fread($beaconconnect,1);$anz=socket_get_status($beaconconnect);
    if ($anz['unread_bytes']>0){$antworta.=fread($beaconconnect,$anz['unread_bytes']);}
	$anzahl = substr($antworta,5,1);

	$packetnr=substr($antworta,strlen($antworta)-1,1);
    $sorted[$packetnr]=substr($antworta,7,strlen($antworta)-13);

for ($i=2;$i<=$anzahl;$i++)
{
$antworta=fread($beaconconnect,1);$anz=socket_get_status($beaconconnect);
if ($anz['unread_bytes']>0){$antworta.=fread($beaconconnect,$anz['unread_bytes']);}
$packetnr=substr($antworta,strlen($antworta)-1,1);
$sorted[$packetnr]=substr($antworta,7,strlen($antworta)-13);

}

    $antwort="";
    for ($j=1;$j<=$anzahl;$j++)
    {
    $antwort.=$sorted[$j];
    }


	$beaconconnectgetpw=fsockopen("udp://".$ip,$port,$errno,$errstr);
	if (is_resource($beaconconnectgetpw))
	{
	$sendstr="ADMIN ".$pw." GAMEPASSWORD";
	fwrite($beaconconnectgetpw,$sendstr,strlen($sendstr));
	if ($socket_blocking_use==True) {socket_set_blocking($beaconconnectgetpw,True);}
	$readedgpw=fread($beaconconnectgetpw,1);$anz=socket_get_status($beaconconnectgetpw);
	if ($anz['unread_bytes']>0){$readedgpw.=fread($beaconconnectgetpw,$anz['unread_bytes']);}
    fclose ($beaconconnectgetpw);
    if ($readedgpw) {$gamepass=trim($readedgpw);}
	}

	fclose($beaconconnect);
   }


if (!isset($antwort))
{echo $text_offline['0'].$ip.$text_offline['1'].$port.$text_offline['2'];
echo "<br><br><br><a class=nav href=\"javascript:location.reload()\">Refresh</a><br><br>";}
else
{
$daten=explode(" �", $antwort);
foreach ($daten as $item){$itemlen=strlen($item);$dataarray[substr($item,0,2)]=substr($item,3,$itemlen-3);}

$counter=0;
if ($dataarray['G1']<>"1"){$passwordedpic='public.gif';}
else {$passwordedpic='private.gif';}
$MapCycle=explode("/",$dataarray['K1']);
$GameTypelist=explode("/",$dataarray['J1']);
$PlayerList=explode("/",substr($dataarray['L1'],1));
$PlayerTime=explode("/",substr($dataarray['M1'],1));
$PlayerPing=explode("/",substr($dataarray['N1'],1));
$PlayerKills=explode("/",substr($dataarray['O1'],1));
$pic=strtolower($dataarray['E1']);
if (!file_exists('./mapimages/'.($pic).'.jpg')){$pic="nopic";}
$InTeam=explode("/",substr($dataarray['TE'],1));
$Deaths=explode("/",substr($dataarray['DE'],1));
$Alive=explode("/",substr($dataarray['HE'],1));
$Ubi=explode("/",substr($dataarray['UB'],1));
$PWpn=explode("/",substr($dataarray['PW'],1));
$SWpn=explode("/",substr($dataarray['SW'],1));
$PWpnG=explode("/",substr($dataarray['PG'],1));
$SWpnG=explode("/",substr($dataarray['SG'],1));
$Hits=explode("/",substr($dataarray['HI'],1));
$Fired=explode("/",substr($dataarray['RF'],1));
$Acc=explode("/",substr($dataarray['AC'],1));
$JoinedLate=explode("/",substr($dataarray['LA'],1));
$TeamScore=explode("/",substr($dataarray['TS'],1));
$dataarray['ST']=$text_servertime.": ".$dataarray['ST'];

if ($info=="banconfirm"){$info="<b><a class=nav href=\"server_admin.php?Submit=Ban&ubi=".$ubi."&sendit=True\">>>>Confirm Ban! : ".$ubi."<<<</a>";}
if ($info=="kickconfirm"){$info="<b><a class=nav href=\"server_admin.php?Submit=Kick&ubi=".$ubi."&sendit=True\">>>>Confirm Kick! : ".$ubi."<<<</a>";}
if ($info=="restartconfirm"){$info="<b><a class=nav href=\"server_admin.php?Submit=restart&port=".$port."&sendit=True\">>>>Confirm Restart!<<<</a>";}
if ($info=="restartmatchconfirm"){$info="<b><a class=nav href=\"server_admin.php?Submit=restartmatch&sendit=True\">>>>Confirm Restartmatch!<<<</a>";}
if ($info=="mapchangeconfirm"){$info="<b><a class=nav href=\"server_admin.php?Submit=Map&map=".$map."&mapt=".$mapt."&sendit=True\">>>>Confirm Mapchange to ".$mapt." !<<<</a>";}
if ($info=="mapremoveconfirm"){$info="<b><a class=nav href=\"server_admin.php?Submit=RemoveMap&map=".$map."&mapt=".$mapt."&sendit=True\">>>>Confirm remove Map: ".$mapt." !<<<</a>";}
if ($info=="mapremovetoendconfirm"){$info="<b><a class=nav href=\"server_admin.php?Submit=RemoveMapsToEnd&map=".$map."&sendit=True\">>>>Confirm remove Maps 2 to ".$map." !<<<</a>";}
if ($info=="restartroundconfirm"){$info="<b><a class=nav href=\"server_admin.php?Submit=restartround&sendit=True\">>>>Confirm Restartround!<<<</a>";}

if (strpos($antwort,"턑3 0")===False and strpos($antwort,"턑3 1")===False) {echo "First Response-UDP-Package overloaded! Try removing same Maps out of Maplist!<br> Caused by too much Maps with long Mapnames!<br> (or old Serverversion without PB-Support...)";}
?>
<center>
<table  border=0 cellspacing=0 class=frame width="<?=$swidth?>">
<tr><td background="images/<?=$design[$dset]?>_header.gif" colspan=1 class=bigheader align=center>FeedbackBox</td>
</tr><tr>
<td class=randende align=center><?=$info?></td>
</tr><tr>
<td class=randende align=center><?=$respond?></td>
</tr></table>
<table  border=0 cellspacing=0 class=frame width="<?=$swidth?>">
<tr>
<td background="images/<?=$design[$dset]?>_header.gif" colspan=2 class=bigheader align=center>Server Control</td>
</tr><tr>
<td class=rand align=left><b><a class=nav href="server_admin.php?Submit=restart">&nbsp;Restart Server</a></td>
<form><td class=randende align=left>&nbsp;
<input type="text" class="textfield" name="inifile" size="25" maxlength="25" value="file to load" title="Enter *.ini file">&nbsp;&nbsp;
<input type="submit" class="button" name="Submit" value="LoadINI">&nbsp;(without .ini !)</td></form>
</tr><tr>
<td class=rand align=left><b><a class=nav href="server_admin.php?Submit=restartmatch">&nbsp;Restart Match</a></td>
<form><td class=randende align=left>&nbsp;
<input type="text" class="textfield" name="inisavefile" size="25" maxlength="25" value="file to save" title="Enter *.ini file">&nbsp;&nbsp;
<input type="submit" class="button" name="Submit" value="SaveINI">&nbsp;(without .ini !)</td></form>
</tr><tr>
<td class=rand align=left valign=center><b><a class=nav href="server_admin.php?Submit=restartround">&nbsp;Restart Round</td>
<form><td class=randende align=left>&nbsp;
<input type="text" class="textfield" name="newadmpass" size="25" maxlength="25" value="New Admin-Password" title="Admin-Password (on Server)">&nbsp;&nbsp;<input type="submit" class="button" name="Submit" value="SetAPass"></td>
</form>
</tr><tr>
<td class=rand align=left valign=center ><b><a class=nav href="server_admin.php?Submit=punks">&nbsp;Punkbuster&nbsp;</a><img src="images/pb.gif"></td>
<form><td class=randende align=left >&nbsp;
<input type="text" class="textfield" name="ccommand" size="70" maxlength="255" value="Enter Console Command here" title="Console">
<input type="submit" class="button" name="Submit" value="SendCommand"></td>
</form></tr><tr>
<form>
<td class=randende align=left colspan=2>&nbsp;
<input type="text" class="textfield" name="saymsg" size="90" maxlength="90" value="Webmaster at Work!" title="Say something to Players">&nbsp;<input type="submit" class="button" name="Submit" value="Say"></td>
</form>
</tr><tr>
<form><td class=randende align=left colspan=2>&nbsp;
<input type="text" class="textfield" name="newmotd" size="90" maxlength="90" value="<?=$dataarray['MM']?>" title="Message of the Day">&nbsp;<input type="submit" class="button" name="Submit" value="SetMOTD"></td>
</form>
</tr></table>
<table  border=0 cellspacing=0 class=frame width="<?=$swidth?>">
<tr>
<td background="images/<?=$design[$dset]?>_header.gif" colspan=4 class=bigheader align=left width="40%">&nbsp;<?=$text_serverinfo?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$dataarray['ST']?></td>
</tr><tr>
<td class=rand>&nbsp;<?=$text_servername?>:&nbsp;&nbsp;</td>
<form>
<td class=rand>&nbsp;<input type="text" class="textfield" name="newsvrname" size="30" maxlength="30" value="<?=htmlentities($dataarray['I1'])?>" title="Servername">&nbsp;<input type="submit" class="button" name="Submit" value="SetName"></td>
</form>
<td class=rand rowspan=9 align=center><img src="images/<?=$passwordedpic?>"></td>
<td class=randende rowspan=9 align=center><img src="mapimages/<?=$pic?>.jpg"></td>
</tr><tr>
<td class=rand>&nbsp;<?=$text_adress?>:</td>
<td class=rand>&nbsp;<?=$ip.":".$dataarray['P1']?></td>
</tr><tr>
<td class=rand>&nbsp;<?=$text_actmap?>:</td>
<td class=rand>&nbsp;<?=$dataarray['E1']?></td>
</tr><tr>
<td class=rand>&nbsp;<?=$text_gametype?>:</td>
<td class=rand>&nbsp;<?=TranslateBeaconGameModeToText($dataarray['F1'])?></td>
</tr><tr>
<td class=rand>&nbsp;<?=$text_version?>:</td>
<td class=rand>&nbsp;<?=$dataarray['D2']?></td>
</tr><tr>
<?php
if ($GameModeTranslate[$dataarray['F1']]=="missioncoop" or $GameModeTranslate[$dataarray['F1']]=="hostagecoop" or $GameModeTranslate[$dataarray['F1']]=="terrorhuntcoop")
{$maxedpl= "8";}
else
{$maxedpl= "16";}
?>
<td class=rand>&nbsp;<?=$text_player?>:</td>
<form><td class=rand>&nbsp;<?=$dataarray['B1']."/".$dataarray['A1']?>&nbsp;&nbsp;&nbsp;<input type="text" class="textfield" name="newmaxpl" size="5" maxlength="5" value="<?=$dataarray['A1']?>" title="Maxplayer">&nbsp;<input type="submit" class="button" name="Submit" value="SetMax"><input type="hidden" name="maxedpl" value="<?=$maxedpl?>"></td>
</form>
</tr><tr>
<td class=rand>&nbsp;<?=$text_rounds?>:</td>
<form><td class=rand>&nbsp;<?=$dataarray['Q1']?> <?=$text_rounds?>/<?=$text_map?>&nbsp;&nbsp;&nbsp;<input type="text" class="textfield" name="newrds" size="5" maxlength="5" value="<?=$dataarray['Q1']?>" title="Rounds per Map">&nbsp;<input type="submit" class="button" name="Submit" value="SetRds"></td>
</form></tr><tr>
<td class=rand>&nbsp;<?=$text_roundtime?>:</td>
<form><td class=rand>&nbsp;<?=(int)($dataarray['R1']/60)?>:<?=sprintf("%02u", $dataarray['R1']-((int)($dataarray['R1']/60)*60))?>&nbsp;m:s&nbsp;&nbsp;&nbsp;<input type="text" class="textfield" name="newrt" size="5" maxlength="5" value="<?=$dataarray['R1']?>" title="Roundtime">&nbsp;s&nbsp;<input type="submit" class="button" name="Submit" value="SetRT"></td>
</form></tr><tr>
<td class=rand>&nbsp;<?=$text_password?>:</td>
<form><td class=rand>&nbsp;<?=$text_yn[$dataarray['G1']]?>&nbsp;&nbsp;
<input type="submit" class="button" name="Submit" value="PWoff">
<input type="text" class="textfield" name="gpw" size="20" maxlength="15" value="<?=$gamepass?>" title="Game-<?=$text_password?>">
<input type="submit" class="button" name="Submit" value="PWon">
</td></form>
</tr><tr>
<td class=rand>&nbsp;Punkbuster:</td>
<td class=rand>&nbsp;<?=$text_yn[$dataarray['L3']]?></td>
<td colspan=2 class=randende  align=center><img src="images/clear.gif" width=10 height=10 name="banlista"><a class=nav href="server_restkit_admin.php" onMouseOver="mi('resta')" onMouseOut="mo('resta')"><b>Restriction Kit Admin</a></td>
</tr><tr>
<td class=rand>&nbsp;<?=$text_customgame?>:</td>
<td class=rand>&nbsp;<?=$dataarray['L2']?></td>
<td colspan=2 class=randende align=center>
<img src="images/clear.gif" width=10 height=10 name="banlista\"><a class=nav href="server_banlist_admin.php" onMouseOver="mi('banlista')" onMouseOut="mo('banlista')"><b>BanList Admin</a>
</td>
</tr><tr>
<td class=rand>&nbsp;<?=$text_currentround?>:</td>
<td class=rand>&nbsp;<?=$dataarray['CR']?></td>
<td class=randende colspan=2 rowspan=2 align=center valign=center>
<?php
if ($dataarray['NR']==1){echo "<input type=submit class=buttonyellow name=Submit value=\"Need RestartMatch for Changes\" >";}
else{echo "In Admin-Mode!";}
?>
</td></tr><tr>
<td class=rand>&nbsp;<?=$text_remaintime?>:</td><td class=rand>
<?php
if ($dataarray['TR']<0){echo "&nbsp;".$text_waitforstart;}
else {
echo "&nbsp;".(int)($dataarray['TR']/60);
echo ":";
echo sprintf("%02u",$dataarray['TR']-((int)($dataarray['TR']/60)*60));
echo "&nbsp;m:s";}
?>
</td></tr></table>
<table width="<?=$swidth?>" border=0 cellspacing=0 class=frame>
<form><tr><td colspan=6 class=bigheader background="images/<?=$design[$dset]?>_header.gif">&nbsp;<?=$text_svrsets?></td>
</tr><tr>
<td class=rand><?=$text_SpamThreshold?></td>
<td class=rand colspan=2 ><input type="text" class="textfield" name="spam" size="20" maxlength="20" value="<?=$dataarray['SH']?>" title="">&nbsp;<input type="submit" class="button" name="Submit" value="SetST"></td>
<td class=rand colspan=2 ><?=$text_bombtime?>&nbsp;<input type="text" class="textfield" name="newbt" size="5" maxlength="5" value="<?=$dataarray['T1']?>" title="<?=$text_bombtime?>">&nbsp;s</td>
<td class=randende><input type="submit" class="button" name="Submit" value="SetBT"></td>
</tr><tr>
<td class=rand><?=$text_ChatLockDuration?></td>
<td class=rand colspan=2><input type="text" class="textfield" name="cld" size="20" maxlength="20" value="<?=$dataarray['CL']?>" title="">&nbsp;<input type="submit" class="button" name="Submit" value="SetCLD"></td>
<td class=rand colspan=2><?=$text_tbr?>&nbsp;<input type="text" class="textfield" name="newtbr" size="5" maxlength="5" value="<?=$dataarray['S1']?>" title="<?=$text_tbr?>">&nbsp;s</td>
<td class=randende><input type="submit" class="button" name="Submit" value="SetTbR"></td>
</tr><tr>
<td class=rand><?=$text_VoteBcMaxFreq?></td>
<td class=rand colspan=2><input type="text" class="textfield" name="vote" size="20" maxlength="20" value="<?=$dataarray['VF']?>" title="">&nbsp;<input type="submit" class="button" name="Submit" value="SetVBMF"></td>
<td class=rand colspan=2><?=$text_terrorcount?>&nbsp;<input type="text" class="textfield" name="newtc" size="5" maxlength="5" value="<?=$dataarray['H2']?>" title=";<?=$text_terrorcount?>"></td>
<td class=randende><input type="submit" class="button" name="Submit" value="SetTC"></td>
</tr><tr>
<td class=rand ><?=$text_CamFirstP?></td>
<td class=rand><a class=nav href="server_admin.php?Submit=optionswitch&type=CamFirstPerson&old=<?=$dataarray['C1']?>"><?=$option_switchonoff[$dataarray['C1']]?></a></td>
<td class=rand><?=$text_Cam3rdP?></td>
<td class=rand><a class=nav href="server_admin.php?Submit=optionswitch&type=CamThirdPerson&old=<?=$dataarray['C3']?>"><?=$option_switchonoff[$dataarray['C3']]?></a></td>
<td class=rand><?=$text_CamFree3rdP?></td>
<td class=randende><b><a class=nav href="server_admin.php?Submit=optionswitch&type=CamFreeThirdP&old=<?=$dataarray['CP']?>"><?=$option_switchonoff[$dataarray['CP']]?></a></td>
</tr><tr>
<td class=rand><?=$text_CamGhost?></td>
<td class=rand><a class=nav href="server_admin.php?Submit=optionswitch&type=CamGhost&old=<?=$dataarray['CG']?>"><?=$option_switchonoff[$dataarray['CG']]?></a></td>
<td class=rand><?=$text_CamFadeToBlack?></td>
<td class=rand><a class=nav href="server_admin.php?Submit=optionswitch&type=CamFadeToBlack&old=<?=$dataarray['CF']?>"><?=$option_switchonoff[$dataarray['CF']]?></a></td>
<td class=rand><?=$text_CamTeamOnly?></td>
<td class=randende><a class=nav href="server_admin.php?Submit=optionswitch&type=CamTeamOnly&old=<?=$dataarray['CT']?>"><?=$option_switchonoff[$dataarray['CT']]?></a></td>
</tr><tr>
<td class=rand><?=$text_ffpw?></td>
<td class=rand><a class=nav href="server_admin.php?Submit=optionswitch&type=ForceFPersonWeapon&old=<?=$dataarray['K2']?>"><?=$option_switchonoff[$dataarray['K2']]?></a></td>
<td class=rand><?=$text_ff?></td>
<td class=rand><a class=nav href="server_admin.php?Submit=optionswitch&type=FriendlyFire&old=<?=$dataarray['Y1']?>"><?=$option_switchonoff[$dataarray['Y1']]?></td>
<td class=rand><?=$text_rotatemap?></td>
<td class=randende><a class=nav href="server_admin.php?Submit=optionswitch&type=RotateMap&old=<?=$dataarray['J2']?>"><?=$option_switchonoff[$dataarray['J2']]?></td>
</tr><tr>
<td class=rand><?=$text_autoteam?></td>
<td class=rand><a class=nav href="server_admin.php?Submit=optionswitch&type=Autobalance&old=<?=$dataarray['Z1']?>"><?=$option_switchonoff[$dataarray['Z1']]?></td>
<td class=rand><?=$text_radar?></td>
<td class=rand><a class=nav href="server_admin.php?Submit=optionswitch&type=AllowRadar&old=<?=$dataarray['B2']?>"><?=$option_switchonoff[$dataarray['B2']]?></td>
<td class=rand><?=$text_aiback?></td>
<td class=randende><a class=nav href="server_admin.php?Submit=optionswitch&type=AIBkp&old=<?=$dataarray['I2']?>"><?=$option_switchonoff[$dataarray['I2']]?></td>
</tr><tr>
<td class=rand><?=$text_penalty?></td>
<td class=rand><a class=nav href="server_admin.php?Submit=optionswitch&type=TeamKillerPenalty&old=<?=$dataarray['A2']?>"><?=$option_switchonoff[$dataarray['A2']]?></td>
<td class=rand><?=$text_teamnames?></td>
<td class=rand><a class=nav href="server_admin.php?Submit=optionswitch&type=ShowNames&old=<?=$dataarray['W1']?>"><?=$option_switchonoff[$dataarray['W1']]?></td>
<td class=rand><?=$text_dedi?></td>
<td class=randende><?=$text_yn[$dataarray['H1']]?></td>
</tr><tr>
<td class=rand><?=$text_DiffLevel?></td>
<td class=rand colspan=3><?=$diff[$dataarray['DL']]?>&nbsp;=><b>&nbsp;<a class=nav href="server_admin.php?Submit=diffset&diffset=1"><?=$diff['1']?></a>&nbsp;&nbsp;<a class=nav href="server_admin.php?Submit=diffset&diffset=2"><?=$diff['2']?></a>&nbsp;&nbsp;<a class=nav href="server_admin.php?Submit=diffset&diffset=3"><?=$diff['3']?></a></td>
<td class=rand>IRCBot</td>
<td class=randende><?=$text_yn[$dataarray['IR']]?></td>
</tr></form>
<?php
if ($dataarray['EV']>=11)
{
echo "<td class=header background=\"images/$design[$dset]_header.gif\" align=center colspan=6>";
if ($dataarray['EV']>=12)
{
echo "<a class=nav2 href=\"server_admin.php?Submit=Messenger\" title=\"Messenger on/off\"><b>Messenger:&nbsp;".$text_yn[$dataarray['ME']]."</b></a></td></tr><tr>";
}
else
{
echo "<b>Messenger:&nbsp;".$text_yn[$dataarray['ME']]."</b></td></tr><tr>";
}
if ($dataarray['ME']==1)
{
if ($dataarray['EV']>=12)
{
echo "<form><td class=randende colspan=6 align=center>";
echo "<input type=text class=textfield name=messtext0 size=100 maxlength=100 value=\"".$dataarray['TA']."\" title=\"Messenger Text 0\">&nbsp;&nbsp;";
echo "<input type=submit class=button name=Submit value=SetText0></td>";
echo "</form></tr><tr><form>";
echo "<td class=randende colspan=6 align=center>";
echo "<input type=text class=textfield name=messtext1 size=100 maxlength=100 value=\"".$dataarray['TB']."\" title=\"Messenger Text 1\">&nbsp;&nbsp;";
echo "<input type=submit class=button name=Submit value=SetText1></td>";
echo "</form></tr><tr><form>";
echo "<td class=randende colspan=6 align=center>";
echo "<input type=text class=textfield name=messtext2 size=100 maxlength=100 value=\"".$dataarray['TC']."\" title=\"Messenger Text 2\">&nbsp;&nbsp;";
echo "<input type=submit class=button name=Submit value=SetText2></td>";
echo "</form></tr>";
}
else
{
echo "<td class=randende colspan=6 align=center>".$dataarray['TA']."<br>".$dataarray['TB']."<br>".$dataarray['TC']."</td></tr><tr>";
}
}
}
echo "</table>";
?>
<table width="<?=$swidth?>" border=0 cellspacing=0 class=frame>
<tr>
<td colspan=2 class=bigheader background="images/<?=$design[$dset]?>_header.gif">&nbsp;<?=$text_playerinfo?></td>
</tr>
<?php
if ($GameModeTranslate[$dataarray['F1']]=="pilot" or $GameModeTranslate[$dataarray['F1']]=="teamsurvival" or $GameModeTranslate[$dataarray['F1']]=="bomb" or $GameModeTranslate[$dataarray['F1']]=="hostage" or $GameModeTranslate[$dataarray['F1']]=="terroristhuntadvmode" or $GameModeTranslate[$dataarray['F1']]=="scatteredhuntadvmode" or $GameModeTranslate[$dataarray['F1']]=="capturetheenemymode" or $GameModeTranslate[$dataarray['F1']]=="countdownmode" or $GameModeTranslate[$dataarray['F1']]=="kamikazemode")
{
?>
<tr>
<td align=center class=green width="50%"><?=$text_greenscore?>: <?=$TeamScore['0']?></td>
<td align=center class=red width="50%"><?=$text_redscore?>: <?=$TeamScore['1']?></td>
</tr>
<?php
}
?>
</table>
<table width="<?=$swidth?>" border=0 cellspacing=0 class=frame>
<tr>
<td class=header background="images/<?=$design[$dset]?>_middle.gif" width="19"></td>
<td class=header background="images/<?=$design[$dset]?>_middle.gif" align=center>&nbsp;<b><?=$text_playername?></td>
<td class=header background="images/<?=$design[$dset]?>_middle.gif" align=center>&nbsp;<b>UBI</td>
<td class=header background="images/<?=$design[$dset]?>_middle.gif" align=center><b>Fly?</td>
<td class=header background="images/<?=$design[$dset]?>_middle.gif" width="30" align=center><b><?=$text_kills?></td>
<td class=header background="images/<?=$design[$dset]?>_middle.gif" width="30" align=center><b><?=$text_deaths?></td>
<td class=header background="images/<?=$design[$dset]?>_middle.gif" width="60" align=center><b><?=$text_timeos?></td>
<td class=header background="images/<?=$design[$dset]?>_middle.gif" width="60" align=center><b><?=$text_pingts?></td>
</tr>
<?php

if ($PlayerList['0'])
{
$sortcount=0;
foreach ($PlayerList as $item)
 {$playersorted[]=$InTeam[$sortcount]." �".htmlentities($PlayerList[$sortcount])." �".$PlayerKills[$sortcount]." �".$PlayerTime[$sortcount]." �".$PlayerPing[$sortcount]." �".$Deaths[$sortcount]." �".$Alive[$sortcount]." �".$Ubi[$sortcount]." �".$PWpn[$sortcount]." �".$SWpn[$sortcount]." �".$PWpnG[$sortcount]." �".$SWpnG[$sortcount]." �".$Hits[$sortcount]." �".$Fired[$sortcount]." �".$Acc[$sortcount]." �".$JoinedLate[$sortcount];
 $sortcount++;}
asort($playersorted);
foreach ($playersorted as $item)
 {$ausgabe=explode(" �",$item);
 $color="rand";

if ($ausgabe['0']=="4"){$color="spec";$ausgabe['6']=3;}
if ($ausgabe['0']=="0"){$color="black";$ausgabe['6']=3;}
if ($GameModeTranslate[$dataarray['F1']]=="pilot" or $GameModeTranslate[$dataarray['F1']]=="teamsurvival" or $GameModeTranslate[$dataarray['F1']]=="bomb" or $GameModeTranslate[$dataarray['F1']]=="hostage" or $GameModeTranslate[$dataarray['F1']]=="terroristhuntadvmode" or $GameModeTranslate[$dataarray['F1']]=="scatteredhuntadvmode" or $GameModeTranslate[$dataarray['F1']]=="capturetheenemymode" or $GameModeTranslate[$dataarray['F1']]=="countdownmode" or $GameModeTranslate[$dataarray['F1']]=="kamikazemode")
{
if ($ausgabe['0']=="3") {$color="red";}
if ($ausgabe['0']=="2") {$color="green";}
}

$playerlink="playerdetail.php?nick=".htmlspecialchars($ausgabe['1'])."&Ubi=".htmlspecialchars($ausgabe['7'])."&PWpn=".$ausgabe['8']."&SWpn=".$ausgabe['9']."&PWpnG=".$ausgabe['10']."&SWpnG=".$ausgabe['11']."&Hits=".$ausgabe['12']."&Fired=".$ausgabe['13']."&Kills=".$ausgabe['2']."&Deaths=".$ausgabe['5']."&Acc=".$ausgabe['14'];
$ladderlink="ubiladder.php?ubi=".$ausgabe['7'];
if ($ausgabe['15']==1){$ausgabe['6']=3;}

$tonserver=explode(':',$ausgabe['3']);
$tonserver['2']=(int)($tonserver['0']/60);
$tonserver['0']=$tonserver['0']-($tonserver['2']*60);
?>
<tr>
<td class=<?=$color?>>&nbsp;<img src="images/<?=$pic_Alive[$ausgabe['6']]?>"></td>
<td class=<?=$color?>><a class=nav href="server_admin.php?Submit=SendPBC&pbc=PB_SV_GetSs&pbadd64=<?=base64_encode($ausgabe['1'])?>"><img src="images/sshot.gif" border="0">&nbsp;<?=$ausgabe['1']?></a></td>
<td class=<?=$color?> align=left>&nbsp;<?=$ausgabe['7']?></td>
<td class=<?=$color?> align=center width="40">&nbsp;<a class=nav href="server_admin.php?Submit=Kick&ubi=<?=$ausgabe['7']?>">Kick</a>&nbsp;|&nbsp;<a class=nav href="server_admin.php?Submit=Ban&ubi=<?=$ausgabe['7']?>">Ban</a>&nbsp;</td>
<td class=<?=$color?> align=center>&nbsp;<?=$ausgabe['2']?></td>
<td class=<?=$color?> align=center>&nbsp;<?=$ausgabe['5']?></td>
<td class=<?=$color?> width="60" align=center><?=sprintf("%02u",$tonserver['2'])?>:<?=sprintf("%02u",$tonserver['0'])?>:<?=sprintf("%02u",$tonserver['1'])?></td>
<td class=<?=$color?> width="60" align=center><?=sprintf("%04u",$ausgabe['4'])?> ms</td>
</tr>
<?php
}
}
else {echo "<tr><td class=rand colspan=8 align=center>".$text_npo."</td></tr>";}
?>
</table>
<table width="<?=$swidth?>" border=0 cellspacing=0 class=frame>
<tr>
<td colspan=6 class=bigheader background="images/<?=$design[$dset]?>_header.gif">&nbsp;<?=$text_maplist?></td>
</tr><tr>
<td class=header background="images/<?=$design[$dset]?>_middle.gif" width="15">&nbsp;<b><?=$text_number?></td>
<td class=header background="images/<?=$design[$dset]?>_middle.gif">&nbsp;<b><?=$text_map?></td>
<td class=header background="images/<?=$design[$dset]?>_middle.gif">&nbsp;<b><?=$text_gametype?></td>
<td class=header background="images/<?=$design[$dset]?>_middle.gif">&nbsp;<b>Go Map</td>
<td class=header background="images/<?=$design[$dset]?>_middle.gif">&nbsp;<b>Delete Map</td>
<td class=header background="images/<?=$design[$dset]?>_middle.gif">&nbsp;<b>Insert Map</td>
</tr>
<?php
$counter =0;
foreach ($MapCycle as $item)
{
if ($counter<>0){
echo"<tr><td class=rand>&nbsp;".$counter."</td><td class=rand>&nbsp;";
if ($dataarray['EV']>=11 and $counter==($dataarray['MN']+1)) {echo "<i>";}
?>
<?=$item?></td>
<td class=rand>&nbsp;<?=TranslateBeaconGameModeToText($GameTypelist[$counter])?></td>
<td class=rand width="60"><b><img src="images/clear.gif" width=10 height=10 name="maps<?=$hitx?>go"><a class=nav href="server_admin.php?Submit=Map&map=<?=$counter?>&mapt=<?=$item?>" onMouseOver="mi('maps<?=$hitx?>go')" onMouseOut="mo('maps<?=$hitx?>go')">Go Map</a></td>
<td class=rand width="85"><b><img src="images/clear.gif" width=10 height=10 name="maps<?=$hitx?>del"><a class=nav href="server_admin.php?Submit=RemoveMap&map=<?=$counter?>&mapt=<?=$item?>" onMouseOver="mi('maps<?=$hitx?>del')" onMouseOut="mo('maps<?=$hitx?>del')">Delete Map</a></td>
<td class=randende width="80"><b><img src="images/clear.gif" width=10 height=10 name="maps<?=$hitx?>ins"><a class=nav href="server_admin.php?Submit=InsertMap&map=<?=$counter?>" onMouseOver="mi('maps<?=$hitx?>ins')" onMouseOut="mo('maps<?=$hitx?>ins')">Insert Map</a></td>
</tr>
<?php
$hitx++;
}
$counter++;
}

?>
<tr>
<td class=rand colspan=4>&nbsp;</td>
<?php
if ($counter > "3")
{
?>
<td class=rand width="85"><b><img src="images/clear.gif" width=10 height=10 name="maps<?=$hitx?>del"><a class=nav href="server_admin.php?Submit=RemoveMapsToEnd&map=<?=$counter-1?>" onMouseOver="mi('maps<?=$hitx?>del')" onMouseOut="mo('maps<?=$hitx?>del')">Del.M.2-<?=$counter-1?></a></td>
<?php
}
else
{ echo "<td class=rand width=85>&nbsp;</td>";}
if ($counter<33)
{
?>
<td class=randende width="80"><b><img src="images/clear.gif" width=10 height=10 name="maps<?=$hitx?>ins"><a class=nav href="server_admin.php?Submit=InsertMap&map=<?=$counter?>" onMouseOver="mi('maps<?=$hitx?>ins')" onMouseOut="mo('maps<?=$hitx?>ins')">Insert Map</a></td>

</tr>
<?php
$hitx++;
}
else {echo "<td class=randende>&nbsp;</td>";}
?>
</table>
<br><input type="submit" class="button" name="Submit" value="Logoff"><br>(Cookies cleared)<br>
<br><input type="submit" class="button" name="Submit" value="Leave"><br>(Cookies stays)
<?=Copyrightext()?></center></form></body></html>
<?php }}}
else
{
if ($beaconerror==True){
?>
<br>Error: No Responce from Server!<br>
<br><br><a class=nav href="serverliste.php"><?=$text_backtolist?></a>
<?php
}
else
{
?>
<table border=0 cellpadding=0 cellspacing=0 width="200">
<tr><td width="100%" class="tabfarbe-3">
<table border=0 cellspacing="1" width="200">
<tr><td align=center class="tabfarbe-5" width="10%"><font class=headers><?=$text_password?></font></td></tr>
<tr><td align=center class="tabfarbe-5" width="10%"><input type="password" name="pw" size="30" maxlength="30"  class="editbox" title="Passwort"></td></tr>
</table></table><br>
<input type="hidden" name="ip" value="<?=$ip?>">
<input type="hidden" name="port" value="<?=$port?>">
<input type="hidden" name="Submit" value="Login">
<input type="submit" name="Submit" value="Login" class="button">
<br><br><a class=nav href="server.php?ip=<?=$ip?>&beaconport=<?=$port?>"><?=$text_back?></a>
<?php } ?>
<?=Copyrightext()?></center></form></body></html>
<?php } ?>


