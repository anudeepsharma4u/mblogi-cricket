<?php
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
$furl = "http://cricketapi.mblogi.com/sfsjson.php?api=".$a;
$fjson = file_get_contents($furl);
$fjson = json_decode($fjson,true);
$rurl = "http://cricketapi.mblogi.com/srsjson.php?api=".$a;
$rjson = file_get_contents($rurl);
$rjson = json_decode($rjson,true);
$curl = "http://cricketapi.mblogi.com/sscjson.php?api=".$a;
$cjson = file_get_contents($curl);
$cjson = json_decode($cjson,true);
$myupdlincs = MBC_URL . '/sscupdate.php';
 ?>
<script>
$(document).ready(function(){
$('ul.tabs').each(function(){
var $active, $content, $links = $(this).find('a');
$active = $($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
$active.addClass('active');
$content = $($active.attr('href'));
$links.not($active).each(function () {
$($(this).attr('href')).hide();
});
$(this).on('click', 'a', function(e){
$active.removeClass('active');
$content.hide();
$active = $(this);
$content = $($(this).attr('href'));
$active.addClass('active');
$content.show();
e.preventDefault();
});
});
});
</script>

<ul id="mblogi_widget" >
			<li class="mbclirchwidget" style="color: #fff;">Current</li>
			
<ul>
		 <li>
		 <div id="latestscData">
		 <?php
		 $inn = $cjson;
  		 $sstfr = count($inn);
if($sstfr == 0)
{
echo 'No live match in progress';
}
else
{
		 
			 foreach($cjson as $mat)
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
<span><?php if($mat[0]['status'] != '' ) { echo $mat[0]['status']; } ?> </span>
<?php
} 

}
?>
			</li>
		</ul>
		<li class="mbclirchwidget" style="color: #fff;">Recent</li>
			
		 <ul><li>
		 <?php foreach($rjson as $midr)
{  ?>
			<div class="mbcmatch"><a href="<?php echo $sturl."/?mid=".$midr['mid']; ?>"><?php echo $midr['Team1']; ?> VS <?php echo $midr['Team2']; ?></a></div>
			<span><?php echo $midr['Seriesname']; ?> <br/><?php echo $midr['startdate']; ?>, <strong><?php print $midr['result']; ?></strong></span>
			<?php } ?>
		 </li></ul>
		 <li class="mbclirchwidget" style="color: #fff;">Fixtures</li>
		<ul><li>
			<?php foreach($fjson as $midf)
{ ?>
			 <div class="mbcmatch"><a href="#"><?php echo $midf['Team1']; ?> VS <?php echo $midf['Team2']; ?></a></div>
			 <span><?php echo $midf['startdate']; ?>, <?php echo $midf['ISTtime']; ?> IST<br/> <?php echo $midf['Seriesname']; ?></span>
			<?php } ?>
		
		</li>
		</ul>
		</ul>
<script>
$("#mblogi_widget > li").click(function(){

	if(false == $(this).next().is(':visible')) {
		$('#mblogi_widget > ul').slideUp(300);
	}
	$(this).next().slideToggle(300);
});

$('#mblogi_widget > ul:eq(0)').show();

</script>