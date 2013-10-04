<?php
/*
Plugin Name: MBlogi Cricket
Plugin URI: http://cric.mblogi.com
Description: Live Cricket Scores
Author: MBlogi
Version: 1.0.1
Author URI: https://mblogitech.com
*/
$siteurl = get_option('siteurl');
define('MBC_FOLDER', dirname(plugin_basename(__FILE__)));
define('MBC_URL', $siteurl.'/wp-content/plugins/' . MBC_FOLDER);
define('MBC_FILE_PATH', dirname(__FILE__));
define('MBC_DIR_NAME', basename(MBC_FILE_PATH));
// this is the table prefix
global $wpdb;
$mbc_table_prefix=$wpdb->prefix;
define('MBC_TABLE_PREFIX', $mbc_table_prefix);
register_activation_hook(__FILE__,'mbc_install');
register_deactivation_hook(__FILE__ , 'mbc_uninstall' );
function log_me($message) {
	if (WP_DEBUG === true) {
		if (is_array($message) || is_object($message)) {
			error_log(print_r($message, true));
		} else {
			error_log($message);
		}
	}
}

function mbc_install()
{
	global $wpdb;
	$table = MBC_TABLE_PREFIX."mblogicricket";
	$structure = "CREATE TABLE if not exists $table (
        id INT(9) NOT NULL AUTO_INCREMENT,
        apikey VARCHAR(80),
        archievepg VARCHAR(20),
        fullscpg VARCHAR(20),
	UNIQUE KEY id (id)
    )";
	$wpdb->query($structure);
	// Populate table
	$wpdb->query("INSERT INTO $table(id)
        VALUES(1)");
}
function mbc_uninstall()
{
	global $wpdb;
	$table = MBC_TABLE_PREFIX."mblogicricket";
	$structure = "drop table if exists $table";
	$wpdb->query($structure);
}
function mbc_admin_menu() {
	add_menu_page(
		"MBlogi Cricket",
		"MBlogi Cricket",
		8,
		__FILE__,
		"mbc_admin_menu_list",
		MBC_URL."/images/menu.gif"
	);

}
add_action('admin_menu','mbc_admin_menu');
function mbc_admin_menu_list()
{
	include 'settings.php';
}

function my_init_method() {
	if (!is_admin()) {
		wp_enqueue_style( 'mblogicricket', plugins_url( 'mblogicricket.css', __FILE__ ));
		wp_enqueue_style( 'mblogicricket');
		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
		wp_enqueue_script( 'jquery' );
	}
}
add_action('init', 'my_init_method');

function mblogi_cricket_archive_shortcode()
{
	ob_start();
	include 'inc/archive.php';
	$myvariableone = ob_get_clean();
	return $myvariableone;
}
add_shortcode("mblogi_cricket_archive","mblogi_cricket_archive_shortcode");

function mblogi_cricket_current_series_shortcode()
{
	ob_start();
	include 'inc/currentseries.php';
	$myvariabletwo = ob_get_clean();
	return $myvariabletwo;
}
add_shortcode("mblogi_cricket_current_series","mblogi_cricket_current_series_shortcode");

function mblogi_cricket_fullscore_scorecard()
{
	ob_start();
	include 'inc/fullscore.php';
	$myvariablethree = ob_get_clean();
	return $myvariablethree;
}
add_shortcode("mblogi_cricket_fullscore","mblogi_cricket_fullscore_scorecard");
add_action("widgets_init", array('Widget_name', 'register'));
class Widget_name {
	function control(){
		 echo 'nothing to configure';
	}
	function widget($args){
		echo $args['before_widget'];
		echo $args['before_title'] . 'Cricket Scores' . $args['after_title'];
		include 'inc/mblogi-widget.php';
		echo $args['after_widget'];
	}
	function register(){
		 register_sidebar_widget('MBlogi Cricket Live', array('Widget_name', 'widget'));
		register_widget_control('MBlogi Cricket Live', array('Widget_name', 'control'));
	}
}

add_action( 'wp_ajax_sscupdate', 'mblogi_fsc_update' );
add_action( 'wp_ajax_nopriv_sscupdate', 'mblogi_fsc_update' );
add_action( 'wp_head', 'js_to_load' );

