<?php /* $Id: page.asteriskinfo.php 2243 2006-08-12 17:13:17Z p_lindheimer $ */
//This file is part of FreePBX.
//
//    FreePBX is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 2 of the License, or
//    (at your option) any later version.
//
//    FreePBX is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with FreePBX.  If not, see <http://www.gnu.org/licenses/>.
//
//    Copyright (C) 2006 Astrogen LLC 
//
$dispnum = 'asteriskinfo'; //used for switch on config.php

$tabindex = 0;

$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
$extdisplay = isset($_REQUEST['extdisplay'])?$_REQUEST['extdisplay']:'summary';
$chan_dahdi = ast_with_dahdi();

	$modesummary = _("Summary");
	$moderegistries = _("Registries");
	$modechannels = _("Channels");
	$modepeers = _("Peers");
	$modesip = _("Sip Info");
	$modeiax = _("IAX Info");
	$modeconferences = _("Conferences");
	$modesubscriptions = _("Subscriptions");
	$modeall = _("Full Report");
	
	$uptime = _("Uptime");
	$activechannels = _("Active Channel(s)");
	$sipchannels = _("Sip Channel(s)");
	$iax2channels = _("IAX2 Channel(s)");
	$iax2peers = _("IAX2 Peers");
	$sipregistry = _("Sip Registry");
	$sippeers = _("Sip Peers");
	$iax2registry = _("IAX2 Registry");
	$subscribenotify = _("Subscribe/Notify");
	if ($chan_dahdi){
		$zapteldriverinfo = _("DAHDI driver info");
	} else {
		$zapteldriverinfo = _("Zaptel driver info");
	}
	$conferenceinfo = _("Conference Info");
	$voicemailusers = _("Voicemail Users");

$modes = array(
	"summary" => $modesummary,
	"registries" => $moderegistries,
	"channels" => $modechannels,
	"peers" => $modepeers,
	"sip" => $modesip,
	"iax" => $modeiax,
	"conferences" => $modeconferences,
	"subscriptions" => $modesubscriptions,
	"voicemail" => $voicemailusers,
	"all" => $modeall
);
$arr_all = array(
	$uptime => "show uptime",
	$activechannels => "show channels",
	$sipchannels => "sip show channels",
	$iax2channels => "iax2 show channels",
	$sipregistry => "sip show registry",
	$sippeers => "sip show peers",
	$iax2registry => "iax2 show registry",
	$iax2peers => "iax2 show peers",
	$subscribenotify => "show hints",
	$zapteldriverinfo => "zap show channels",
	$conferenceinfo => "meetme",
	$voicemailusers => "show voicemail users",
);
$arr_registries = array(
	$sipregistry => "sip show registry",
	$iax2registry => "iax2 show registry",
);
$arr_channels = array(
	$activechannels => "show channels",
	$sipchannels => "sip show channels",
	$iax2channels => "iax2 show channels",
);
$arr_peers = array(
	$sippeers => "sip show peers",
	$iax2peers => "iax2 show peers",
);
$arr_sip = array(
	$sipregistry => "sip show registry",
	$sippeers => "sip show peers",
);
$arr_iax = array(
	$iax2registry => "iax2 show registry",
	$iax2peers => "iax2 show peers",
);
$arr_conferences = array(
	$conferenceinfo => "meetme",
);
$arr_subscriptions = array(
	$subscribenotify => "show hints"
);
$arr_voicemail = array(
	$voicemailusers => "show voicemail users",
);

$engineinfo = engine_getinfo();
$astver =  $engineinfo['version'];

if (version_compare($astver, '1.4', 'ge')) {
	$arr_all[$uptime]="core show uptime";
	$arr_all[$activechannels]="core show channels";
	$arr_all[$subscribenotify]="core show hints";
	$arr_all[$voicemailusers]="voicemail show users";
	$arr_channels[$activechannels]="core show channels";
	$arr_subscriptions[$subscribenotify]="core show hints";
	$arr_voicemail[$voicemailusers]="voicemail show users";
}

