<html>
	<head>
		<title>PHP OMXPlayer Control Setup</title>
		<style type="text/css">
			.error{ color:red; font-weight:bold; }
		</style>
	</head>
	<body>

		<?php
		error_reporting(E_ALL);
		//  Set a junk VIDDIR since cfg.php needs it
                $VIDDIR = 'EXAMPLE';
                // Load current variables 
                require_once 'cfg.php';

              // Current values - you might need to refresh after a change
		echo ' Current Working directory = '.getcwd().'';
		echo '<h1>Current cfg.php parameters:<br></h1>';
                echo ' <i> NOTE: This script has some refresh issues, you probably need to hit Refresh on the browser to see the new values. </i><p>';
		echo ' CTRLDIR = '.CTRLDIR.'<br>';
		//echo ' FIFO = '.FIFO.'<br>';
		//echo 'VIDPATH = '.VIDPATH.'<br>';
		echo ' VIDPATHBASE = '.VIDPATHBASE.'<br>';
		echo ' TEMPERATUREFILE = '.TEMPERATUREFILE.'<br>';
		//echo ' vidpathbase =' . $_POST['vidpathbase'] . ' <br>';
		echo '<p>';

	     // Form 
              $script = basename(__FILE__); // the name of this script
              $VIDPATHBASE=$_REQUEST['vidpathbase'];
              if ( "$VIDPATHBASE" == '' ) {
                $VIDPATHBASE=VIDPATHBASE;
              }
              $CTRLDIR=$_REQUEST['ctrldir'];
              if ( "$CTRLDIR" == '' ) {
                $CTRLDIR=CTRLDIR;
              }
              $TEMPERATUREFILE=$_REQUEST['temperaturefile'];
              if ( "$TEMPERATUREFILE" == '' ) {
                $TEMPERATUREFILE=TEMPERATUREFILE;
              }
              echo "<h1>Change cfg.php/cfg.sh values:</h1>";
              echo "<FORM action=\"\" method=\"post\">";
              echo "<b>Path to contain control files (CTRLDIR) = </b><input type=\"text\" size=\"30\" name=\"ctrldir\" value=\"{$CTRLDIR}\" /><br>";
              echo "<b>Path to video parent dir (VIDPATHBASE) = </b><input type=\"text\" size=\"30\" name=\"vidpathbase\" value=\"{$VIDPATHBASE}\" /><br>";
              echo " NOTE: Actual videos must be under subdirectories.  (eg. /VIDPATHBASE/DoctorWho)<br>";
              echo "<b>Path to a file with current temperature (TEMPERATUREFILE) = </b><input type=\"text\" size=\"30\" name=\"temperaturefile\" value=\"{$TEMPERATUREFILE}\" /><br>";
              echo "<input type=\"hidden\" name=\"write\" value=\"WRITE\" /><br>";
              echo "<INPUT type=\"submit\" value=\"Write config\"> <INPUT type=\"reset\" value=\"Reset Boxes\">";
              echo "</FORM>";
        //echo "<br><a href=\"{$script}?vidpathbase={$VIDPATHBASE}&ctrldir={$CTRLDIR}&save=save_dont\"><button type=\"button\">save config</button></a>";

 //  Write Form fields if submitted 
 //  Very evil RegEx statements, but a simple explaination is they do a "sed" substitute for the matching lines 