function js_to_load() {

?>
<script type="text/javascript">
	jQuery(document).ready(function(){
		setInterval(function() {
			jQuery.get(
				'<?php echo get_option('siteurl') . '/wp-admin/admin-ajax.php' ?>',
				{
					action		: 'sscupdate',
				},
				function(response) {
					jQuery('#latestscData').html(response);
				}
			);
		}, 20000 );	
	});
</script>

<?php
}

function mblogi_fsc_update() {

	ob_start();

	global $wpdb;
	$table = $wpdb->prefix . "mblogicricket";
	$sql_stmt = "SELECT * from $table where id=1";
	$sqlvals = $wpdb->get_results($sql_stmt);
	$a = $sqlvals[0]->apikey;
	$b = $sqlvals[0]->fullscpg;
	$styurli = get_site_url();
	$tables = $wpdb->prefix . "posts";
	$sql_stmts = "SELECT * from $tables where id='$b'";
	$sqlvalss = $wpdb->get_results($sql_stmts);
	$as = $sqlvalss[0]->post_name;
	$sturl = $styurli."/".$as;
	//print "i am in if<br/>";
	$url = "http://cricketapi.mblogi.com/sscjson.php?&api=".$a;
	$json = file_get_contents($url);
	$json = json_decode($json,true);
	$inn = $json;
	$sstfr = count($inn);
	if($sstfr == 0)
	{
		echo 'No live match in progress';
	}
	else
	{
		foreach($inn as $mat)
		{
			$xxt = count($mat);
			$innings_tot = $xxt - 1;
			$innings_tot;
?>
<div class="mbcmatch"><a href="<?php echo $sturl."/?mid=".$mat[0]['mid']; ?>" ><?php echo $mat[0]['team1shortname']; ?> VS <?php echo $mat[0]['team2shortName']; ?></a></div>
<div> <?php
			if($innings_tot == 1)
			{
				print $mat[1]['batting'].": ".$mat[1]['runs']."/".$mat[1]['wickets']." in ".$mat[1]['overs']." Ov ";
			}
			if($innings_tot == 2)
			{
				print $mat[1]['batting'].": ".$mat[1]['runs']."/".$mat[1]['wickets']." in ".$mat[1]['overs']." Ov | ";
				print $mat[2]['batting'].": ".$mat[2]['runs']."/".$mat[2]['wickets']." in ".$mat[2]['overs']." Ov";
			}
			if($innings_tot == 4)
			{
				if($mat[1]['batting'] == $mat[3]['batting'])
				{
					print $mat[1]['batting']." ".$mat[1]['runs']."/".$mat[1]['wickets']." & ";
					print $mat[3]['runs']."/".$mat[3]['wickets']." | ";
				}
				elseif($mat[1]['batting'] == $mat[4]['batting'])
				{
					print $mat[1]['batting']." ".$mat[1]['runs']."/".$mat[1]['wickets']." & ";
					print $mat[4]['runs']."/".$mat[4]['wickets']." | ";
				}

				if($mat[2]['batting'] == $mat[3]['batting'])
				{
					print $mat[2]['batting']." ".$mat[2]['runs']."/".$mat[2]['wickets']."  & ";
					print $mat[3]['runs']."/".$mat[3]['wickets'];
				}
				elseif($mat[2]['batting'] == $mat[4]['batting'])
				{
					print $mat[2]['batting']." ".$mat[2]['runs']."/".$mat[2]['wickets']." &";
					print $mat[4]['runs']."/".$mat[4]['wickets'];
				}
			}
			if($innings_tot == 3)
			{
				if($mat[1]['batting'] == $mat[3]['batting'])
				{
					print $mat[1]['batting']." ".$mat[1]['runs']."/".$mat[1]['wickets']." & ";
					print $mat[3]['runs']."/".$mat[3]['wickets']." | ";
				}

				if($mat[2]['batting'] == $mat[3]['batting'])
				{
					print $mat[2]['batting']." ".$mat[2]['runs']."/".$mat[2]['wickets']." & ";
					print $mat[3]['runs']."/".$mat[3]['wickets'];
				}
				elseif($mat[2]['batting'] != $mat[3]['batting'])
				{
					print $mat[2]['batting']." ".$mat[2]['runs']."/".$mat[2]['wickets'];
				}
			}
			?> </div>
<span><?php if($mat[0]['status'] != '' ) { echo $mat[0]['status']; }  ?> </span>

<?php
		}
	}

	$output = ob_get_contents();
	ob_end_clean();
	echo $output;
	exit;

}

