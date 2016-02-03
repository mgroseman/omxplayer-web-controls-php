omxplayer-web-controls-php
==========================
This is first experimental proof-of-concept build to control omxplayer using web-enabled devices.
Written in PHP+some shell scripting.

Remember to allow group write to installation directory.

Install:
1) Install and configure a web server.
2) Unpack tar file into /var/www/, or whereever you want that is accessable via the web server.  (Note:  This will overwrite your /var/www/index.html, so you might want to make a backup if you have other webpages)
3) Load setup.php via a browser:  http://localhost/pi_web_player/setup.php, or similar.
4) Change directories via setup.php to whatever matching your environment.  Also see the other notes about "sudo" if you want shutdown/reboot/clear screen capabilities.
5) Edit /var/www/index.html to point to your installation directory and video files.  Note the "viddir=ABC" parameter in the examples.  That must match a subdirectory under your main video location.  That variable will manage the playlists for each directory.  Don't put spaces in it.
6) You can also mount USB storage and create symbolic links pointing to it.  See the example /vids/ directory.
7) Place .mp4s or whatever additional format video files you Pi has a license for into the video directories.
8) If you want the Pi temperature output, add the cron line located in pi_web_player/setupfiles/cronjob.teperature into the "pi" user's crontab.  (Or whatever user that can write to the output file)
8) Connect to the index.html and see if it works!  (eg. http://localhost/)

Original annoucement:
https://www.raspberrypi.org/forums/viewtopic.php?f=35&t=15947

Original screenshots:
Here are small clues to get the idea what it is:

http://img21.imageshack.us/img21/8317/sscczz.jpg

http://www.youtube.com/watch?v=8QeWDKIpAAw

Updates May 2013 from Mike Roseman:
=============
Vastly reworked the single video at a time interface, into an inteface that can sequentially play files from multiple repositories.
Reason for change:
  - I wanted a car video player for my toddler.  That had the requirement that I be able to change the video and start/stop without actually seeing the screen.  So I took Juggler's script and bent it to my needs.  I didn't want to be selecting a new video every 10 minutes, so I added continous play.
  - I'll leave the details of that for another time, but the jist is I made the Pi an Access Point so our cell phones/tablet can connect to the Pi via Wifi and attached it to a tiny screen and speakers.  Then we can just look at the Web interface to control the screen.
  - Pardon me, but this is the first time I ever used PHP, so it might be a little messy... :)

New features:
 - It remembers what file is is playing, and can loop through all files in a directory.  Once it views them all it will optionally loop continously.
 - It can keep track of its place in multiple video subdirectories.  (eg.  vids/Show1/, vids/Show2/, vids/Show3/)
    - You pass the subdir as a HTML argument. (eg. .../index.php?viddir=TEST)  See index.html for examples.
 - Added buttons to shutdown and reboot the computer
 - Added button to skip to next video
 - After shutdown or stopping, it will restart from the last played file.  Or you can select another.
 - Added a temperature readout for the Pi
    - example cronjob in: pi_web_player/setupfiles/cronjob.temperature
 - Multiple phones/web browers can control the same video display 
 - /var/www/pi_web_player_control/ (or whatever you change that to) only contains control files for the various pointers, FIFO, and other status files.   I made it separate if you want the main script directory read-only for the http user.
    - Oops, but the the config files are still in the main dir so it needs to be read/write, need to think about that one.

Notes:
  - Refreshing the statuses doesn't automatically happen, you need to hit Reload.  I did this on the theory that it would save power if it wasn't always refreshing itself.  Maybe someone has a better way.
  - Button:  LOOP ALL - means it will start from the first video after hitting the last video 
  - Button:  CONTINUOUS - means it will play all videos in the subdirectory in sequence.  Otherwise it stops after this one
  - setup.php is different.  It still has some notes about "sudo" you should look at, and does a few permission checks.  You can also edit cfg.sh and cfg.php by hand, which is probably easier anyway.  Just make sure they have the same values!   
  - setup.php also has some refresh issues.  The new values won't be reflected unless you Reload

JugglerLKR didn't add a copyright or license that I see to the code.
I, mgroseman, license my commits under the MIT license

