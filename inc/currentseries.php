<?php
global $wpdb;
$table = MBC_TABLE_PREFIX."mblogicricket";
$sql_stmt = "SELECT * from $table where id=1";
$sqlvals = $wpdb->get_results($sql_stmt);
$a = $sqlvals[0]->apikey;
$b = $sqlvals[0]->fullscpg;
$c = $sqlvals[0]->archievepg;
$styurli = get_site_url();
$tables = $wpdb->prefix . "posts";
$sql_stmts = "SELECT * from $tables where id='$b'";
$sqlvalss = $wpdb->get_results($sql_stmts);
$as = $sqlvalss[0]->post_name;
$sturl = $styurli."/".$as;
$sql_stmtss = "SELECT * from $tables where id='$c'";
$sqlvalsss = $wpdb->get_results($sql_stmtss);
$ass = $sqlvalsss[0]->post_name;
$sturla = $styurli."/".$ass;
$url = "http://cricketapi.mblogi.com/currseriesjson.php?api=".$a;
$json = file_get_contents($url);
$json = json_decode($json,true);
foreach($json as $json)
{
?>
<ul id="accordion">

<?php print "<li class='mbclirch'>".$json[0]['series']."</li><ul>";  ?>
<li><table class="mbcthiy"><thead><th style="width:20%">Date</th><th style="width:40%">Match Details</th><th style="width:40%">Result</th></thead>
<?php
for($i=1;$i<count($json);$i++)
{ ?>
  		<tr> <?php
print "<td>".$json[$i]['date']."</td><td>";
if($json[$i]['teamone'] != null) { echo $json[$i]['teamone']." vs ".$json[$i]['teamtwo']." (".$json[$i]['desc'].")"; } else { echo $json[$i]['desc']; }
echo "</td><td>";
if($json[$i]['result'] != null){ echo  "<a href='".$sturl."?mid=".$json[$i]['mid']."' >".$json[$i]['result']."</a>"; } else { echo "To be played"; }
echo "</td></tr>";
}
?>
</table>
</li>
</ul>
</ul>
<?php
}
?>
<script>
$("#accordion > li").click(function(){

	if(false == $(this).next().is(':visible')) {
		$('#accordion > ul').slideUp(300);
	}
	$(this).next().slideToggle(300);
});

$('#accordion > ul:eq(0)').show();

</script>