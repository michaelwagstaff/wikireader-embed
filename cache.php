<?php
require "utils.php";
require "pageFetch.php";
//This script is for caching pages from a given dictionary page.
$url = "Dictionary_of_chemical_formulas";
$utils = new utils;
$directory = $utils->openPage($url);
$conn = $utils->database();
$cache = new cache;
$maxLength = 0;
$cache->getLinks($directory);
class cache 
{
	function getLinks($page)
	{
		$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($page);
		$counter = 0;
		$outerCounter = 0;
		$toRemove = [];
		/*$toRemove[] = $dom->getElementById('toc');
		$toRemove[] = $dom->getElementById('collapsibleTable0');*/
		foreach ($toRemove as $item)
		{
			$item->parentNode->removeChild($item);
		}


		$tagToSplit = $dom->getElementById ("External_links");
		$toSplitString = $dom->saveHTML($tagToSplit);
		$pageContent = $dom->saveHTML();
		$pageContent = preg_split("[".$toSplitString."]", $pageContent)[0];
		$dom->loadHTML($pageContent);


		$tables[] = $dom->getElementsByTagName('table');
		$links = [];
		foreach($tables as $table)
		{

			$links[] = $dom->getElementsByTagName('a');

			foreach($links as $tableLinks)
			{

				foreach($tableLinks as $link)
				{
					$counter++;
					if($counter>=36)
					{
						$symbol = $link->parentNode->parentNode->firstChild->c14n();
						echo $this->saveLink($link->nodeValue,$link->getAttribute('href'), $symbol, $dom);
						/*echo $link->nodeValue;
						echo $link->getAttribute('href'), '<br>';*/
					}
				}
			}
		}
	}
	function saveLink($title, $url, $symbol, $dom)
	{
		if(strpos($url,"index.php"))
		{
			return "";
		}
		$title = ucwords($title);
		$uri = explode("/",$url)[2];
		$pageFetch = new pageFetch;
		
		$pageContent = $pageFetch->constructor($uri);
		$pageContent = $pageFetch->extremeCache($pageContent);
		if(strlen($pageContent)>$GLOBALS['maxLength'])
		{
			$GLOBALS['maxLength'] = strlen($pageContent);
		}
		$conn = $GLOBALS['utils']->database();
		$pageContent = addslashes($pageContent);
		$symbol = substr($symbol, 4, strlen($symbol) - 9);
		$sql = "INSERT INTO wikipages VALUES (\"$title\",\"$symbol\",\"$url\",\"$pageContent\",0,".strlen($pageContent).");";
		if ($conn->query($sql) === TRUE) {
			echo "New record created successfully";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
		$conn->close();

		return $title . "  " . $uri . "<br>" . $GLOBALS['maxLength'] . "<br>";
	}
}
?>