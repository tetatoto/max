<?php
include_once("php_config.php");
include_once("simple_html_dom.php");

$proxy = '127.0.0.1:3128'; 

function getPageContent($url, $proxy) {

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	//curl_setopt($curl, CURLOPT_PROXY, $proxy);
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

$subject1 = $_POST["subject1"];
$subject2 = $_POST["subject2"];

$url_google = 'https://www.google.fr/#q='.$subject1.'&tbm=nws';
$url = 'http://lemoteur.orange.fr/?module=lemoteur&bhv=actu&kw='.$subject1.'%20'.$subject2;
$url_dom = getPageContent($url, $proxy);



?>

<!DOCTYPE html>
<html>
<head>
<meta charset="ISO-8859-1">
<title>Test appli "SAVAGE" automatic generation of news based videos</title>
</head>
	<body>
		<h1> Result page </h1>
		<h2>The choosen subject is :</h2>
			<?php
				echo '<br> '.$subject1.'<br>';
			?>
		<h2>The url parsed is :</h2>
			<?php
				echo '<br> '.$url.'<br>';
			?>
		<h2>The page found is  :</h2>
		
			<?php

				$i = 0;
				foreach ($url_dom->find('div.entry') as $title) {
					$i = $i +1;
					echo '<h3>Article '.$i.': <br> ';
					/*echo $title->plaintext;*/
					/*print_r($title);*/
					echo '<br> </h3>';
					
					echo '<h4>Text :</h4>';
					echo $title->plaintext;
					echo '<br>';
					

					echo '<h4>Link :</h4>';
					$link_to_article = $title->find('a')[0];
					echo $link_to_article;

					$text_title_article = $link_to_article->plaintext;
					echo '<br>';
					echo  'texte titre article <br>';
					echo $text_title_article;
					echo '<br>';
					
					/*echo '<input type="button" onclick="submit" name="'.$link_to_article.'" value="Choose this article">';*/
					


/*					print_r($title);
					echo '<br> <br> <br>';*/
					/*foreach ($title->find('div.mw') as $title2) {
						echo '<h4>children : ';
						echo $title2->plaintext;
						echo '</h4>';
					}*/
					
				}
				/*print_r($url_dom);*/
				$variable = $url_dom->find('div.entry');

			?>
		<form method="POST" action="getting_the_text.php">
			<h4>Wich article do you want to choose ?</h4>
			<?php
			for ($j=1; $j <= $i ; $j++) { 
				#value contient le lien vers l'article
				$value = $variable[$j-1]->find('a')[0]->href;
				/*value 2 contient le texte du titre de l'article*/
				$value2 = $variable[$j-1]->find('a')[0]->plaintext;
			?>

				<!--The value send by the form is $value-->
				<input type="radio" name="article" value="<?php echo htmlspecialchars($value) ?>" checked> <?php echo htmlspecialchars($j) ?> <br>
				<input type="hidden" name="title" value="<?php echo htmlspecialchars($value2) ?>"/>
			<?php
			}
			?>

			<input type="submit" name="submit" value="submit">

			
		</form>
	</body>
</html>