if (version_compare($astver, '1.6', 'ge')) {
	$arr_conferences[$conferenceinfo]="meetme list";
}

if ($chan_dahdi){
	$arr_all[$zapteldriverinfo]="dahdi show channels";
}
if ($chan_dahdi){
	$arr_all[$zapteldriverinfo]="dahdi show channels";
}
?>
</div>

<div class="rnav"><ul>
<?php 
foreach ($modes as $mode => $value) {
	echo "<li><a id=\"".($extdisplay==$mode)."\" href=\"config.php?&type=".urlencode("tool")."&display=".urlencode($dispnum)."&extdisplay=".urlencode($mode)."\">"._($value)."</a></li>";
}
?>
</ul></div>

<div class="content">
<h2><span class="headerHostInfo"><?php echo _("Asterisk (Ver. ").$astver."): "._($modes[$extdisplay])?></span></h2>

<form name="asteriskinfo" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post">
<input type="hidden" name="display" value="asteriskinfo"/>
<input type="hidden" name="action" value="asteriskinfo"/>
<table>

<table class="box">
<?php
if (!$astman) {
?>
	<tr class="boxheader">
		<td colspan="2" align="center"><h5><?php echo _("ASTERISK MANAGER ERROR")?><hr></h5></td>
	</tr>
		<tr class="boxbody">
			<td>
			<table border="0" >
				<tr>
					<td align="left">
							<?php 
							echo "<br>"._("The module was unable to connect to the Asterisk manager.<br>Make sure Asterisk is running and your manager.conf settings are proper.<br><br>");
							?>
					</td>
				</tr>
			</table>
			</td>
		</tr>
<?php
} else {
	if ($extdisplay != "summary") {
		$arr="arr_".$extdisplay;
		foreach ($$arr as $key => $value) {
?>
			<tr class="boxheader">
				<td colspan="2" align="center"><h5><?php echo _("$key")?><hr></h5></td>
			</tr>
			<tr class="boxbody">
				<td>
				<table border="0" >
					<tr>
						<td>
							<pre>
								<?php 
								$response = $astman->send_request('Command',array('Command'=>$value));
								$new_value = $response['data'];
								echo ltrim($new_value,'Privilege: Command');
								?>
							</pre>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		<?php
			}
		} else {
	?>
			<tr class="boxheader">
				<td colspan="2" align="center"><h5><?php echo _("Summary")?><hr></h5></td>
			</tr>
			<tr class="boxbody">
				<td>
				<table border="0">
					<tr>
						<td>
							<?php echo buildAsteriskInfo(); ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
<?php
	}
}
?>
	</table>
<tr>
	<td colspan="2"><h6><input name="Submit" type="submit" value="<?php echo _("Refresh")?>" tabindex="<?php echo ++$tabindex;?>"></h6></td>
</tr>
</table>

<script language="javascript">
<!--
var theForm = document.asteriskinfo;
//-->
</script>
</form>

<?php

function convertActiveChannel($sipChannel, $channel = NULL){
	if($channel == NULL){
		print_r($sipChannel);
		exit();
		$sipChannel_arr = explode(' ', $sipChannel[1]);
		if($sipChannel_arr[0] == 0){
			return 0;
		}else{
			return count($sipChannel_arr[0]);
		}
	}elseif($channel == 'IAX2'){
		$iaxChannel = $sipChannel;
	}
}

function getActiveChannel($channel_arr, $channelType = NULL){
	if(count($channel_arr) > 1){
		if($channelType == NULL || $channelType == 'SIP'){
			$sipChannel_arr = $channel_arr;
			$sipChannel_arrCount = count($sipChannel_arr);
			$sipChannel_string = $sipChannel_arr[$sipChannel_arrCount - 2];
			$sipChannel = explode(' ', $sipChannel_string);
			return $sipChannel[0];
		}elseif($channelType == 'IAX2'){
			$iax2Channel_arr = $channel_arr;
			$iax2Channel_arrCount = count($iax2Channel_arr);
			$iax2Channel_string = $iax2Channel_arr[$iax2Channel_arrCount - 2];
			$iax2Channel = explode(' ', $iax2Channel_string);
			return $iax2Channel[0];
		}
	}
}