if ($_REQUEST['write'] == 'WRITE') {
  
  system("cat cfg.sh | /bin/sed -e \"s:\(^CTRLDIR=\).*:\\1$CTRLDIR:\" -e \"s:\(^VIDPATHBASE=\).*:\\1$VIDPATHBASE:\" -e \"s:\(^TEMPERATUREFILE=\).*:\\1$TEMPERATUREFILE:\" > cfg.sh.tmp ; mv cfg.sh.tmp cfg.sh ",$output);
  if ($output != 0 ) {
   echo("<h2 class=\"error\">cfg.sh writing returned an error: $output</h2>");
  }

  system("cat cfg.php | /bin/sed -e \"s:\(^define('CTRLDIR', '\).*\(');\):\\1$CTRLDIR\\2:\" -e \"s:\(^define('VIDPATHBASE', '\).*\(');\):\\1$VIDPATHBASE\\2:\" -e \"s:\(^define('TEMPERATUREFILE', '\).*\(');\):\\1$TEMPERATUREFILE\\2:\" > cfg.php.tmp ; mv cfg.php.tmp cfg.php ",$output) ;
  if ($output != 0 ) {
  echo("<h2 class=\"error\">cfg.php writing returned an error: $output</h2>");
  }

//commandline equivalents for testing
//cat cfg.sh | sed -e "s:\(^CTRLDIR=\).*:\1/test/hi:" -e "s:\(^VIDPATHBASE=\).*:\1/test/hi2:" -e "s:\(^TEMPERATUREFILE\).*:\1/test/hi3:" >> testcfg.sh
//cat cfg.php | sed -e "s:\(^define('CTRLDIR', '\).*\(');\):\1/test/hi\2:" -e "s:\(^define('VIDPATHBASE', '\).*\(');\):\1/test/hi2\2:" -e "s:\(^define('TEMPERATUREFILE', '\).*\(');\):\1/test/hi3\2:"
}

       // Checks - you might need to refresh after making a change
	echo '<h2>Checks:<br></h2>';

                       if ( is_writable("$CTRLDIR") ) {
                                echo "$CTRLDIR is writable - OK<br>";
                        } else {
                                echo "$CTRLDIR is not writable for httpd user<br>";
                               die();
                        }
                       if ( file_exists("$VIDPATHBASE") ) {
                                echo "$VIDPATHBASE exists - OK<br>";
                                echo "$VIDPATHBASE Contents:<br><blockquote><i>";
				system ("ls -F $VIDPATHBASE");
                                echo "</blockquote></i><br>";
                                 
                        } else {
                                echo "$VIDPATHBASE does not exist<br>";
                                echo "$VIDPATHBASE must exist and should have videos under a subdirectory<br>";
                               die();
                        }
                      if ( file_exists("$TEMPERATUREFILE") ) {
                                echo "$TEMPERATUREFILE exists - OK<br>";
                        } else {
                                echo "$TEMPERATUREFILE does not exist<br>";
                                echo "$TEMPERATUREFILE is not critical to operations<br>";
                        }



			$processUser = posix_getpwuid(posix_geteuid());
			if ( is_writable('/dev/vchiq') ) {
				echo '/dev/vchiq is writable - OK<br>';
			} else {
				echo '/dev/vchiq is not writable for httpd user<br>';
				echo 'you have to run shell command:<br>';
				echo 'sudo usermod -a -G video '.$processUser['name'].'<br>';
				echo 'this will allow http server user which runs omxplayer access /dev/vchiq to display video<br>';
				die();
			}

			if ( posix_mkfifo(FIFO, 0777) ) {
				echo FIFO.' is writable - OK<br>';
			} else {
				echo 'can\'t create '.FIFO.' - please fix persmissions!<br>';
				die();
			}

			if ( chmod(FIFO,0777) ) {
				echo FIFO.' permissions - OK<br>';
			} else {
				echo 'can\'t change permissions for '.FIFO.' - please fix persmissions!<br>';
				die();
			}

			unlink(FIFO);

                    // Notes
                        echo '<h2>Notes:<br></h2>';

			echo "<h3>Please note - if you want to clear screen before player start please modify cls.sh to your needs and run this command from shell:</h3>";
			echo "<p><b><i>sudo sh -c 'echo \"".$processUser['name']." ALL=(ALL) NOPASSWD: /bin/sh -c ./cls.sh, /sbin/shutdown -r now, /sbin/shutdown -h now\" >/etc/sudoers.d/".$processUser['name']." && chmod 0640 /etc/sudoers.d/".$processUser['name']."'</i></b></p>";
			echo "<p>This command allows cls.sh and the shutdown/reboot scripts to do necessary tasks and it can be done only using sudo.</p>";

//		}



		?>

	</body>
</html>
