
<?php
include_once("php_config.php");
include_once("simple_html_dom.php");
require_once('voicerss_tts.php');

$proxy = '127.0.0.1:3128'; 

function getPageContent($url, $proxy) {

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	// curl_setopt($curl, CURLOPT_PROXY, $proxy);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_REFERER, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.125 Safari/533.4");
    $str = curl_exec($curl);
    curl_close($curl);

    // Create a DOM object
    $dom = new simple_html_dom();
    // Load HTML from a string
    $dom->load($str, true, false);

    return $dom;
}

/*This value contains the link to the artcile choosen during the previous step*/
$url = $_POST["article"];
/*We extract the text content of the title */
$titre_article_texte = $_POST["title"];

/*The goal is to take each word of the title and make a google search image with those keywords*/
// Construction of the google image search url :
// BEGIN
$keywords_search_image = explode(' ', $titre_article_texte);
$keyword_number = count($keywords_search_image);
$url_images = "";
for ($z = 0; $z < $keyword_number; $z ++) {
	if ($z == $keyword_number -1) {
		$url_images = $url_images.$keywords_search_image[$z];
	}
	else 
	{
		$url_images = $url_images.$keywords_search_image[$z]."+";
	}
}
// $url_images = $url_images."&source=lnms&tbm=isch";
// END 

// Displaying a few information to check the success of what happened before
echo '<h3> URL IMAGE SEARCH </h3> ';
echo $url_images;
echo '<br>';
echo '<h3> URL ARTICLE </h3> ';
echo $url;
echo '<br>';

// GENRATION OF THE DOM OF THE ARTICLE PAGE
$url_dom = getPageContent($url, $proxy);

// NOT CURRETNLY USED 
// $url_dom_images = getPageContent($url_images, $proxy);

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="ISO-8859-1">
<title>Test appli "SAVAGE" automatic generation of news based videos</title>
</head>
	<body>
		<h1> Artcile choosen : </h1>
		<h2>The text seems to be : </h2>
		<?php
				file_put_contents("text_outputs\mydata2.txt", "");
				foreach ($url_dom->find('p') as $text) {
					echo '<p>';
					echo $text->outertext;
					echo '</p>';


					
				    $data = $text->plaintext;
					$nb_words = str_word_count($data);
					echo '<h3> Number of words = ';
					echo $nb_words;
					echo '</h3>';
					if ($nb_words > 31) {
						$ret = file_put_contents('text_outputs\mydata2.txt', $data, FILE_APPEND | LOCK_EX);
						if($ret === false) {
							die('There was an error writing this file');
						}
						else {
							echo "$ret bytes written to file";
						}
					}
					else {
						echo '<h4>This content is not useful enough to be written </h4>';
					}
				}

				//exec('test_balabolka.bat');

				//creating the audio file from the text file "mydata2.txt" 
				echo '<h1>Voice RSS Reading text </h1>';
				$text_to_read = file_get_contents('text_outputs\mydata2.txt');

				echo $text_to_read;
				echo '<br>';
				

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

				file_put_contents('audio_outputs/audio_voice_rss.mp3', base64_decode($voice['response']));
				echo '<h3> MP3 recording is done  </h3>';

				//print_r($voice);
				//echo '<audio src="' . $voice['response'] . '" autoplay="autoplay"></audio>';
				

		?>
		<h2>The images seems to be : </h2>
		<?php
		// Executing the phantomJS script which creates the file urls_images.txt 
		$command_line ='phantomjs script3.js '.'"'.$url_images.'"';
		echo '<h3>Command line </h3>';
		echo $command_line;
		echo '<br>';
		$giving_auth = shell_exec('chmod 777 *');
		$phantom_response  = shell_exec($command_line);
		// print_r($phantom_response);
		// var_dump($phantom_response);
		echo '<h3> Echo exec script response  </h3>';
		echo $phantom_response;
		echo '<br>';

		echo '<h3> Trying to download images  </h3>';
		// $urls_array = explode(" ", $phantom_response);
		$urls_array = preg_split("/[^\w]*([\s]+[^\w]*|$)/", $phantom_response, -1, PREG_SPLIT_NO_EMPTY);
		$number_images_found = count($urls_array);
		for ($i=0; (($i < $number_images_found) && ($i <10)); $i++) { 
			echo '<h4> Url of the image  </h4>';
			echo $urls_array[$i];
			echo '<br>';
			// $imageData = base64_encode(file_get_contents($urls_array[$i]));
			// echo '<img src="data:image/jpeg;base64,'.$imageData.'">';
			$url_here = $urls_array[$i];
			//$image = 'video_outputs/image_test_2.jpg';
			// $url_here = $urls_array[$i];
			$image = 'video_outputs/image'.$i.'.jpg';
			
			// file_put_contents($image, file_get_contents($url_here));
			$giving_auth_2 = shell_exec('chmod -R 777 video_outputs');
			$ch = curl_init($url_here);
			$fp = fopen($image, 'wb');
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);
			
			echo "<img src='".$image."' alt='error'>";
		}

		?>
		
	</body>
</html>



<!--This JS script is not currently used (didnt worked) -->
<!--<script>
			// document.getElementById('targetFrame').contentWindow.targetFunction();
			var cont=document.getElementById('targetFrame').contentWindow.getElementsByTagName("body")[0];
			var imgs=document.getElementById('targetFrame').contentWindow.getElementsByTagName("a");
			var i=0;var divv= document.createElement("div");
			var aray=new Array();var j=-1;
			while(++i<imgs.length){
				if(imgs[i].href.indexOf("/imgres?imgurl=http")>0){
				divv.appendChild(document.createElement("br"));
				aray[++j]=decodeURIComponent(imgs[i].href).split(/=|%|&/)[1].split("?imgref")[0];
				divv.appendChild(document.createTextNode(aray[j]));
				}
			}
			cont.insertBefore(divv,cont.childNodes[0]);
</script>-->
