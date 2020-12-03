#!/bin/sh

VOD=0

while [[ $# -gt 0 ]]
do
	key="$1"
	case $key in
		-o | --vod)
			VOD=1
			shift
			;;
	esac
done

for i in [ 1 2 3 ]
do
	curl -s http://dens.tv/tvpage_octo/channelgen/$i
done > .tmplistdens
cat .tmplistdens | \
jq -r '.data[]' | \
jq -r -s 'sort_by(.seq|tonumber) | .[] | "[\(.seq)]\t\(.title)"' | \
pr -l1 -3 -t

read -p "Choose channel number : " id
if [[ $VOD == 1 ]]; then
	curl -s https://dens.tv/tvpage_octo/epgchannel2/0/$id > .tmpvoddens
	cat .tmpvoddens | jq -r '.data[] | "[\(.seq)] \(.title) -> [\(.starttime)]"'
	read -p "Select VOD code : " vod
	M3U8_URL=$(cat .tmpvoddens | jq --arg vod $vod -r '.data[] | select(.seq ==
	$vod) | .play' | sed 's/amp;//g')
	echo $M3U8_URL
else
	M3U8_URL=$(cat .tmplistdens | \
	jq --arg id $id -r '.data[] | select(.seq == $id) | .playurl')
fi

echo $M3U8_URL
youtube-dl -F $M3U8_URL
read -p "Select format code (low code for lower quality, high code for higher
quality) : " format
mpv --hls-bitrate=$format $M3U8_URL
