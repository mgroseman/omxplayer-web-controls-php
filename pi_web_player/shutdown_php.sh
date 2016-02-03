#!/bin/sh
#Not sure if this shutdown sound works:
#aplay /home/pi/sound/leave.wav &
sync ; sync ; sync
sudo /sbin/shutdown -h now &
