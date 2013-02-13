<?php

/* openal test */

class SoundGenerator {
    var $freq = 44100; // [Hz]
    var $half_tone_diff;
    var $tone_list;
    function __construct() {
	$half_tone_diff = pow(2, 1/12);
	// generate freq each tone
	$this->tone_list = array(
	    'C' => 440 / pow($half_tone_diff, 9),
	    'D' => 440 / pow($half_tone_diff, 7),
	    'E' => 440 / pow($half_tone_diff, 5),
	    'F' => 440 / pow($half_tone_diff, 4),
	    'G' => 440 / pow($half_tone_diff, 2),
	    'A' => 440,
	    'B' => 440 * pow($half_tone_diff, 2),
        );
    }
    // OpenAL construct
    function output($data) {
        // openal setting
        $dev = openal_device_open();
        $con = openal_context_create($dev);
        openal_context_current($con);
        
        $buff = openal_buffer_create();
        openal_buffer_data($buff, AL_FORMAT_MONO8, $data, $this->freq);
        $src = openal_source_create();
        openal_source_set($src, AL_BUFFER, $buff);
        
        // play sound
        openal_source_play($src);
        
        $time = strlen($data) / $this->freq;
        sleep($time + 1);
        
        // destroy
        openal_context_destroy($con);
        openal_device_close($dev);
    }
    // audio data generate from tone
    function toneData($tone, $period) {
        if ($tone == 'R') {
            $tone_hz = 0;
        } else {
            $tone_hz = $this->tone_list[$tone];
        }
        $amp = 100; // max:127
        $data = '';
        for ($i = 0 ; $i < $this->freq * $period ; $i++) {
            $value = sin(2 * M_PI * $tone_hz * $i / $this->freq) * $amp + 127;
            $data .= chr($value);
        }
        return $data;
    }
    // interpret music note data
    function playData($text) {
        $data = '';
        $text = str_replace(' ', '', $text);
        preg_match_all('/([C-GABR])([0-9]+)/i', $text, $matches);
        foreach ($matches[1] as $idx => $tone) {
            $note_div = $matches[2][$idx];
            $period = 4 * 0.5 / $note_div; // 0.5 = T120
            $data .= $this->toneData($tone, $period);
        }
        return $data;
    }
    // play score
    function play($texts) { 
        if (is_array($texts) === false) {
            $texts = array($texts);
        }
        $datas = array();
        $data_lens = array();
        foreach($texts as $text) {
       	    $data = $this->playData($text);
       	    $datas []= $data;
            $data_lens []= strlen($data);
        }
        $data_maxlen = max($data_lens);
        // padding data for max length. // BUG ?
        foreach($texts as $idx => $text) {
            $texts[$idx] .= str_pad(chr(127), $data_maxlen - $data_lens[$idx]);
        }
        $data = '';
        // sound mixer
        for ($i = 0 ; $i < $data_maxlen ; $i++) {
            $datum = 0;
            foreach($texts as $idx => $text) {
                $datum += ord($datas[$idx][$i]);
            }
            $data  .= chr($datum / count($texts));
        }
        $this->output($data);
    }
}

$sg = new SoundGenerator();

// Test
// $sg->play("C4 D4 E4 F4 G4 A4 B4");
// $sg->play(array("E8E16D16C8D8 E16R16 E16R16 E16R16 R8 D16R16 D16R16 D16R16")
$sg->play(array("C1C1", "R2E2E1", "R2R2G1"));

echo "OK\n";
exit(0);
