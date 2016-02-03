<?php
error_reporting(E_ALL);
require_once 'JsHttpRequest.php';
$JsHttpRequest = new JsHttpRequest ( 'windows-1251' );
// Grab commandline argument for VIDDIR since cfg.php needs it
$VIDDIR = $_REQUEST['viddir'];
// Load variables
require_once 'cfg.php';

function play($file,$viddir) {
	$err = '';
	exec('pgrep omxplayer', $pids);  //omxplayer is running?
	if ( empty($pids) ) {  //NO
		@unlink (FIFO);
		posix_mkfifo(FIFO, 0777);
		chmod(FIFO, 0777);
		// Update Status file to PLAYING
                $statfd = fopen(STATFILE, "w");
                fwrite($statfd, "PLAYING\n");
                fclose($statfd);
		//  Run omx_play.sh with arguments
		shell_exec ( getcwd().'/omx_play.sh '.escapeshellarg($file).' '.escapeshellarg($viddir));
		$out = 'playing '.basename($file);

	} else {   //YES
		$out = '';
		$err = 'omxplayer is already runnning';
	}
	return array ( 'res' => $out, 'err' => $err );
}

function shutdown() {
    $err = '';
    shell_exec ( getcwd().'/shutdown_php.sh ');
    $out = 'Issuing shutdown ';
    return array ( 'res' => $out, 'err' => $err );
}

function reboot() {
    $err = '';
    shell_exec ( getcwd().'/reboot_php.sh ');
    $out = 'Issuing reboot ';
    return array ( 'res' => $out, 'err' => $err );
}

function togglefile($file) {
    // This function will open a file and toggle the contents between YES and NO
    $err = '';
    $current = file_get_contents($file);
    $statfd = fopen($file, "w");
    if ($current == "YES\n") {
      fwrite($statfd, "NO\n");
      $out = 'Set to NO';
    } else {
      fwrite($statfd, "YES\n");
      $out = 'Set to YES';
    }
    fclose($statfd);
    return array ( 'res' => $out, 'err' => $err );
}


function send($command) {
	$err = '';
	exec('pgrep omxplayer', $pids);
	if ( !empty($pids) ) {
		if ( is_writable(FIFO) ) {
			if ( $fifo = fopen(FIFO, 'w') ) {
				stream_set_blocking($fifo, false);
				if ($command == 'next_vid') {
				 // To play next video, we send a "quit" to the current omxplayer 
				 //   The monitor script will notice and then kick off next video
				 fwrite($fifo, 'q');
				 $out = 'skipping';
				} else {
				 fwrite($fifo, $command);
				}
				fclose($fifo);
				if ($command == 'q') {
					// QUIT player
					//  Set status file to STOPPED (even if we are just skipping)
					$statfd = fopen(STATFILE, "w");
					fwrite($statfd, "STOPPED\n");
					fclose($statfd);
					sleep (1);
					@unlink(FIFO);
					$out = 'stopped';
				}
				if ($command == 'p') {
					// PAUSE / UNPAUSE toggle
					$current = file_get_contents(STATFILE);
                                        $statfd = fopen(STATFILE, "w");
					if ($current == "PAUSED\n") {
					  fwrite($statfd, "PLAYING\n");
					  $out = 'unpause';
					} else {
                                          fwrite($statfd, "PAUSED\n");
					  $out = 'paused';
					}
                                        fclose($statfd);
                                }

			}
		}
	} else {
		$out = '';
		$err .= 'not running';
	}
	return array ( 'res' => $out, 'err' => $err );
}

$act = $_REQUEST['act'];
unset($result);

switch ($act) {

	case 'play':
        // Pass arguments
	$result = play($_REQUEST['arg'],$VIDDIR);
	break;

	case 'stop';
	$result = send('q');
	break;

	case 'pause';
	$result = send('p');
	break;

	case 'volup';
	$result = send('+');
	break;

	case 'voldown';
	$result = send('-');
	break;

	case 'seek-30';
	$result = send(pack('n',0x5b44));
	break;

	case 'seek30';
	$result = send(pack('n',0x5b43));
	break;

	case 'seek-600';
	$result = send(pack('n',0x5b42));
	break;

	case 'seek600';
	$result = send(pack('n',0x5b41));
	break;

	case 'speedup';
	$result = send('1');
	break;

	case 'speeddown';
	$result = send('2');
	break;

	case 'nextchapter';
	$result = send('o');
	break;

	case 'prevchapter';
	$result = send('i');
	break;

	case 'nextaudio';
	$result = send('k');
	break;

	case 'prevaudio';
	$result = send('j');
	break;

	case 'togglesubtitles';
	$result = send('s');
	break;

	case 'nextsubtitles';
	$result = send('m');
	break;

	case 'prevsubtitles';
	$result = send('n');
	break;

	case 'next_vid';
	$result = send('next_vid');
	break;

        case 'shutdown':
        $result = shutdown();
        break;

        case 'reboot':
        $result = reboot();
        break;

	case 'continous':
        $result = togglefile(CONTINOUS);
        break;

	case 'loopall':
        $result = togglefile(LOOPALL);
        break;

	default:
	$err = 'wrong command';
}

$GLOBALS['_RESULT'] = $result;
?>