add_action( 'wp_ajax_liveupdate', 'mbi_live_update' );
add_action( 'wp_ajax_nopriv_liveupdate', 'mbi_live_update' );

function mbi_live_update() {

ob_start();	
$inn = $_GET['inn'];
$matchid = $_GET['mid'];
//$a = "QuDoPT5fq3UrTN6w";
global $wpdb;
$table = $wpdb->prefix . "mblogicricket";
$sql_stmt = "SELECT * from $table where id=1";
$sqlvals = $wpdb->get_results($sql_stmt);
$a = $sqlvals[0]->apikey;
//print "i am in if<br/>"; 
 $url = "http://cricketapi.mblogi.com/liveupdatejson.php?mid=".$matchid."&api=".$a."&inn=".$inn;
$json = file_get_contents($url);
$json = json_decode($json,true);
$inn = $json;
if($inn['runninginnigs'] != $inn['innings'] || $inn['result'] == 'complete') 
{ ?>
<script type="text/javascript">
parent.window.location.reload();
</script>
<?php }
print "<span style='color: #282828; font: bold 12px Arial,Helvetica,sans-serif;'>".$inn['status'];
if($inn['result']== 'rain') { echo"(".$inn['result'].")"; }
?> </span> 
<table class="mbctable">
<tr class="mbctr"><th class="mbcth" width="65%" colspan="2" style="text-align:left; padding-left:15px; color:#FFF;"><?php print $inn['battingteamname'];?> Innings</th><th class="mbcth" width="8%" style="color:#FFF;">Runs</th><th class="mbcth" width="8%" style="color:#FFF;">Balls</th><th class="mbcth" width="6%" style="color:#FFF;">4's</th><th class="mbcth" width="6%" style="color:#FFF;">6's</th><th class="mbcth" width="8%" style="color:#FFF;">SR</th></tr>
<?php foreach($inn['batmen'] as $bat)
{ ?>
<tr class="mbctr">
<td class="mbctd"><?php if($bat['outdesc']=='batting'){ print "<b>"; } print $bat['shortname']; if($bat['strikestatus'] == 'striker' ){ print "*"; } if($bat['outdesc']=='batting'){ print "</b>"; }?></td>
<td class="mbctd"><?php if($bat['outdesc']=='batting'){ print "<b>"; } print $bat['outdesc']; if($bat['outdesc']=='batting'){ print "</b>"; }?></td>
<td class="mbctd"><?php if($bat['outdesc']=='batting'){ print "<b>"; } print $bat['runs']; if($bat['outdesc']=='batting'){ print "</b>"; }?></td>
<td class="mbctd"><?php if($bat['outdesc']=='batting'){ print "<b>"; } print $bat['balls']; if($bat['outdesc']=='batting'){ print "</b>"; }?></td>
<td class="mbctd"><?php if($bat['outdesc']=='batting'){ print "<b>"; } print $bat['fours']; if($bat['outdesc']=='batting'){ print "</b>"; }?></td>
<td class="mbctd"><?php if($bat['outdesc']=='batting'){ print "<b>"; } print $bat['six']; if($bat['outdesc']=='batting'){ print "</b>"; } ?></td>
<td class="mbctd"><?php if($bat['outdesc']=='batting'){ print "<b>"; } if($bat['strikerate'] != "") { print $bat['strikerate']; } else { $rgyuikj = $bat['runs']/$bat['balls']*100; echo number_format($rgyuikj, 2, '.', ''); } if($bat['outdesc']=='batting'){ print "</b>"; } ?></td></tr>
<?php } 
foreach($inn['didnt'] as $didntbat)
{ ?>
<tr class="mbctr"><td class="mbctd"><?php echo $didntbat['fullname']; ?></td><td class="mbctd" colspan="6"></td></tr>
<?php } ?>
<tr class="mbctr"><td class="mbctd" colspan="2" align="right">Extras(b: <?php print $inn['extras']['Byes']; ?>, lb: <?php print $inn['extras']['Leg byes']; ?>, wb: <?php print $inn['extras']['Wideballs']; ?>, nb: <?php print $inn['extras']['Noball']; ?>, p: <?php print $inn['extras']['Penalty']; ?>): </td><td class="mbctd"><?php print $inn['extras']['Total']; ?></td><td class="mbctd" colspan="4" align="left">
</td></tr>
<tr class="mbctr"><td class="mbctd" colspan="2" align="right">Total: </td><td class="mbctd"><b><?php print $inn['runs']; ?></b></td><td class="mbctd" colspan="4" align="left">(<?php print $inn['wickets']; ?> wkts, <?php print $inn['overs'];?> overs)</td></tr>
</table>
<div id="fow" style="color: #282828; float: left; font: bold 12px Arial,Helvetica,sans-serif; text-align: left; width: 100%;">Fall of Wickets</div>
 <div style="color: #282828; font: 12px Arial,Helvetica,sans-serif; margin: 20px 0px; padding-top: 9px; text-align: left; width: 100%;">
 
<?php 
//$a = array();
$bat[] = $inn['batmen'];
$sortArray = array(); 

foreach($inn['batmen'] as $person){ 
    foreach($person as $key=>$value){ 
        if(!isset($sortArray[$key])){ 
            $sortArray[$key] = array(); 
        } 
        $sortArray[$key][] = $value; 
    } 
} 

$orderby = "wicketnumber"; //change this to whatever key you want from the array 

array_multisort($sortArray[$orderby],SORT_ASC,$inn['batmen']);

//print_r($b);
foreach($inn['batmen'] as $bat)
{
if($bat['wicketnumber'] != "")
{
//$a=$a.$bat['wicketnumber'];
echo $bat['wicketnumber']."/".$bat['teamruns']."(".$bat['shortname'].",".$bat['Overnumber']." ov), ";
}
} 
//print_r($a);
?>
</div>
<table class="mbctable">
<tr class="mbctr"><th class="mbcth" style="color:#FFF;">Bowler</th><th class="mbcth" style="color:#FFF;">Overs</th><th class="mbcth" style="color:#FFF;">M</th><th class="mbcth" style="color:#FFF;">R</th><th class="mbcth" style="color:#FFF;">Wkt</th><th class="mbcth" style="color:#FFF;">Nb</th><th class="mbcth" style="color:#FFF;">Wb</th><th class="mbcth" style="color:#FFF;">Eco</th></tr>
<?php foreach($inn['bowler'] as $bow)
{ ?>
<tr class="mbctr"><td class="mbctd"><?php if($bow['strikestatus'] != '') {echo '<b>'; } print $bow['shortname']; if($bow['strikestatus'] == 'striker') {echo '*';} if($bow['strikestatus'] != '') {echo '</b>';} ?></td><td class="mbctd"><?php if($bow['strikestatus'] != '') {echo '<b>';} print $bow['overs']; if($bow['strikestatus'] != '') {echo '</b>';} ?></td><td class="mbctd"><?php if($bow['strikestatus'] != '') {echo '<b>'; } print $bow['maiden']; if($bow['strikestatus'] != '') {echo '</b>';} ?></td><td class="mbctd"><?php if($bow['strikestatus'] != '') {echo '<b>'; } print $bow['run']; if($bow['strikestatus'] != '') {echo '</b>';} ?></td><td class="mbctd"><?php if($bow['strikestatus'] != '') {echo '<b>'; } print $bow['wickets']; if($bow['strikestatus'] != '') {echo '</b>';} ?></td><td class="mbctd"><?php if($bow['strikestatus'] != '') {echo '<b>'; } print $bow['noball']; if($bow['strikestatus'] != '') {echo '</b>';} ?></td><td class="mbctd"><?php if($bow['strikestatus'] != '') {echo '<b>'; } print $bow['wideballs']; if($bow['strikestatus'] != '') {echo '</b>';} ?></td><td class="mbctd"><?php if($bow['strikestatus'] != '') {echo '<b>'; } if($bow['strikerate'] != "") { print $bow['strikerate']; } else { $rgthuijko = $bow['run']/$bow['overs'];  echo number_format($rgthuijko, 2, '.', '');}  if($bow['strikestatus'] != '') {echo '</b>';} ?></td></tr>
<?php } ?>
</table>
	<?php	
	$output = ob_get_contents();
	ob_end_clean();
	echo $output;
	exit;
}