function getRegistration($registration, $channelType = 'SIP'){
	if($channelType == NULL || $channelType == 'SIP'){
		$sipRegistration_arr = $registration;
		$sipRegistration_count = count($sipRegistration_arr);
		return $sipRegistration_count-3;
		
	}elseif($channelType == 'IAX2'){
		$iax2Registration_arr = $registration;
		$iax2Registration_count = count($iax2Registration_arr);
		return $iax2Registration_count-3;
	}
}

function getPeer($peer, $channelType = NULL){
	global $astver_major, $astver_minor;
	global $astver;
	if(count($peer) > 1){	
		if($channelType == NULL || $channelType == 'SIP'){
			$sipPeer = $peer;
			$sipPeer_count = count($sipPeer);
			$sipPeerInfo_arr['sipPeer_count'] = $sipPeer_count -3;
			$sipPeerInfo_string = $sipPeer[$sipPeer_count -2];
			$sipPeerInfo_arr2 = explode('[',$sipPeerInfo_string);
			$sipPeerInfo_arr3 = explode(' ',$sipPeerInfo_arr2[1]);
			if (version_compare($astver, '1.4', 'ge')) { 
				$sipPeerInfo_arr['online'] = $sipPeerInfo_arr3[1] ;
				$sipPeerInfo_arr['offline'] = $sipPeerInfo_arr3[3];

				$sipPeerInfo_arr['online-unmonitored'] = $sipPeerInfo_arr3[6];
				$sipPeerInfo_arr['offline-unmonitored'] = $sipPeerInfo_arr3[8];
			}else{
				$sipPeerInfo_arr['online'] = $sipPeerInfo_arr3[0];
				$sipPeerInfo_arr['offline'] = $sipPeerInfo_arr3[3];
			}
			return $sipPeerInfo_arr;
			
		}elseif($channelType == 'IAX2'){
			$iax2Peer = $peer;
			$iax2Peer_count = count($iax2Peer);
			$iax2PeerInfo_arr['iax2Peer_count'] = $iax2Peer_count -3;
			$iax2PeerInfo_string = $iax2Peer[$iax2Peer_count -2];
			$iax2PeerInfo_arr2 = explode('[',$iax2PeerInfo_string);
			$iax2PeerInfo_arr3 = explode(' ',$iax2PeerInfo_arr2[1]);
			$iax2PeerInfo_arr['online'] = $iax2PeerInfo_arr3[0];
			$iax2PeerInfo_arr['offline'] = $iax2PeerInfo_arr3[2];
			$iax2PeerInfo_arr['unmonitored'] = $iax2PeerInfo_arr3[4];
			return $iax2PeerInfo_arr;
		}
	}
}

