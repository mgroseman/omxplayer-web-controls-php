<?php
// Grab commandline argument (sent from index.html) for VIDDIR since cfg.php needs it [eg. /?viddir=OTHER]
//  This script can handle multiple video directories and each will have its own playlist.  See example index.html.
$VIDDIR=$_GET["viddir"];
// Load variables
require_once 'cfg.php';
mb_internal_encoding('UTF-8');
setlocale(LC_ALL, 'ru_RU.UTF-8');
?>
<html>
	<head>
		<title>PHP OMXPlayer Control</title>
		<style type="text/css">
			.error{ color:red; font-weight:bold; }
			.button{ height: 50px; width: 85px; }
		</style>
		<!--  Sets up Javascript Ajax fuctions to handle all button actions -->
		<script src="JsHttpRequest.js"></script>
		<script>
			function omxajax(act) {

				if (act == 'play') {
					var arg=document.getElementById('selected_file').value;
					//alert (arg);
				}

				JsHttpRequest.query(
				'omx_control.php',
				{
					// Arguments as an array:
					//  Action keyword
					"act": act,
					//  Filename = 'selected_file'
					"arg": arg,
					//  Viddir path  (specified from index.html argument)
					"viddir": "<?php echo $VIDDIR ?>"
				},
				function(result, errors) {
					if (result['err']) {
						document.getElementById('err').innerHTML = result['err'];
						//alert (result['err']);
					} else {
						if (result) {
							document.getElementById('res').innerHTML = result['res'];
							document.getElementById('err').innerHTML = '&nbsp;';
						}
					}
				},
				true //disable caching
				);
			}
		</script>
	</head>
	<body>
		<center>
			<?php
			// List of files matching this regex pattern in VIDPATH
			$files = glob(VIDPATH.'/{*.[mM][kK][vV],*.[aA][vV][iI],*.[mM][pP][4]}', GLOB_BRACE | GLOB_MARK);
			//print_r($files);
			$vids = array_filter ($files, function ($file) { if (substr($file,-1) != '/') return true;} ); //filter out directories
			//Write a simple list of found videos so we can control order for continous play
                        $fp = fopen(VIDLIST, 'w');
			$countvids=0;
                        foreach ($vids as $key=>$val) {
			     $countvids++;
                             fwrite($fp, "$countvids $val\n");
                        }

                        fclose($fp);
			?>
			<!-- Present the list of files to user -->
			<select id="selected_file">
				<?php
				// First choice always current_file
				echo '<option value="current_file">Start_Current_Video</option>';
				// Rest of choices
				foreach ($vids as $key=>$val) {
					echo '<option value="'.$val.'">'.basename($val).'</option>';
				}
				?>
			</select>

			<?php
			// Functions to read some status file contents
			function get_temp()
			{
			 // I use this for a display of temperatures
			 $tempfile = TEMPERATUREFILE;
			 $a = fopen($tempfile, "r");
			 $contents = fread($a, filesize($tempfile) );
			 print $contents;
			 fclose($a);
			}
			?>
			<?php
			function get_currentvid()
			{
			 $tempfile = CURRENTVIDFILE;
			 $a = fopen($tempfile, "r");
			 $contents = fread($a, filesize($tempfile) );
			 if ( $contents == "" ) {
				$contents = "None";
			 }
			 print $contents;
			 fclose($a);
			}
			?>
			<?php
			function get_status()
			{
			 $tempfile = STATFILE;
			 $a = fopen($tempfile, "r");
			 $contents = fread($a, filesize($tempfile) );
			 print $contents;
			 fclose($a);
			}
			?>

                <br>
		<!-- Display a few statuses -->
		<b>Current status:</b> <?php get_status(); ?> <br>
		<b>Current vid:</b> <?php get_currentvid(); ?> <br>
		<!-- This is return output from pressing buttons -->
		<b>Messages:</b> <i id="res">&nbsp;</i><i id="err">&nbsp;</i> <br>
		<b>Temp:</b> <?php get_temp(); ?>

			<!-- build Button display and actions -->
			<table cellspacing="5" cellpadding="0" border="0">
				<tr>
					<td>
						<button type="button" class="button" onclick="omxajax('voldown');">VOLUME -</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('play');">PLAY</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('volup');">VOLUME +</button>
					</td>
				</tr>
				<tr>
					<td>
						<button type="button" class="button" onclick="omxajax('seek-30');">SEEK -30</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('pause');">PAUSE</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('seek30');">SEEK +30</button>
					</td>
				</tr>
				<tr>
					<td>
						<button type="button" class="button" onclick="omxajax('seek-600');">SEEK -600</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('stop');">STOP</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('seek600');">SEEK +600</button>
					</td>
				</tr>
				<tr><td colspan="3"><hr></td></tr>
				<tr>
					<td>
						<button type="button" class="button" onclick="document.location.reload(true)">Refresh</button>
					</td>
					<td>
						<button type="button" class="button" onclick="">&nbsp;</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('next_vid');">NEXT VID</button>
					</td>
				</tr>
				<tr><td colspan="3"><hr></td></tr>
				<tr>
					<td>
						<button type="button" class="button" onclick="omxajax('loopall')">Loop All <?php echo file_get_contents(LOOPALL); ?></button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('shutdown');">Shutdown Pi</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('continous');">Continuous <?php echo file_get_contents(CONTINOUS); ?></button>
					</td>
				</tr>
				<tr><td colspan="3"><hr></td></tr>
				<tr>
					<td>
						<button type="button" class="button" onclick="omxajax('speedup');">SPEED +</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('nextchapter');">NEXT CHAPTER</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('nextaudio');">NEXT AUDIO</button>
					</td>
				</tr>
				<tr>
					<td>
						<button type="button" class="button" onclick="omxajax('speeddown');">SPEED -</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('prevchapter');">PREV CHAPTER</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('prevaudio');">PREV AUDIO</button>
					</td>
				</tr>
				<tr><td colspan="3"><hr></td></tr>
				<tr>
					<td>
						<button type="button" class="button" onclick="omxajax('prevsubtitles');">PREV SUBTITLES</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('togglesubtitles');">TOGGLE SUBTITLES</button>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('nextsubtitles');">NEXT SUBTITLES</button>
					</td>
				</tr>
				<tr><td colspan="3"><hr></td></tr>
				<tr>
					<td>
						<button type="button" class="button" onclick="">&nbsp;</button>
					</td>
					<td>
						<a href="setup.php?path=<?php echo VIDPATH;?>"><button type="button" class="button" >SETUP</button></a>
					</td>
					<td>
						<button type="button" class="button" onclick="omxajax('reboot');">Reboot Pi</button>
					</td>
				</tr>
			</table>



		</center>

	</body>
</html>

