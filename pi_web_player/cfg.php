<?php
// We are passed the instance name as constant(VIDDIR) from index.html
//  We can handle multiple instances/video sub-directories
//
// Control file directory
define('CTRLDIR', '/var/www/dist/pi_web_player_control');
// global files
define('FIFO', CTRLDIR.'/omxplayer_fifo');
define('STATFILE', CTRLDIR.'/status');
define('CONTINOUS', CTRLDIR.'/continous.setting');
define('LOOPALL', CTRLDIR.'/loopall.setting');
define('TEMPERATUREFILE', '/var/www/dist/stat/current.temperature');
// per-instance files
define('VIDLIST', CTRLDIR."/vid_list_$VIDDIR");
define('CURRENTVIDFILE', CTRLDIR."/vid_currentname_$VIDDIR");
define('CURRENTVIDPTR', CTRLDIR."/vid_pointer_$VIDDIR");
//Location of videos - subdirectory per-instance
define('VIDPATHBASE', '/var/www/dist/vids');
define('VIDPATH', VIDPATHBASE."/$VIDDIR");
?>
