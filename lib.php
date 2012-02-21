<?php

header( 'Content-Type: audio/wav');

// Possible Letters
$letters = array('a.wav','b.wav','c.wav','d.wav','e.wav','f.wav','g.wav','h.wav','i.wav','j.wav','k.wav','l.wav','m.wav','n.wav','o.wav','p.wav','q.wav','r.wav','s.wav','t.wav','u.wav','v.wav','w.wav','x.wav','y.wav','z.wav');
$string = array();

if (!isset($_GET['q'])) {
    for ($i=0; $i < 6 ; $i++) { 
        $string[$i] = $letters[array_rand($letters)];
    }
} else {
    $string = preg_split('//', $_GET['q'], -1, PREG_SPLIT_NO_EMPTY);
    foreach ($string as $key => $value) {
        $string[$key] = $value.".wav";
    }
}

echo joinwavs($string);

// http://www.splitbrain.org/blog/2006-11/15-joining_wavs_with_php
function joinwavs($wavs){
    $fields = join('/',array( 'H8ChunkID', 'VChunkSize', 'H8Format',
                              'H8Subchunk1ID', 'VSubchunk1Size',
                              'vAudioFormat', 'vNumChannels', 'VSampleRate',
                              'VByteRate', 'vBlockAlign', 'vBitsPerSample' ));
    $data = '';
    foreach($wavs as $wav){
        $fp     = fopen('audio/'.$wav,'rb');
        $header = fread($fp,36);
        $info   = unpack($fields,$header);
        // read optional extra stuff
        if($info['Subchunk1Size'] > 16){
            $header .= fread($fp,($info['Subchunk1Size']-16));
        }
        // read SubChunk2ID
        $header .= fread($fp,4);
        // read Subchunk2Size
        $size  = unpack('vsize',fread($fp, 4));
        $size  = $size['size'];
        // read data
        $data .= fread($fp,$size);
    }
    return $header.pack('V',strlen($data)).$data;
}
?>