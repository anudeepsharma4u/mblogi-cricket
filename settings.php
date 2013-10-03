<?php
global $wpdb;
$table = MBC_TABLE_PREFIX."mblogicricket";
echo "<div class='wrap'>";
echo "<div id='icon-options-general' class='icon32'><br /></div><h2>MBlogi Cricket Settings</h2>";
if(isset($_POST['save']))
{
$mkey = $_POST['Mblogikey'];
$arc = $_POST['arcpg'];
$fsc = $_POST['fscpg'];
if($arc != $fsc)
{
$wpdb->query("UPDATE $table SET `apikey`='$mkey',`archievepg`='$arc',`fullscpg`='$fsc' WHERE `id`=1");
}
else
{
echo "<span style='color:red;font-weight:bold;'>Please do not select same pages for archieve and full score card</span>";
}
}
$str = "SELECT * FROM $table WHERE id=1";
	$res = $wpdb->get_results($str);
	$mblogikey = $res[0]->apikey;
	$arcval = $res[0]->archievepg;
	$fscval = $res[0]->fullscpg;
echo "<form action='' method='post'>";
echo "<div width='50%'><table border='0' class='form-table'>";
echo "<tr valign='top'>";
echo "<td scope='row'>MBlogi cricket API Key</td><td><input name='Mblogikey' type='text' value='$mblogikey' class='regular-text' /></td>";
echo "</tr>";
echo "<tr>";
$table1 = MBC_TABLE_PREFIX."posts";
$str1 = "SELECT id,post_title FROM $table1 WHERE post_status='publish' AND post_type='page'";
	$res1 = $wpdb->get_results($str1);
echo "<td>Archive dispaly page</td><td><select name='arcpg'>";
foreach($res1 as $row){
if($row->id == $arcval){
echo "<option value='$row->id' selected>".$row->post_title."</option>";
}
else
{
echo "<option value='$row->id'>".$row->post_title."</option>";
}
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Fullscorecard display page</td><td><select name='fscpg'>";
foreach($res1 as $row){
if($row->id == $fscval){
echo "<option value='$row->id' selected>".$row->post_title."</option>";
}
else
{
echo "<option value='$row->id'>".$row->post_title."</option>";
}
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td>&nbsp;</td><td><input type='submit' name='save' value='save' /></td>";
echo "</tr>";
echo "</table> </div>";
echo "</form>";
echo "</div></div>";
?>