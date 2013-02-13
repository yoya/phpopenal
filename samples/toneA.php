<?php

// sin wave data generate
$freq = 8000; // [Hz] telephone sound quality
$toneA = 440;  // [Hz] time signal sound
$period = 3;   // [sec] sound length
$amp = 60; // max 127
 
$data = '';
$theta = 2 * M_PI * $toneA / $freq;
    for ($i = 0 ; $i < $freq * $period ; $i++) {
        $value = sin($theta  * $i) * $amp + 127;
        $data .= chr($value);
    }
 
// OpenAL construct
$dev = openal_device_open();
$con = openal_context_create($dev);
openal_context_current($con);
 
// audio data setting
$buff = openal_buffer_create();
openal_buffer_data($buff, AL_FORMAT_MONO8, $data, $freq);
$src = openal_source_create();
openal_source_set($src, AL_BUFFER, $buff);
 
// play sound
openal_source_play($src);
sleep($period + 1);
 
// destruct
openal_context_destroy($con);
openal_device_close($dev);
