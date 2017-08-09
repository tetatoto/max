#!/bin/bash



video_duration="$(mp3info -p "%S\n" audio_outputs/audio_voice_rss.mp3)"

echo "***************************************************************"
echo "STARTING THE GENERATION OF THE FULL VIDEO"
echo "_______________________________________________________________"
echo "The duration of the video is $video_duration seconds "


# FIRST STEP : creating a loop with the template video in order to reach the right duration (given in argument)

let 'template_duration=13'
let 'current_duration=0'

echo "The duration of the template is $template_duration seconds"
echo "_______________________________________________________________"

# 1.1 // loop to get the full duration video

# Initiate the final video with the right duration looping the template

while [ $current_duration -lt $video_duration ]
do 
	printf "file '%s'\n" templates/template_woman.mp4 >> mylist.txt;
	((current_duration=current_duration+template_duration))
	#echo "current duration is $current_duration"

done

ffmpeg -f concat -i mylist.txt -c copy video_outputs/generated_video.mp4

echo "final duration is $current_duration"

# 1.2 // Adding the sound file to the video

ffmpeg -i "video_outputs/generated_video.mp4" -i "audio_outputs/audio_voice_rss.mp3" -c copy -shortest -map 0:v0 -map 1:a:0 "video_outputs/generated_video_with_sound.mp4"

# SECOND STEP : Adding images to the background on the top right corner of the video

# 2.1 // Converting images to the right size 

echo 'CONVERTING IMAGES IN SMALLER ONES'
mogrify -resize 300x250 picture_outputs/*.jpg
echo 'END CONVERTION'

# 2.2 // Adding resized images to the video with ffmpeg

let 'current_timeline=0'
let 'image_cursor=0'

#while [ $current_timeline -lt $video_duration ]
#do
#	let 'next=current_timeline+10'
	ffmpeg -y -i video_outputs/generated_video_with_sound.mp4 -i "picture_outputs/image${image_cursor}.jpg" -filter_complex "[0:v][1:v] overlay=640-284-20:5:enable='between(t,0,20)'" -pix_fmt yuv420p -c:a copy video_outputs/generated_video_with_images.mp4
#	((current_timeline=current_timeline+10))
#	((image_cursor++))
#
#done











