<?php
//Import heading if applicable here
$wikiPage = getPage();
$newPage = pageProcess($wikiPage);
echo $newPage;

function getPage()
{
	
	if(isset($_GET['url'])) {
		$url = $_GET["url"];
	}
	else
	{
		$url = "Silver_nitrate";
	}
	$wikiReader = curl_init();
	curl_setopt($wikiReader, CURLOPT_URL, "https://en.wikipedia.org/wiki/" . $url);
	curl_setopt($wikiReader, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($wikiReader, CURLOPT_SSL_VERIFYPEER, false);
	$wikiPage = curl_exec($wikiReader);
	$status=curl_getinfo($wikiReader);
	if($status['http_code'] == 200 )
	{
		return $wikiPage;
	}
	else
	{
		return $status['http_code'];
	}
}
function pageProcess($page)
{
	$dom = new DOMDocument();

	libxml_use_internal_errors(true);
	$dom->loadHTML($page);
	$toRemove = [];
	$toRemove[] = $dom->getElementsByTagName('link');
	$toRemove[] = $dom->getElementsByTagName('script');
	$remove = [];
	$counter = 0;
	foreach ($toRemove as $individualItem) {
		foreach($individualItem as $item)
		{
			$remove[] = $item;
		}
	}
	$cssLink = $dom->createElement('link');
	$cssLink->setAttribute('rel','stylesheet');
	$cssLink->setAttribute('href','wiki.css');
	foreach ($remove as $item)
	{
		if($counter == 0)
		{
			$item->parentNode->replaceChild($cssLink, $item);
			$counter++;
		}
		else
		{
			$item->parentNode->removeChild($item); 
		}	
	}
	$tagToSplit = $dom->getElementById ("External_links");
	//$tagToSplit = $tagToSplit->parentNode;
	$toSplitString = $dom->saveHTML($tagToSplit);
	$pageContent = $dom->saveHTML();

	/*$pageContent = preg_split("[".$toSplitString."]", $pageContent)[0];
	$pageContent = substr($pageContent, 0, -10);*/
	$pageContent = preg_replace('#href="/wiki/#', 'href="load.php?url=', $pageContent);
	echo $pageContent;
	$dom->loadHTML($pageContent);
}
?>