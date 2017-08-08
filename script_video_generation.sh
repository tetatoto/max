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
	printf "file '%s'\n" templates/template_man.mp4 >> mylist.txt;
	((current_duration=current_duration+template_duration))
	#echo "current duration is $current_duration"

done

ffmpeg -f concat -i mylist.txt -c copy video_outputs/generated_video.mp4

echo "final duration is $current_duration"

# 1.2 // Adding the sound file to the video

ffmpeg -i "video_outputs/generated_video.mp4" -i "audio_outputs/audio_voice_rss.mp3" -codec copy -shortest "video_outputs/generated_video_with_sound.mp4"

# SECOND STEP : Adding images to the background on the top right corner of the video

# 2.1 // 
# image='image007.jpg'
# echo "test converting image size command line"
# convert "$image" -resize 200x200> miniatures/$image
# for image in ` /images/*.png /images/*.jpg /images/*.jpeg /images/*.gif 2>/dev/null`
# do
#     convert $image -thumbnail '200x200>' miniatures/$image
#     echo "one more"
# done

