<table>
<tr>
<?php
$line=0;
$mapdir = opendir ("mapimages");
while ($file = readdir ($mapdir))
{
    if ($file != "." && $file != "..")
    {
		echo "<td align=center><img src=\"mapimages/".$file."\"><br>$file\n</td>";
		$line++;
		if ($line=="5")
		{
		$line=0; echo "</tr><tr>";
		}
    }
}
closedir($mapdir);
?>
</tr>

