<?php
// Script (C)opyright 2003 by =TSAF=Muschel
// Released under GNU GENERAL PUBLIC LICENSE
// www.tsaf.de , muschel@tsaf.de
error_reporting(2047);
foreach ($_GET as $key => $value){${$key}=$value;}
if (!isset($Submit)){$Submit="main";}
require("config.inc.php");

$db = mysql_connect($dbHost,$dbUser,$dbPass) or die ("<CENTER>Connect-Error to MySQL! Check $dbHost, $dbUser and $dbPass in config.inc.php!");
mysql_select_db($dbDatabase,$db) or die ("<CENTER>Connect-Error to Database! Check $dbDatabase in config.inc.php!");
$res=mysql_query("SELECT * FROM ".$dbtable2." WHERE id='1'");
$dbrow=mysql_fetch_array($res);
$lset=$dbrow['language'];
$dset=$dbrow['css'];
$customlanguage=$language[$lset];
$css="css/".$design[$dset]."_css.css";

require('language/'.$customlanguage.'.inc.php');
BuildPlayerAndNicksTables();
BuildStatsTablesArray();

$res=mysql_query("SELECT * FROM $dbtable2 WHERE id='1'");
$dbrow = mysql_fetch_array($res);
$adminname=$dbrow['aname'];
$adminpassword= $dbrow['apass'];

$loggedin=False;
$namecookie=crc32($adminname.$adminpassword."serverliste");