function buildAsteriskInfo(){
	global $astman;
	global $astver;
	$uptime = _("Uptime: ");
	$activesipchannels = _("Active SIP Channel(s): ");
	$activeiax2channels = _("Active IAX2 Channel(s): ");
	$sipregistry = _("Sip Registry: ");
	$iax2registry = _("IAX2 Registry: ");
	$sippeers = _("Sip Peers: ");
	$iax2peers = _("IAX2 Peers: ");

	
	$arr = array(
		$uptime => "show uptime",
		$activesipchannels => "sip show channels",
		$activeiax2channels => "iax2 show channels",
		$sipregistry => "sip show registry",
		$iax2registry => "iax2 show registry",
		$sippeers => "sip show peers",
		$iax2peers => "iax2 show peers",	
	);
	
	if (version_compare($astver, '1.4', 'ge')) {
		$arr[$uptime] = 'core show uptime';
	}
	
	$htmlOutput  = '<div style="color:#000000;font-size:12px;margin:10px;">';
	$htmlOutput  .= '<table border="1" cellpadding="10">';

	foreach ($arr as $key => $value) {
	
		$response = $astman->send_request('Command',array('Command'=>$value));
		$astout = explode("\n",$response['data']);

		switch ($key) {
			case $uptime:
				$uptime = $astout;
				$htmlOutput .= '<tr><td colspan="2">'.$uptime[1]."<br />".$uptime[2]."<br /></td>";
				$htmlOutput .= '</tr>';
			break;
			case $activesipchannels:
				$activeSipChannel = $astout;
				$activeSipChannel_count = getActiveChannel($activeSipChannel, $channelType = 'SIP');
				$htmlOutput .= '<tr>';
				$htmlOutput .= "<td>".$key.$activeSipChannel_count."</td>";
			break;
			case $activeiax2channels:
				$activeIAX2Channel = $astout;
				$activeIAX2Channel_count = getActiveChannel($activeIAX2Channel, $channelType = 'IAX2');
				$htmlOutput .= "<td>".$key.$activeIAX2Channel_count."</td>";
				$htmlOutput .= '</tr>';
			break;
			break;
			case $sipregistry:
				$sipRegistration = $astout;
				$sipRegistration_count = getRegistration($sipRegistration, $channelType = 'SIP');
				$htmlOutput .= '<tr>';
				$htmlOutput .= "<td>".$key.$sipRegistration_count."</td>";
			break;
			case $iax2registry:
				$iax2Registration = $astout;
				$iax2Registration_count = getRegistration($iax2Registration, $channelType = 'IAX2');
				$htmlOutput .= "<td>".$key.$iax2Registration_count."</td>";
				$htmlOutput .= '</tr>';
			break;
			case $sippeers:
				$sipPeer = $astout;
				$sipPeer_arr = getPeer($sipPeer, $channelType = 'SIP');
				if($sipPeer_arr['offline'] != 0){
					$sipPeerColor = 'red';
				}else{
					$sipPeerColor = '#000000';
				}
				$htmlOutput .= '<tr>';
			  if (version_compare($astver, '1.4', 'ge')) { 
				  $htmlOutput .= "<td>".$key."<br />&nbsp;&nbsp;&nbsp;&nbsp;"._("Online: ").$sipPeer_arr['online']."<br />&nbsp;&nbsp;&nbsp;&nbsp;"._("Online-Unmonitored: ").$sipPeer_arr['online-unmonitored'];
          $htmlOutput .= "<br />&nbsp;&nbsp;&nbsp;&nbsp;"._("Offline: ")."<span style=\"color:".$sipPeerColor.";font-weight:bold;\">".$sipPeer_arr['offline']."</span><br />&nbsp;&nbsp;&nbsp;&nbsp;"._("Offline-Unmonitored: ")."<span style=\"color:".$sipPeerColor.";font-weight:bold;\">".$sipPeer_arr['offline-unmonitored']."</span></td>";
        } else {
				  $htmlOutput .= "<td>".$key."<br />&nbsp;&nbsp;&nbsp;&nbsp;"._("Online: ").$sipPeer_arr['online']."<br />&nbsp;&nbsp;&nbsp;&nbsp;"._("Offline: ")."<span style=\"color:".$sipPeerColor.";font-weight:bold;\">".$sipPeer_arr['offline']."</span></td>";
        }
			break;
			case $iax2peers:
				$iax2Peer = $astout;
				$iax2Peer_arr = getPeer($iax2Peer, $channelType = 'IAX2');
				if($iax2Peer_arr['offline'] != 0){
					$iax2PeerColor = 'red';
				}else{
					$iax2PeerColor = '#000000';
				}
				$htmlOutput .= "<td>".$key."<br />&nbsp;&nbsp;&nbsp;&nbsp;"._("Online: ").$iax2Peer_arr['online']."<br />&nbsp;&nbsp;&nbsp;&nbsp;"._("Offline: ")."<span style=\"color:".$iax2PeerColor.";font-weight:bold;\">".$iax2Peer_arr['offline']."</span><br />&nbsp;&nbsp;&nbsp;&nbsp;"._("Unmonitored: ").$iax2Peer_arr['unmonitored']."</td>";
				$htmlOutput .= '</tr>';
			break;
			default:
			}
		}
	$htmlOutput .= '</table>';
	return $htmlOutput."</div>";
	}
?>
