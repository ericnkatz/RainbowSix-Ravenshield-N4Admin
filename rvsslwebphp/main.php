<?php
// Script (C)opyright 2003 by =TSAF=Muschel
// Released under GNU GENERAL PUBLIC LICENSE
// www.tsaf.de , muschel@tsaf.de
error_reporting(2047);
foreach ($_GET as $key => $value)
{
	${$key}=$value;
}

require("config.inc.php");
ConnectTheDBandGetDefaults();

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
// -->
</script>
<?php
echo "<table border=0 cellspacing=0 width=".$swidth."><tr><td align=left class=bigheader background=\"images/".$design[$dset]."_header.gif\">";
ShowFlags("main.php","");

echo $text_maintitle."</td></tr><tr><td><hr></td></tr></table>";

echo "<table border=0 cellspacing=0 width=".$swidth."><tr><td align=center class=bigheader colspan=2 background=\"images/".$design[$dset]."_header.gif\">".$text_mainmenu."</td></tr>";

echo "<tr><td class=randende align=center><a class=nav href=\"playerstats.php\"><font size=4>Playerstats</font></a></td></tr>";
echo "<tr><td class=randende align=center><a class=nav href=\"serverliste.php\"><font size=4>Serverlist</font></a></td></tr>";
echo "<tr><td class=randende align=center><a class=nav href=\"admin.php\"><font size=4>Administration</font></a></td></tr>";
echo "</table>";

?>
<?=Copyrightextmain()?></form></center></body>
