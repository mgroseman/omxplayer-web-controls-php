#!/bin/sh
#Load variables (argument $2 = $VIDDIR)
. ./cfg.sh $2

#Clear screen (oddly this leaves bars on screen during first video played)
sudo sh -c "./cls.sh"

#file selected (argument $1 = video filename)
SELECTION=$1

#if "current_file" selected, Restore playing from last file saved
if [ "$SELECTION" = "current_file" ] ; then
  CURRENTNAME=`cat $CURRENTVIDFILE`
  SELECTION=`grep "$CURRENTNAME" $VIDLIST | awk '{print $2}'`
  if [ -z "$SELECTION" ] ; then
    # No file found.  Either no files exist or need to select one
    echo "STOPPED" > $STATFILE
    exit 10
  fi
fi
 
#Lookup file # associated with filename
VIDNUM=`grep " $SELECTION" $VIDLIST | awk '{print $1}'`
#Save this # as the "current_file" pointer
echo $VIDNUM > $CURRENTVIDPTR
#Save filename as the "current_file" name
echo $SELECTION | awk -F/ '{print $NF}' > $CURRENTVIDFILE

#Kick off player 
#Note: This line is only run for the first video of the sequence.  The next videos are started via omx_monitor.sh
#You can amend omxplayer arguments here if you want to (and also in omx_monitor.sh)  
#   It says hdmi, but this also works for my analog output
omxplayer -p -o hdmi "$SELECTION" < $FIFO >/dev/null 2>&1 &
sleep 1
echo -n . > $FIFO

#start omx background monitor process that will kick off next video when finished
#argument $2 = $VIDDIR
./omx_monitor.sh NULL $2 > /dev/null 2>&1 &
