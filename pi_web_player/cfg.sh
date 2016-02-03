# We are passed the instance name as argument from the php scripts
#  We can handle multiple instances/video sub-directories

VIDDIR=$2
# Control file directory
CTRLDIR=/var/www/dist/pi_web_player_control
# global files
FIFO=$CTRLDIR/omxplayer_fifo
STATFILE=$CTRLDIR/status
CONTINOUS=$CTRLDIR/continous.setting
LOOPALL=$CTRLDIR/loopall.setting
TEMPERATUREFILE=/var/www/dist/stat/current.temperature
# per-instance files
VIDLIST=$CTRLDIR/vid_list_$VIDDIR
CURRENTVIDFILE=$CTRLDIR/vid_currentname_$VIDDIR
CURRENTVIDPTR=$CTRLDIR/vid_pointer_$VIDDIR
#Location of videos - subdirectory per-instance
VIDPATHBASE=/var/www/dist/vids
VIDPATH=${VIDPATHBASE}/${VIDDIR}

#Export all variables
export FIFO VIDPATHBASE VIDPATH STATFILE CURRENTVIDFILE TEMPERATUREFILE CONTINOUS LOOPALL CURRENTVIDPTR VIDLIST CTRLDIR
