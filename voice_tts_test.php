<?php
require_once('voicerss_tts.php');

echo '<h3> Beginning </h3>';

$text_to_read="C'est ce texte que tu dois lire mon petit robot";

echo '<h4> '.$text_to_read.' </h4>';

$tts = new VoiceRSS;
				$voice = $tts->speech([
					'key' => 'b3a50426f7914067aa6d40ce1d46ae71',
					'hl' => 'fr-fr',
					'src' => $text_to_read,
					'r' => '0',
					'c' => 'mp3',
					'f' => '44khz_16bit_stereo',
					'ssml' => 'false',
					'b64' => 'true'
				]);

file_put_contents('audio_voice_rss.mp3', base64_decode($voice['response']));

echo '<h3> Done ;) </h3>';

?>
