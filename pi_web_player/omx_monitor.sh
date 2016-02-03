#!/bin/sh
#Monitor the running omxplayer and kick off a new one if not manually killed or out of files
#set -x

#Load variables (argument $2 = $VIDDIR
. ./cfg.sh $2
#Load current filename/status/settings
VIDNUM=`cat $CURRENTVIDPTR`
STATUS=`cat $STATFILE`
CONTINOUS_SETTING=`cat $CONTINOUS`
LOOPALL_SETTING=`cat $LOOPALL`

#If STATUS isn't PLAYING or PAUSED, exit monitor process.  Otherwise loop
while [ "$STATUS" = "PLAYING" -o "$STATUS" = "PAUSED" ] ;
 do
  STATUS=`cat $STATFILE`
  # Is omxplayer is running?
  if [ -n "`pgrep omxplayer`" ] ; then
    # It is RUNNING.  Wait 3 seconds before next check.
    sleep 3
  elif [ "$STATUS" = "PLAYING" -o "$STATUS" = "PAUSED" ] ; then
    # omxplayer is NOT running!
    # Exit if CONTINOUS_SETTING is NO.  Will not play the next file.
    if [ "$CONTINOUS_SETTING" = "NO" ] ; then
      # Status file becomes STOPPED
      echo "STOPPED" > $STATFILE 
      exit 0
    fi
    # Else kick off the next video
    # Increment file counter
    VIDNUM=`expr $VIDNUM + 1`
    # find next filename
    NEXTVID=`grep "^${VIDNUM} " $VIDLIST | awk '{print $2}'`
    # Did we run out of files?
    if [ -z "$NEXTVID" ] ; then
        #Exit if LOOPALL_SETTING is NO.  Will not loop back to beginning.
        if [ "$LOOPALL_SETTING" = "NO" ] ; then 
          # Status file becomes STOPPED
          echo "STOPPED" > $STATFILE 
          exit 0
        fi
	#Otherwise loop back to first file
        VIDNUM=1
	NEXTVID=`grep "^${VIDNUM} " $VIDLIST | awk '{print $2}'`
	if [ -z "$NEXTVID" ] ;
          #This shouldn't occur.  Maybe there are no videos. Stopping.
          echo "STOPPED" > $STATFILE
          exit 10
        fi
    fi

    # Save this # as the "current_file" pointer
    echo $VIDNUM > $CURRENTVIDPTR
    # Save filename as the "current_file" name
    echo $NEXTVID | awk -F/ '{print $NF}'  > $CURRENTVIDFILE
    # Make sure Status file reflects we are PLAYING
    #  If we hit the NEXT VID button, the status would have been set to STOPPED
    echo "PLAYING" > $STATFILE

    #Kick off player
    #Note: This line is only run for the first video of the sequence.  The next videos are started via omx_monitor.sh
    #You can amend omxplayer arguments here if you want to (and also in omx_play.sh)
    #   It says hdmi, but this also works for my analog output
    omxplayer -p -o hdmi "$NEXTVID" < $FIFO >/dev/null 2>&1 &
    sleep 1
    echo -n . > $FIFO
  fi
done

