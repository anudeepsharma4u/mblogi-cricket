<?php
global $wpdb;
$table = $wpdb->prefix . "mblogicricket";
$sql_stmt = "SELECT * from $table where id=1";
$sqlvals = $wpdb->get_results($sql_stmt);
$a = $sqlvals[0]->apikey;
if(isset($_REQUEST['mid']) && $_REQUEST['mid'] != 0)
{
//print "i am in if<br/>"; 
$url = "http://cricketapi.mblogi.com/fscjson.php?mid=".$_REQUEST['mid']."&api=".$a;
$json = file_get_contents($url);
$json = json_decode($json,true); ?>
<div class="matchhead"><span style="font-size:11px; font-weight: bold;"> 
<?php 
if($json[0]['team1']['fullName'] == "" && $json[0]['team2']['fullName'] == "")
{ 
print $json[0]['team1']['shortName']; ?> VS <?php print $json[0]['team2']['shortName'];
} 
else
{ 
print $json[0]['team1']['fullName']; ?> VS <?php print $json[0]['team2']['fullName']; 
} ?>, <?php
print $json[0]['series']; ?> <br/> <?php 
print $json[0]['matchdesc']; ?>, </span><span style="font-size:10px;">
Date: <?php print $json[0]['startdate']; ?> - <?php print $json[0]['enddate']; ?> <br/> Venue: <?php print $json[0]['venue-name']; ?>, 
<?php print $json[0]['venue-city']; ?>, <?php print $json[0]['venue-country']; ?></span><br/>
<span style="font-family:Verdana, Geneva, sans-serif; font-size:11px; font-weight: bold;"> <?php if($json[0]['state'] != 'inprogress') { print $json[0]['status']; ?> </br> <?php } ?>
<?php if($json[0]['toss']['tossWinner'] != "" ) { ?> Toss: <?php print $json[0]['toss']['tossWinner']; ?> ( Elected to <?php print $json[0]['toss']['tossDecision']; ?>) <?php } if($json[0]['mom'] != null) {echo "<br/>Man of the match: ".$json[0]['mom']; } if($json[0]['mos'] != null) {echo "<br/>Man of the series: ".$json[0]['mos']; }  ?>  </span>
</div><br/>
<?php
$s=count($json);
$planum = count($json[$s-1]['Squads']);
$team1 = $json[$s-1]['Squads'][0]['Country'];
$team2 = $json[$s-1]['Squads'][$planum-1]['Country'];
$team1players11 = null;
$team1bench = null;
$team2players11 = null;
$team2bench = null;
$team1squadarray[] = null;
$team2squadarray[] =null;
foreach($json[$s-1]['Squads'] as $json1)
{
if($json1['Country'] == $team1 && $json1['status'] == "")
{
if($json1['isCaptain'] != null)
{
$a = "(c)";
}
else
{
$a = null;
}
if($json1['isWiket Keeper'] != null)
{
$b = "(wk)";
}
else
{
$b = null;
}
if($team1players11 == null)
{
$team1players11 = $json1['Fullname'].$a.$b;
array_push($team1squadarray,$json1['Fullname'].$a.$b);
}
else
{
$team1players11 = $team1players11.",".$json1['Fullname'].$a.$b;
array_push($team1squadarray,$json1['Fullname'].$a.$b);
}
}
else if($json1['Country'] == $team1 && $json1['status'] == "bench")
{
if($team1bench == null)
{
$team1bench = $json1['Fullname'];
}
else
{
$team1bench = $team1bench.",".$json1['Fullname'];
}
}
else if($json1['Country'] == $team2 && $json1['status'] == "")
{
if($json1['isCaptain'] != null)
{
$c = "(c)";
}
else
{
$c = null;
}
if($json1['isWiket Keeper'] != null)
{
$d = "(wk)";
}
else
{
$d = null;
}
if($team2players11 == null)
{
$team2players11 = $json1['Fullname'].$c.$d;
array_push($team2squadarray,$json1['Fullname'].$c.$d);
}
else
{
$team2players11 = $team2players11.",".$json1['Fullname'].$c.$d;
array_push($team2squadarray,$json1['Fullname'].$c.$d);
}
}
else if($json1['Country'] == $team2 && $json1['status'] == "bench")
{
if($team2bench == null)
{
$team2bench = $json1['Fullname'];
}
else
{
$team2bench = $team2bench.",".$json1['Fullname'];
}
}
}
//print $batteam1;
//print $s;
if($json[0]['state'] != 'inprogress' && $json[0]['state'] != 'rain')
{
for($i=1;$i<$s-1;$i++)
{
$inn = $json[$i];
if($inn['battingteamname'] == null) {
}
else
{
?>
<table class="mbctable">
<tr class="mbctr"><th class="mbcth" class="mbcth" width="65%" colspan="2" style="text-align:left; padding-left:15px; color:#FFF;" ><?php print $inn['battingteamname'];?> Innings</th><th class="mbcth" class="mbcth" width="8%" style="color:#FFF;" >Runs</th><th class="mbcth" class="mbcth" width="8%" style="color:#FFF;" >Balls</th><th class="mbcth" class="mbcth" width="6%" style="color:#FFF;"  >4's</th><th class="mbcth" class="mbcth" width="6%" style="color:#FFF;"  >6's</th><th class="mbcth" class="mbcth" width="8%" style="color:#FFF;" >SR</th></tr>
<?php 
 $batcurrteam[] = null;
foreach($inn['batmen'] as $bat)
{ ?>
<tr class="mbctr"><td class="mbctd"><?php print $bat['shortname']; ?></td><td class="mbctd"><?php print $bat['outdesc']; ?></td><td class="mbctd"><?php print $bat['runs']; ?></td><td class="mbctd"><?php print $bat['balls']; ?></td><td class="mbctd"><?php print $bat['fours']; ?></td><td class="mbctd"><?php print $bat['six']; ?></td><td class="mbctd"><?php if($bat['strikerate'] != "") { print $bat['strikerate']; } else { $rgyuikj = $bat['runs']/$bat['balls']*100; echo number_format($rgyuikj, 2, '.', ''); }?></td></tr>
<?php 
}
if ($inn['didnt'] != null)
{
foreach($inn['didnt'] as $didntbat)
{ ?>
<tr class="mbctr"><td class="mbctd"><?php echo $didntbat['fullname']; ?></td><td class="mbctd" colspan="6"></td></tr>
<?php } } ?>
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
?>
</div>
<table class="mbctable ">
<tr class="mbctr"><th class="mbcth" class="mbcth"  style="color:#FFF;">Bowler</th><th class="mbcth" class="mbcth"  style="color:#FFF;">Overs</th><th class="mbcth" class="mbcth"  style="color:#FFF;">M</th><th class="mbcth" class="mbcth"  style="color:#FFF;">R</th><th class="mbcth" class="mbcth"  style="color:#FFF;">Wkt</th><th class="mbcth" class="mbcth"  style="color:#FFF;">Nb</th><th class="mbcth" class="mbcth"  style="color:#FFF;">Wb</th><th class="mbcth" class="mbcth"  style="color:#FFF;">Eco</th></tr>
<?php foreach($inn['bowler'] as $bow)
{ ?>
<tr class="mbctr"><td class="mbctd"><?php print $bow['shortname']; ?></td><td class="mbctd"><?php print $bow['overs']; ?></td><td class="mbctd"><?php print $bow['maiden']; ?></td><td class="mbctd"><?php print $bow['run']; ?></td><td class="mbctd"><?php print $bow['wickets']; ?></td><td class="mbctd"><?php print $bow['noball']; ?></td><td class="mbctd"><?php print $bow['wideballs']; ?></td><td class="mbctd"><?php if($bow['strikerate'] != "") { print $bow['strikerate']; } else { $rgthuijko = $bow['run']/$bow['overs'];  echo number_format($rgthuijko, 2, '.', '');} ?></td></tr>
<?php } ?>
</table>
<?php
}
}
}
else
{?>
<?php 
$dfog = $_REQUEST['mid'];
$innid = $s-2;
?>
<script type="text/javascript">
	jQuery(document).ready(function() {
		setInterval(function() {
			jQuery.get(
				'<?php echo get_option('siteurl') . '/wp-admin/admin-ajax.php' ?>',
				{
					action		: 'liveupdate',
					mid 		: '<?php echo $dfog; ?>',
					inn 		: '<?php echo $innid; ?>'
				},
				function(response) {
					jQuery('#latestData').html(response);
				}
			);
		}, 20000 );	
	});	
</script>
<div id="latestData">
<?php
$inn = $json[$s-2];
?>
<table class="mbctable">
<tr class="mbctr"><th class="mbcth" class="mbcth" width="65%" colspan="2" style="text-align:left; padding-left:15px; color:#FFF;" ><?php print $inn['battingteamname'];?> Innings</th><th class="mbcth" class="mbcth" width="8%" style="color:#FFF;" >Runs</th><th class="mbcth" class="mbcth" width="8%" style="color:#FFF;" >Balls</th><th class="mbcth" class="mbcth" width="6%" style="color:#FFF;"  >4's</th><th class="mbcth" class="mbcth" width="6%" style="color:#FFF;"  >6's</th><th class="mbcth" class="mbcth" width="8%" style="color:#FFF;" >SR</th></tr>
<?php 
if($inn['batmen'] != null)
{
foreach($inn['batmen'] as $bat)
{ ?>
<tr class="mbctr"><td class="mbctd"><?php if($bat['outdesc']=='batting'){ print "<b>"; } print $bat['shortname']; if($bat['strikestatus'] == 'striker' ){ print "*"; } if($bat['outdesc']=='batting'){ print "</b>"; }?></td>
<td class="mbctd"><?php if($bat['outdesc']=='batting'){ print "<b>"; } print $bat['outdesc']; if($bat['outdesc']=='batting'){ print "</b>"; }?></td>
<td class="mbctd"><?php if($bat['outdesc']=='batting'){ print "<b>"; } print $bat['runs']; if($bat['outdesc']=='batting'){ print "</b>"; }?></td>
<td class="mbctd"><?php if($bat['outdesc']=='batting'){ print "<b>"; } print $bat['balls']; if($bat['outdesc']=='batting'){ print "</b>"; }?></td>
<td class="mbctd"><?php if($bat['outdesc']=='batting'){ print "<b>"; } print $bat['fours']; if($bat['outdesc']=='batting'){ print "</b>"; }?></td>
<td class="mbctd"><?php if($bat['outdesc']=='batting'){ print "<b>"; } print $bat['six']; if($bat['outdesc']=='batting'){ print "</b>"; } ?></td>
<td class="mbctd"><?php if($bat['outdesc']=='batting'){ print "<b>"; } if($bat['strikerate'] != "") { print $bat['strikerate']; } else { $rgyuikj = $bat['runs']/$bat['balls']*100; echo number_format($rgyuikj, 2, '.', ''); } if($bat['outdesc']=='batting'){ print "</b>"; } ?></td></tr>
<?php }  }
if($inn['didnt'] != null)
{
foreach($inn['didnt'] as $didntbat)
{ ?>
<tr class="mbctr"><td class="mbctd"><?php echo $didntbat['fullname']; ?></td><td class="mbctd" colspan="6"></td></tr>
<?php } } ?>
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
//print_r($a);?>
</div>
<table class="mbctable ">
<tr class="mbctr"><th class="mbcth" class="mbcth"  style="color:#FFF;">Bowler</th><th class="mbcth" class="mbcth"  style="color:#FFF;">Overs</th><th class="mbcth" class="mbcth"  style="color:#FFF;">M</th><th class="mbcth" class="mbcth"  style="color:#FFF;">R</th><th class="mbcth" class="mbcth"  style="color:#FFF;">Wkt</th><th class="mbcth" class="mbcth"  style="color:#FFF;">Nb</th><th class="mbcth" class="mbcth"  style="color:#FFF;">Wb</th><th class="mbcth" class="mbcth"  style="color:#FFF;">Eco</th></tr>
<?php 
if($inn['bowler'] != null)
{
foreach($inn['bowler'] as $bow)
{ ?>
<tr class="mbctr"><td class="mbctd"><?php if($bow['strikestatus'] != '') {echo '<b>'; } print $bow['shortname']; if($bow['strikestatus'] == 'striker') {echo '*';} if($bow['strikestatus'] != '') {echo '</b>';} ?></td><td class="mbctd"><?php if($bow['strikestatus'] != '') {echo '<b>';} print $bow['overs']; if($bow['strikestatus'] != '') {echo '</b>';} ?></td><td class="mbctd"><?php if($bow['strikestatus'] != '') {echo '<b>'; } print $bow['maiden']; if($bow['strikestatus'] != '') {echo '</b>';} ?></td><td class="mbctd"><?php if($bow['strikestatus'] != '') {echo '<b>'; } print $bow['run']; if($bow['strikestatus'] != '') {echo '</b>';} ?></td><td class="mbctd"><?php if($bow['strikestatus'] != '') {echo '<b>'; } print $bow['wickets']; if($bow['strikestatus'] != '') {echo '</b>';} ?></td><td class="mbctd"><?php if($bow['strikestatus'] != '') {echo '<b>'; } print $bow['noball']; if($bow['strikestatus'] != '') {echo '</b>';} ?></td><td class="mbctd"><?php if($bow['strikestatus'] != '') {echo '<b>'; } print $bow['wideballs']; if($bow['strikestatus'] != '') {echo '</b>';} ?></td><td class="mbctd"><?php if($bow['strikestatus'] != '') {echo '<b>'; } if($bow['strikerate'] != "") { print $bow['strikerate']; } else { $rgthuijko = $bow['run']/$bow['overs'];  echo number_format($rgthuijko, 2, '.', '');}  if($bow['strikestatus'] != '') {echo '</b>';} ?></td></tr>
<?php } } ?>
</table>
</div>
<?php for($i=$s-3;$i>=1;$i--)
{
$inn = $json[$i];
?>
<table class="mbctable">
<tr class="mbctr"><th class="mbcth" class="mbcth" width="65%" colspan="2" style="text-align:left; padding-left:15px; color:#FFF;" ><?php print $inn['battingteamname'];?> Innings</th><th class="mbcth" class="mbcth" width="8%" style="color:#FFF;" >Runs</th><th class="mbcth" class="mbcth" width="8%" style="color:#FFF;" >Balls</th><th class="mbcth" class="mbcth" width="6%" style="color:#FFF;"  >4's</th><th class="mbcth" class="mbcth" width="6%" style="color:#FFF;"  >6's</th><th class="mbcth" class="mbcth" width="8%" style="color:#FFF;" >SR</th></tr>
<?php foreach($inn['batmen'] as $bat)
{ ?>
<tr class="mbctr"><td class="mbctd"><?php if($bat['strikestatus'] != ''&& $bat['outdesc']=='batting'  ){ print "<b>"; }print $bat['shortname']; if($bat['strikestatus'] == 'striker' && $bat['outdesc']=='batting' ){ print "*"; }if($bat['strikestatus'] != ''&& $bat['outdesc']=='batting'  ){ print "</b>"; } ?></td><td class="mbctd"><?php print $bat['outdesc']; ?></td><td class="mbctd"><?php print $bat['runs']; ?></td><td class="mbctd"><?php print $bat['balls']; ?></td><td class="mbctd"><?php print $bat['fours']; ?></td><td class="mbctd"><?php print $bat['six']; ?></td><td class="mbctd"><?php if($bat['strikerate'] != "") { print $bat['strikerate']; } else { $rgyuikj = $bat['runs']/$bat['balls']*100; echo number_format($rgyuikj, 2, '.', ''); }?></td></tr>
<?php } 
if ($inn['didnt'] != null)
{
foreach($inn['didnt'] as $didntbat)
{ ?>
<tr class="mbctr"><td class="mbctd"><?php echo $didntbat['fullname']; ?></td><td class="mbctd" colspan="6"></td></tr>
<?php } } ?>
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
//print_r($a);?>
</div>
<table class="mbctable">
<tr class="mbctr"><th class="mbcth" class="mbcth"  style="color:#FFF;">Bowler</th><th class="mbcth" class="mbcth"  style="color:#FFF;">Overs</th><th class="mbcth" class="mbcth"  style="color:#FFF;">M</th><th class="mbcth" class="mbcth"  style="color:#FFF;">R</th><th class="mbcth" class="mbcth"  style="color:#FFF;">Wkt</th><th class="mbcth" class="mbcth"  style="color:#FFF;">Nb</th><th class="mbcth" class="mbcth"  style="color:#FFF;">Wb</th><th class="mbcth" class="mbcth"  style="color:#FFF;">Eco</th></tr>
<?php foreach($inn['bowler'] as $bow)
{ ?>
<tr class="mbctr"><td class="mbctd"><?php print $bow['shortname']; ?></td><td class="mbctd"><?php print $bow['overs']; ?></td><td class="mbctd"><?php print $bow['maiden']; ?></td><td class="mbctd"><?php print $bow['run']; ?></td><td class="mbctd"><?php print $bow['wickets']; ?></td><td class="mbctd"><?php print $bow['noball']; ?></td><td class="mbctd"><?php print $bow['wideballs']; ?></td><td class="mbctd"><?php if($bow['strikerate'] != "") { print $bow['strikerate']; } else { $rgthuijko = $bow['run']/$bow['overs'];  echo number_format($rgthuijko, 2, '.', '');} ?></td></tr>
<?php } ?>
</table>
<?php
}?>
<?php }
//displaying squads-------------------------------------------------------------------------------------------------------------------
if($team1 != null)
{
?>
<div style="background:#225280; color:#FFF; font-weight: bold; text-align:left; font-family:Verdana, Geneva, sans-serif; font-size:11px; padding: 5px 1px; width:100%;">Squads</div>
<span style="color: #333; font-weight: bold; text-align:left; padding: 5px 1px; font-family:Verdana, Geneva, sans-serif; font-size:12px;" > <?php print $team1; ?>:<br/>
PlayingXI:  </span><span style="color:#333; font-family:Verdana, Geneva, sans-serif; font-size:10px;" ><?php print $team1players11."."; ?> </span> <br/><span style="color:#333; font-weight: bold; text-align:left; padding: 5px 1px; font-family:Verdana, Geneva, sans-serif; font-size:12px;" >Bench:</span> <span style="color:#333; font-family:Verdana, Geneva, sans-serif; font-size:10px;" ><?php print $team1bench."."; ?> </span>
<br/>
<span style="color:#333; font-weight: bold; text-align:left; padding: 5px 1px; font-family:Verdana, Geneva, sans-serif; font-size:12px;" >
<?php print $team2; ?>:<br/>
PlayingXI:  </span><span style="color:#333; font-family:Verdana, Geneva, sans-serif; font-size:10px;" ><?php print  $team2players11."."; ?> </span> <br/><span style="color:#333; font-weight: bold; text-align:left; padding: 5px 1px; font-family:Verdana, Geneva, sans-serif; font-size:12px;" >Bench:</span> <span style="color:#333; font-family:Verdana, Geneva, sans-serif; font-size:10px;" ><?php print $team2bench."."; ?> </span> <?php
}
//end of displaying squads--------------------------------------------------------------------------------------------------------------------
}
?>