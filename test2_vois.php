<?php
require_once('voicerss_tts.php');

echo '<h1>TESTs</h1>';

$tts = new VoiceRSS;
$voice = $tts->speech([
    'key' => 'b3a50426f7914067aa6d40ce1d46ae71',
    'hl' => 'fr-fr',
    'src' => "Bonjour, je m'apelle Théophile, je suis actuellement en stage de fin d'études chez Orange, et je suis à la recherche d'un CDI pour l'an prochain",
    'r' => '0',
    'c' => 'mp3',
    'f' => '44khz_16bit_stereo',
    'ssml' => 'false',
    'b64' => 'true'
]);

echo '<audio src="' . $voice['response'] . '" autoplay="autoplay"></audio>';

?>