if (isset($admin) and isset($pw))
{
	if ($admin==$adminname and $pw==$adminpassword)
	{
		setcookie ("RVSServerliste", "$namecookie", time()+3600 , "/");$loggedin=True;}
	}
	if ($Submit=="Logoff")
	{
		setcookie ("RVSServerliste", "", time()-3600,"/");}
	else
	{
		if (isset($HTTP_COOKIE_VARS["RVSServerliste"]))
		{
			$usrcookie=$HTTP_COOKIE_VARS["RVSServerliste"];
		}
		else 
		{
			$usrcookie="";
		}
		if ($usrcookie==$namecookie)
		{
		$loggedin=True;
		}
	}

	if($loggedin==True)
	{
		require("header.php");
		if ($Submit)
		{

?>
<script language="javascript">
<!--
if (document.images) { on = new Image(); on.src = "images/indicator.gif"; off = new Image(); off.src ="images/clear.gif"; }
function mi(n) { if (document.images) {document[n].src = eval("on.src");}}
function mo(n) { if (document.images) {document[n].src = eval("off.src");}}
// -->
</script>
<link rel="stylesheet" type="text/css" href="<?=$css?>">
<body class=body><center>
<table border=0 cellspacing=0 width="<?=$awidth?>"><form name="listadm" action="admin.php">
<tr><td align=center class=bigheader background="images/<?=$design[$dset]?>_header.gif"><b>Raven-Shield <?=$text_serverlist?> Admin</b></td>
</tr><tr><td><hr></td></tr></table>
<?php

		$res=mysql_query("SELECT * FROM $dbtable1 ORDER BY sort");
		$serveranzahl=mysql_num_rows($res);
		$q=0;
		while ($dbrow = mysql_fetch_array($res))
		{
			$listdata[$q]['id']= $dbrow['id'];
			$listdata[$q]['ip']= $dbrow['ip'];
			$listdata[$q]['bp']= $dbrow['bp'];
			$listdata[$q]['sort']= $dbrow['sort'];
			$listdata[$q++]['text']= $dbrow['text'];
		}

		$res=mysql_query("SELECT * FROM $dbtable3 ORDER by map");
		$linkanzahl=mysql_num_rows($res);
		$q=0;
		while($dbrow = mysql_fetch_array($res))
		{
			$mapdata[$q]['id']= $dbrow['id'];
			$mapdata[$q]['map']= $dbrow['map'];
			$mapdata[$q++]['link']= $dbrow['link'];
		}

		$res=mysql_query("SELECT * FROM ".$dbtable4."ServerIdentsNames");
		$identanzahl=mysql_num_rows($res);
		$q=0;
		while($dbrow = mysql_fetch_array($res))
		{
			$identdata[$q]['id']= $dbrow['id'];
			$identdata[$q]['ident']= $dbrow['serverident'];
			$identdata[$q++]['name']= $dbrow['servername'];
			$allidents[]=$dbrow['serverident'];
		}

		switch ($Submit)
		{
			case "Login";
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=admin.php\">";
				break;

			case "Change Serverlist Entry";
				$wahl=mysql_query("UPDATE $dbtable1 SET ip='$ipneu',bp='$bpneu',sort='$sortneu',text='$textneu' WHERE id='$isneu'");
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=admin.php?Submit=serverlist\">";
				break;

			case "Change Link-Entry";
				$wahl=mysql_query("UPDATE $dbtable3 SET map='$mapneu',link='$linkneu' WHERE id='$linkid'");
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=admin.php?Submit=dllinks\">";
				break;

			case "Change Ident-Entry";
				if (in_array($sidentneu,$allidents) and $identold!=$sidentneu)
				{
					echo "Serverident allready in Database, canceled!<br><br>";
					echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"2; url=admin.php?Submit=serverident\">";
				}
				else
				{
					if ($identold!=$sidentneu)
					{
						echo "Updating Playertable for new Serverident!<br><br>";
						$wahl=mysql_query("UPDATE $Playertable SET serverident='$sidentneu' WHERE serverident='$identold'");
					}
					$wahl=mysql_query("UPDATE ".$dbtable4."ServerIdentsNames SET serverident='$sidentneu',servername='$snameneu' WHERE id='$identid'");
					echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=admin.php?Submit=serverident\">";
				}
				break;

			case "New Serverlist Entry";
				$sql="INSERT INTO $dbtable1 VALUES('','$ipneu','$bpneu','$sortneu','$textneu')";
				$result=mysql_query($sql);
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=admin.php?Submit=serverlist\">";
				break;

			case "New Ident";
				if (isset($allidents))
				{
					if (in_array($sidentneu,$allidents))
					{
						echo "Serverident allready in Database, canceled!<br><br>";
						echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"2; url=admin.php?Submit=serverident\">";
					}
					else
					{
						$sql="INSERT INTO ".$dbtable4."ServerIdentsNames VALUES('','$sidentneu','$snameneu')";
						$result=mysql_query($sql);
						echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=admin.php?Submit=serverident\">";
					}
				}
				else
				{
					$sql="INSERT INTO ".$dbtable4."ServerIdentsNames VALUES('','$sidentneu','$snameneu')";
					$result=mysql_query($sql);
					echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=admin.php?Submit=serverident\">";
				}
				break;

			case "New Link";
				$sql="INSERT INTO $dbtable3 VALUES('','$mapneu','$linkneu')";
				$result=mysql_query($sql);
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=admin.php?Submit=dllinks\">";
				break;

			case "deletelistip";
				mysql_query("DELETE FROM $dbtable1 WHERE id='$isneu'");
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=admin.php?Submit=serverlist\">";
				break;

			case "deletelink";
				mysql_query("DELETE FROM $dbtable3 WHERE id='$lneu'");
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=admin.php?Submit=dllinks\">";
				break;

			case "deleteident";
				echo "Warning!<br>This will delete all Player and Stats from the Ident: <b>".$delidentname."</b> !<br><br>";
				echo "<a class=nav href=\"admin.php?Submit=deleteidentconfirmed&delident=".$delident."&delidentname=".$delidentname."\">>>> Confirm delete Ident '".$delidentname."' <<<</a><br><br>";
				echo "<a class=nav href=\"admin.php?Submit=serverident\">>>> CANCEL <<<</a><br><br>";
				break;

			case "deleteidentconfirmed";
				echo "Deleting Ident and Player+Stats of it, please wait!<br><br>";

				$look="SELECT id FROM ".$Playertable." WHERE serverident='".$delidentname."'";
				$res=mysql_query($look);

				while ($dbrow=mysql_fetch_array($res))
				{
					$delid=$dbrow['id'];
					foreach ($Statstable as $statstbl)
					{
						$deletestats="DELETE FROM ".$statstbl." WHERE fromid='".$delid."'";
						mysql_query($deletestats);
						$deletenicks="DELETE FROM ".$Nicktable." WHERE fromid='".$delid."'";
						mysql_query($deletenicks);
					}
				}
				$deleteplayers="DELETE FROM ".$Playertable." WHERE serverident='".$delidentname."'";
				mysql_query($deleteplayers);
				mysql_query("DELETE FROM ".$dbtable4."ServerIdentsNames WHERE id='".$delident."'");
				mysql_query("DELETE FROM ".$dbtable6."Update");
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=admin.php?Submit=serverident\">";
				break;

			case "Cancel Edit Links";
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=admin.php?Submit=dllinks\">";
				break;

			case "Cancel Edit Serverlist";
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=admin.php?Submit=serverlist\">";
				break;

			case "Cancel Admin Config";
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=admin.php?Submit=main\">";
				break;

			case "Cancel Edit Ident";
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=admin.php?Submit=serverident\">";
				break;

			case "language";
				$wahl = mysql_query("UPDATE $dbtable2 SET language='$lan' WHERE id='1'");
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=admin.php?Submit=main\">";
				break;

			case "design";
				$wahl = mysql_query("UPDATE $dbtable2 SET css='$deset' WHERE id='1'");
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=admin.php?Submit=main\">";
				break;

			case "SetAdmin";
				$wahl=mysql_query("UPDATE $dbtable2 SET aname='$nameneu',apass='$passneu' WHERE id='1'");
				$namecook=crc32($nameneu.$passneu."serverliste");
				setcookie ("RVSServerliste", "$namecook", time()+3600 , "/");
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; url=admin.php?Submit=main\">";
				break;

			case "forceladderupdate";
				echo "<br>Ladders forces to Update!<br><br>";
				mysql_query("DELETE FROM ".$dbtable6."Update");
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"1; url=admin.php?Submit=main\">";
				break;

			case "main";
				echo "<table border=0 cellspacing=0 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=bigheader background=\"images/".$design[$dset]."_header.gif\">";
				echo "<b>Main</b>";
				echo "</td></tr><tr>";
				echo "<td align=center class=randende>";
				echo "<a class=nav href=\"admin.php?Submit=serverlist\">Serverlist IP-Config</a>";
				echo "</td></tr><tr>";
				echo "<td align=center class=randende>";
				echo "<a class=nav href=\"admin.php?Submit=dllinks\">Map Downloadlinks-Config</a>";
				echo "</td></tr><tr>";
				echo "<td align=center class=randende>";
				echo "<a class=nav href=\"admin.php?Submit=serverident\">Stats Serverident-Config</a>";
				echo "</td></tr></table><br>";
				echo "<table border=0 cellpadding=0 cellspacing=0 width=500><tr>";
				echo "<td align=center class=bigheader background=\"images/".$design[$dset]."_header.gif\" width=\"10%\"><b>Language</b></td>";
				echo "<td align=center class=bigheader background=\"images/".$design[$dset]."_header.gif\" width=\"10%\"><b>Design</b></td>";
				echo "<td align=center class=bigheader background=\"images/".$design[$dset]."_header.gif\" width=\"10%\"><b>Admin</b></td>";
				echo "</tr><tr><td align=center class=rand width=\"10%\">";
				$counter=0;
				foreach ($language as $item)
				{
					echo "<img src=\"images/clear.gif\" width=10 height=10 name=\"lang$counter\"><a class=nav href=\"admin.php?lan=".$counter."&Submit=language\" onMouseOver=\"mi('lang$counter')\" onMouseOut=\"mo('lang$counter')\"><b>".$item."</a><br>";
					$counter++;
				}
				echo "</td><td align=\"center\" class=rand width=\"10%\">";
				$counter=0;
				foreach ($design as $item)
				{
					echo "<img src=\"images/clear.gif\" width=10 height=10 name=\"design$counter\"><a class=nav href=\"admin.php?deset=".$counter."&Submit=design\" onMouseOver=\"mi('design$counter')\" onMouseOut=\"mo('design$counter')\"><b>".$item."</a><br>";
					$counter++;
				}
				echo "</td>";
				echo "<td align=center class=randende width=\"10%\"><img src=\"images/clear.gif\" width=10 height=10 name=\"admconf\"><a class=nav href=\"admin.php?Submit=adminconfig\" onMouseOver=\"mi('admconf')\" onMouseOut=\"mo('admconf')\"><b>Config-Login</a><br>";
				echo "<img src=\"images/clear.gif\" width=10 height=10 name=\"screens\"><a class=nav href=\"screenscheck.php\" target=\"_blank\" onMouseOver=\"mi('screens')\" onMouseOut=\"mo('screens')\"><b>Mapimages-Check</a><br>";
				echo "<img src=\"images/clear.gif\" width=10 height=10 name=\"force\"><a class=nav href=\"admin.php?Submit=forceladderupdate\" onMouseOver=\"mi('force')\" onMouseOut=\"mo('force')\"><b>Force Ladderupdate</a></td>";
				break;

			case "serverlist";
				echo "<table border=0 cellspacing=0 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-3>";
				echo "<table border=0 cellspacing=1 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-5></td>";
				echo "<td align=center class=tabfarbe-5 width=\"10%\">";
				echo "<font class=headers>Server IP</font></td>";
				echo "<td align=center class=tabfarbe-5 width=\"10%\">";
				echo "<font class=headers>Server Beacon Port</font></td>";
				echo "<td align=center class=tabfarbe-5 width=\"10%\">";
				echo "<font class=headers>".$text_displayorder."</font></td>";
				echo "<td align=center class=tabfarbe-5>";
				echo "<font class=headers>".$text_comment."</font></td>";
				echo "<td align=center class=tabfarbe-5></td>";
				echo "</tr>";
				if (isset($listdata))
				{
					foreach ($listdata as $listarraykey =>$listentry)
					{
						echo "<tr>";
						echo "<td align=center class=tabfarbe-5><img src=\"images/clear.gif\" width=10 height=10 name= \"edit".$listentry['id']."\">";
						echo "<a class=nav href=\"admin.php?Submit=listedit&pedit=".$listarraykey."\" onMouseOver=\"mi('edit".$listentry['id']."')\" onMouseOut=\"mo('edit".$listentry['id']."')\">";
						echo "<b>".$text_edit."</a></td>";
						echo "<td align=center class=tabfarbe-5><font class=normal>".$listentry['ip']."</font></td>";
						echo "<td align=center class=tabfarbe-5><font class=normal>".$listentry['bp']."</font></td>";
						echo "<td align=center class=tabfarbe-5><font class=normal>".$listentry['sort']."</font></td>";
						echo "<td align=center class=tabfarbe-5><font class=normal>".$listentry['text']."</font></td>";
						echo "<td align=center class=tabfarbe-5><img src=\"images/clear.gif\" width=10 height=10 name=\"del".$listentry['id']."\">";
						echo "<a class=nav href=\"admin.php?Submit=deletelistip&isneu=".$listentry['id']."\" onMouseOver=\"mi('del".$listentry['id']."')\" onMouseOut=\"mo('del".$listentry['id']."')\">";
						echo "<b>".$text_delete."</a></td>";
						echo "</tr>";
					}
				}
				echo "</table></table>";
				echo "<table border=0 cellspacing=0 width=\"".$awidth."\" text=\"#FFFFFF\">";
				echo "<tr><td><hr></td></tr></table>";
				echo "<table border=0 cellpadding=0 cellspacing=0 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td width=\"100%\" class=tabfarbe-3>";
				echo "<table border=0 cellspacing=1 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-5 width=\"10%\"><font class=headers>Server IP</font></td>";
				echo "<td align=center class=tabfarbe-5 width=\"10%\"><font class=headers>Server Beacon Port</font></td>";
				echo "<td align=center class=tabfarbe-5 width=\"10%\"><font class=headers><?=$text_displayorder?></font></td>";
				echo "<td align=center class=tabfarbe-5><font class=headers>".$text_comment."</font></td>";
				echo "</tr><tr>";
				echo "<td align=center class=tabfarbe-5>";
				echo "<input class=textfield type=text name=ipneu size=15 maxlength=100 value=\"0.0.0.0\" class=editbox title=\"New Server큦 IP\"></td>";
				echo "<td align=center class=tabfarbe-5>";
				echo "<input class=textfield type=text name=bpneu size=5 maxlength=5 value=\"8777\" class=editbox title=\"New Server큦 ServerBeaconPort\"></td>";
				echo "<td align=center class=tabfarbe-5>";
				echo "<input class=textfield type=text name=sortneu size=4 maxlength=4 value=\"1\" class=editbox title=\|New Server큦 Display-Order\"></td>";
				echo "<td align=center class=tabfarbe-5>";
				echo "<input class=textfield type=text name=textneu size=40 maxlength=40 value=\"".$text_comment."\" class=editbox title=\"New Server큦 Comment\"></td>";
				echo "</tr><tr>";
				echo "<td align=center class=tabfarbe-5 colspan=4><br>";
				echo "<input type=\"submit\" name=\"Submit\" value=\"New Serverlist Entry\" class=\"button\"><br>&nbsp;</td>";
				echo "</tr></table><tr><td><hr></td></tr></table>";
				echo "<table border=0 cellpadding=0 cellspacing=0 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td width=\"100%\" class=tabfarbe-3>";
				echo "<table border=0 cellspacing=1 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-5 width=\"10%\">";
				echo "<img src=\"images/clear.gif\" width=10 height=10 name=\"switch\">";
				echo "<a class=nav href=\"admin.php?Submit=main\" onMouseOver=\"mi('switch')\" onMouseOut=\"mo('switch')\"><b>Back to Main</b></a>";
				echo "</td></tr></table></table><br>";
				break;

			case "listedit";

				echo "<table border=0 cellspacing=0 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-3>";
				echo "<table border=0 cellspacing=1 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-5 width=\"10%\"><font class=headers>Server IP</font></td>";
				echo "<td align=center class=tabfarbe-5 width=\"10%\"><font class=headers>Server Beacon Port</font></td>";
				echo "<td align=center class=tabfarbe-5 width=\"10%\"><font class=headers>".$text_displayorder."</font></td>";
				echo "<td align=center class=tabfarbe-5><font class=headers>".$text_comment."</font></td>";
				echo "</tr><tr>";
				echo "<td align=center class=tabfarbe-5><font class=normal>".$listdata[$pedit]['ip']."</font></td>";
				echo "<td align=center class=tabfarbe-5><font class=normal>".$listdata[$pedit]['bp']."</font></td>";
				echo "<td align=center class=tabfarbe-5><font class=normal>".$listdata[$pedit]['sort']."</font></td>";
				echo "<td align=center class=tabfarbe-5><font class=normal>".$listdata[$pedit]['text']."</font></td>";
				echo "</tr></table><tr><td><hr></td></tr></table>";
				echo "<table border=0 cellpadding=0 cellspacing=0 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td width=\"100%\" class=tabfarbe-3>";
				echo "<table border=0 cellspacing=1 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-5 width=\"10%\"><font class=headers>Server IP</font></td>";
				echo "<td align=center class=tabfarbe-5 width=\"10%\"><font class=headers>Server Beacon Port</font></td>";
				echo "<td align=center class=tabfarbe-5 width=\"10%\"><font class=headers>".$text_displayorder."</font></td>";
				echo "<td align=center class=tabfarbe-5><font class=headers>".$text_comment."</font></td>";
				echo "</tr><tr>";
				echo "<input type=hidden name=\"isneu\" value=\"".$listdata[$pedit]['id']."\" class=editbox>";
				echo "<td align=center class=tabfarbe-5><input class=textfield type=text name=\"ipneu\" size=15 maxlength=100 value=\"".$listdata[$pedit]['ip']."\" class=editbox title=\"New IP\"></td>";
				echo "<td align=center class=tabfarbe-5><input class=textfield type=text name=\"bpneu\" size=5 maxlength=5 value=\"".$listdata[$pedit]['bp']."\" class=editbox title=\"New ServerBeaconport\"></td>";
				echo "<td align=center class=tabfarbe-5><input class=textfield type=text name=\"sortneu\" size=4 maxlength=4 value=\"".$listdata[$pedit]['sort']."\" class=editbox title=\"New Display-Order\"></td>";
				echo "<td align=center class=tabfarbe-5><input class=textfield type=text name=\"textneu\" size=40 maxlength=40 value=\"".$listdata[$pedit]['text']."\" class=editbox title=\"New Comment\"></td>";
				echo "</tr><tr>";
				echo "<td align=center class=tabfarbe-5 colspan=4><br><input type=submit name=\"Submit\" value=\"Change Serverlist Entry\" class=button>&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit name=\"Submit\" value=\"Cancel Edit Serverlist\" class=button><br>&nbsp;</td>";
				echo "</tr></table><tr><td><hr></td></tr></table>";
				break;

			case "dllinks";
				echo "<table border=0 cellspacing=0 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-3>";
				echo "<table border=0 cellspacing=1 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-5><font class=normal></font></td>";
				echo "<td align=center class=tabfarbe-5><font class=headers>Mapname</font></td>";
				echo "<td align=center class=tabfarbe-5><font class=headers>Link</font></td>";
				echo "<td align=center class=tabfarbe-5><font class=headers></font></td>";
				echo "</tr>";
				if (isset($mapdata))
				{
					foreach ($mapdata as $maparraykey => $mapentry)
					{
						echo "<tr>";
						echo "<td align=center class=tabfarbe-5><img src=\"images/clear.gif\" width=10 height=10 name=\"editl".$mapentry['id']."\"><a class=nav href=\"admin.php?Submit=editdllinks&ledit=".$maparraykey."\" onMouseOver=\"mi('editl".$mapentry['id']."')\" onMouseOut=\"mo('editl".$mapentry['id']."')\"><b>".$text_edit."</a></td>";
						echo "<td align=center class=tabfarbe-5><font class=normal>".$mapentry['map']."</font></td>";
						echo "<td align=center class=tabfarbe-5><img src=\"images/clear.gif\" width=10 height=10 name=\"link".$mapentry['id']."\"><a class=nav target=\"_blank\" href=\"".$mapentry['link']."\" onMouseOver=\"mi('link".$mapentry['id']."')\" onMouseOut=\"mo('link".$mapentry['id']."')\">".$mapentry['link']."</a></td>";
						echo "<td align=center class=tabfarbe-5><img src=\"images/clear.gif\" width=10 height=10 name=\"dell".$maparraykey."\"><a class=nav href=\"admin.php?Submit=deletelink&lneu=".$mapentry['id']."\" onMouseOver=\"mi('dell".$mapentry['id']."')\" onMouseOut=\"mo('dell".$mapentry['id']."')\"><b>".$text_delete."</a></td>";
						echo "</tr>";
					}
				}
				echo "</table></table>";
				echo "<table border=0 cellspacing=0 width=\"".$awidth."\">";
				echo "<tr><td><hr></td></tr></table>";
				echo "<table border=0 cellpadding=0 cellspacing=0 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td width=\"100%\" class=tabfarbe-3>";
				echo "<table border=0 cellspacing=1 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-5><font class=headers>Mapname</font></td>";
				echo "<td align=center class=tabfarbe-5><font class=headers>Link</font></td>";
				echo "</tr><tr>";
				echo "<td align=center class=tabfarbe-5><input class=textfield type=text name=\"mapneu\" size=25 maxlength=40 value=\"Mapname\" class=editbox title=\"New Maps Name\"></td>";
				echo "<td align=center class=tabfarbe-5><input class=textfield type=text name=\"linkneu\" size=50 maxlength=255 value=\"http://\" class=editbox title=\"New Link to Download\"></td>";
				echo "</tr><tr>";
				echo "<td align=center class=tabfarbe-5 colspan=4><br><input type=submit name=\"Submit\" value=\"New Link\" class=button><br>&nbsp;</td>";
				echo "</tr></table><tr><td><hr></td></tr></table>";
				echo "<table border=0 cellpadding=0 cellspacing=0 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td width=\"100%\" class=tabfarbe-3>";
				echo "<table border=0 cellspacing=1 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-5 width=\"10%\">";
				echo "<img src=\"images/clear.gif\" width=10 height=10 name=\"switch\">";
				echo "<a class=nav href=\"admin.php?Submit=main\" onMouseOver=\"mi('switch')\" onMouseOut=\"mo('switch')\"><b>Back to Main</b></a>";
				echo "</td></tr></table></table><br>";
				break;

			case "editdllinks";
				echo "<table border=0 cellspacing=0 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-3>";
				echo "<table border=0 cellspacing=1 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-5><font class=headers>Mapname</font></td>";
				echo "<td align=center class=tabfarbe-5><font class=headers>Link</font></td>";
				echo "</tr><tr>";
				echo "<td align=center class=tabfarbe-5><font class=normal>".$mapdata[$ledit]['map']."</font></td>";
				echo "<td align=center class=tabfarbe-5><font class=normal>".$mapdata[$ledit]['link']."</font></td>";
				echo "</tr></table>";
				echo "<tr><td><hr></td></tr></table>";
				echo "<table border=0 cellpadding=0 cellspacing=0 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td width=\"100%\" class=tabfarbe-3>";
				echo "<table border=0 cellspacing=1 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-5><font class=headers>Mapname</font></td>";
				echo "<td align=center class=tabfarbe-5><font class=headers>Link</font></td>";
				echo "</tr><tr>";
				echo "<input type=hidden name=\"linkid\" value=\"".$mapdata[$ledit]['id']."\" class=editbox>";
				echo "<td align=center class=tabfarbe-5><input class=textfield type=text name=\"mapneu\" size=25 maxlength=40 value=\"".$mapdata[$ledit]['map']."\" class=editbox title=\"New Maps Name\"></td>";
				echo "<td align=center class=tabfarbe-5><input class=textfield type=text name=\"linkneu\" size=50 maxlength=255 value=\"".$mapdata[$ledit]['link']."\" class=editbox title=\"New Link to Download\"></td>";
				echo "</tr><tr>";
				echo "<td align=center class=tabfarbe-5 colspan=4><br><input type=submit name=\"Submit\" value=\"Change Link-Entry\" class=button>&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit name=\"Submit\" value=\"Cancel Edit Links\" class=button><br>&nbsp;</td>";
				echo "</tr></table><tr><td><hr></td></tr></table>";
				break;

			case "adminconfig";
				echo "<table border=0 cellspacing=0 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-3>";
				echo "<table border=0 cellspacing=1 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-5 width=\"50%\"><font class=headers>Name</font></td>";
				echo "<td align=center class=tabfarbe-5 width=\"50%\"><font class=headers>".$text_password."</font></td>";
				echo "</tr><tr>";
				echo "<td align=center class=tabfarbe-5><font class=normal>".$adminname."</font></td>";
				echo "<td align=center class=tabfarbe-5><font class=normal>".$adminpassword."</font></td>";
				echo "</tr></table><tr><td><hr></td></tr></table>";
				echo "<table border=0 cellpadding=0 cellspacing=0 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td width=\"100%\" class=tabfarbe-3>";
				echo "<table border=0 cellspacing=1 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-5 width=\"50%\"><font class=headers>Name</font></td>";
				echo "<td align=center class=tabfarbe-5 width=\"50%\"><font class=headers>".$text_password."</font></td>";
				echo "</tr><tr>";
				echo "<td align=center class=tabfarbe-5><input class=textfield type=text name=\"nameneu\" size=15 maxlength=15 value=\"".$adminname."\" class=editbox title=\"New Name\"></td>";
				echo "<td align=center class=tabfarbe-5><input class=textfield type=text name=\"passneu\" size=15 maxlength=15 value=\"".$adminpassword."\" class=editbox title=\"New Password\"></td>";
				echo "</tr><tr>";
				echo "<td align=center class=tabfarbe-5 colspan=2>&nbsp;<br><input type=submit name=\"Submit\" value=\"SetAdmin\" class=button>&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit name=\"Submit\" value=\"Cancel Admin Config\" class=button><br>&nbsp;</td>";
				echo "</tr></table><tr><td><hr></td></tr></table>";
				break;

			case "serverident";
				echo "<table border=0 cellspacing=0 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-3>";
				echo "<table border=0 cellspacing=1 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-5></td>";
				echo "<td align=center class=tabfarbe-5><font class=headers>Serverident</font></td>";
				echo "<td align=center class=tabfarbe-5><font class=headers>Servername</font></td>";
				echo "<td align=center class=tabfarbe-5></td>";
				echo "</tr>";
				if (isset($identdata))
				{
					foreach ($identdata as $identarraykey => $idententry)
					{
						echo "<tr>";
						echo "<td align=center class=tabfarbe-5><img src=\"images/clear.gif\" width=10 height=10 name=\"editident".$idententry['id']."\"><a class=nav href=\"admin.php?Submit=editident&identedit=".$identarraykey."\" onMouseOver=\"mi('editident".$idententry['id']."')\" onMouseOut=\"mo('editident".$idententry['id']."')\"><b>".$text_edit."</a></td>";
						echo "<td align=center class=tabfarbe-5><font class=normal>".$idententry['ident']."</font></td>";
						echo "<td align=center class=tabfarbe-5><font class=normal>".$idententry['name']."</a></td>";
						echo "<td align=center class=tabfarbe-5><img src=\"images/clear.gif\" width=10 height=10 name=\"delident".$idententry['id']."\"><a class=nav href=\"admin.php?Submit=deleteident&delident=".$idententry['id']."&delidentname=".$idententry['ident']."\" onMouseOver=\"mi('delident".$idententry['id']."')\" onMouseOut=\"mo('delident".$idententry['id']."')\"><b>".$text_delete."</a></td>";
						echo "</tr>";
					}
				}
				echo "</table><tr><td><hr></td></tr></table>";
				echo "<table border=0 cellpadding=0 cellspacing=0 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td width=\"100%\" class=tabfarbe-3>";
				echo "<table border=0 cellspacing=1 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-5><font class=headers>Serverident</font></td>";
				echo "<td align=center class=tabfarbe-5><font class=headers>Servername</font></td>";
				echo "</tr><tr>";
				echo "<td align=center class=tabfarbe-5><input class=textfield type=text name=\"sidentneu\" size=25 maxlength=20 value=\"Ident\" class=editbox title=\"New Serverident\"></td>";
				echo "<td align=center class=tabfarbe-5><input class=textfield type=text name=\"snameneu\" size=35 maxlength=30 value=\"MyServer\" class=editbox title=\"New Servername\"></td>";
				echo "</tr><tr>";
				echo "<td align=center class=tabfarbe-5 colspan=4><br><input type=submit name=\"Submit\" value=\"New Ident\" class=button><br>&nbsp;</td>";
				echo "</tr></table><tr><td><hr></td></tr></table>";
				echo "<table border=0 cellpadding=0 cellspacing=0 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td width=\"100%\" class=tabfarbe-3>";
				echo "<table border=0 cellspacing=1 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-5 width=\"10%\">";
				echo "<img src=\"images/clear.gif\" width=10 height=10 name=\"switch\">";
				echo "<a class=nav href=\"admin.php?Submit=main\" onMouseOver=\"mi('switch')\" onMouseOut=\"mo('switch')\"><b>Back to Main</b></a>";
				echo "</td></tr></table></table><br>";
				break;

			case "editident";
				echo "<table border=0 cellspacing=0 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-3>";
				echo "<table border=0 cellspacing=1 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-5><font class=headers>Serverident</font></td>";
				echo "<td align=center class=tabfarbe-5><font class=headers>Servername</font></td>";
				echo "</tr><tr>";
				echo "<td align=center class=tabfarbe-5><font class=normal>".$identdata[$identedit]['ident']."</font></td>";
				echo "<td align=center class=tabfarbe-5><font class=normal>".$identdata[$identedit]['name']."</font></td>";
				echo "</tr></table>";
				echo "<tr><td><hr></td></tr></table>";
				echo "<table border=0 cellpadding=0 cellspacing=0 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td width=\"100%\" class=tabfarbe-3>";
				echo "<table border=0 cellspacing=1 width=\"".$awidth."\">";
				echo "<tr>";
				echo "<td align=center class=tabfarbe-5><font class=headers>Serverident</font></td>";
				echo "<td align=center class=tabfarbe-5><font class=headers>Servername</font></td>";
				echo "</tr><tr>";
				echo "<input type=hidden name=\"identid\" value=\"".$identdata[$identedit]['id']."\">";
				echo "<input type=hidden name=\"identold\" value=\"".$identdata[$identedit]['ident']."\">";
				echo "<td align=center class=tabfarbe-5><input class=textfield type=text name=\"sidentneu\" size=25 maxlength=20 value=\"".$identdata[$identedit]['ident']."\" class=editbox title=\"New Serverident\"></td>";
				echo "<td align=center class=tabfarbe-5><input class=textfield type=text name=\"snameneu\" size=35 maxlength=30 value=\"".$identdata[$identedit]['name']."\" class=editbox title=\"New Servername\"></td>";
				echo "</tr><tr>";
				echo "<td align=center class=tabfarbe-5 colspan=4><br><input type=submit name=\"Submit\" value=\"Change Ident-Entry\" class=button>&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit name=\"Submit\" value=\"Cancel Edit Ident\" class=button><br>&nbsp;</td>";
				echo "</tr></table><tr><td><hr></td></tr></table>";
				break;

		}
?>
</tr></table>
<br>
<input type=submit name="Submit" value="Logoff" class=button>
<?=Copyrightextmain()?></center></form></body></html>
<?php
}
}
else{
?>
<link rel="stylesheet" type="text/css" href="<?=$css?>">
<body class=body>
<center>
<table border="0" cellspacing="0" width="<?=$awidth?>"><form name="listadm" action="admin.php">
<tr><td align="center" class="oben" background="images/<?=$design[$dset]?>_header.gif">Raven-Shield <?=$text_serverlist?> Admin-Login</td>
</tr><tr><td><hr></td></tr></table>
<table border="0" cellpadding="0" cellspacing="0" width="200">
<tr><td width="100%" class="tabfarbe-3">
<table border="0" cellspacing="1" width="200">
<tr>
<td align="center" class="tabfarbe-5" width="10%"><font class=headers>Name</font></td>
<td align="center" class="tabfarbe-5" width="10%"><font class=headers><?=$text_password?></font></td>
</tr><tr>
<td align="center" class="tabfarbe-5" width="10%"><input class="textfield" type="text" name="admin" size="15" maxlength="15"  class="editbox" title="Name"></td>
<td align="center" class="tabfarbe-5" width="10%"><input class="textfield" type="password" name="pw" size="15" maxlength="15"  class="editbox" title="Passwort"></td>
</tr></table></table><br>
<input type="submit" name="Submit" value="Login" class="button" >
<br><br>
<a class=nav href="main.php"><?=$text_back?></a>
<?=Copyrightextmain()?></form></body></html>
<?php }?>
