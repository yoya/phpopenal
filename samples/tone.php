<?php

/* sound wave generate */

$freq = 44000; // [Hz]
$period = 0.5; // [sec]

$half_tone_diff = pow(2, 1/12);

$tone_list = array(
	'C' => 440 / pow($half_tone_diff, 9),
	'D' => 440 / pow($half_tone_diff, 7),
	'E' => 440 / pow($half_tone_diff, 5),
	'F' => 440 / pow($half_tone_diff, 4),
	'G' => 440 / pow($half_tone_diff, 2),
	'A' => 440,
	'B' => 440 * pow($half_tone_diff, 2),
	'C2' => 440 * pow($half_tone_diff, 3),
);

$time = $period * count($tone_list);

$data = '';

foreach ($tone_list as $tone) {
    $amp = 100; // max:127
    for ($i = 0 ; $i < $freq * $period ; $i++) {
        $value = sin(2 * M_PI * $tone * $i / $freq) * $amp + 127;
        $data .= chr($value);
    }
}

// openal setting
$dev = openal_device_open();
$con = openal_context_create($dev);
openal_context_current($con);

$buff = openal_buffer_create();
openal_buffer_data($buff, AL_FORMAT_MONO8, $data, $freq);
$src = openal_source_create();
openal_source_set($src, AL_BUFFER, $buff);

// play sound
openal_source_play($src);
sleep($time + 1);

// destroy
openal_context_destroy($con);
openal_device_close($dev);

echo "OK\n";

exit(0);
