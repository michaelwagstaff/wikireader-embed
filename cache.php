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
					if($counter>=36 && $counter<=50)
					{
						echo $link->parentNode->parentNode->firstChild->c14n();
						echo $this->saveLink($link->nodeValue,$link->getAttribute('href'), "", $dom);
						/*echo $link->nodeValue;
						echo $link->getAttribute('href'), '<br>';*/
					}
				}
			}
		}
	}
	function saveLink($title, $url, $symbols, $dom)
	{
		if(strpos($url,"index.php"))
		{
			return "";
		}
		$title = ucwords($title);
		$uri = explode("/",$url)[2];
		$pageFetch = new pageFetch;
		
		$pageContent =  $pageFetch->constructor($uri);
		if(strlen($pageContent)>$GLOBALS['maxLength'])
		{
			$GLOBALS['maxLength'] = strlen($pageContent);
		}
		
		return $title . "  " . $uri . "<br>" . $GLOBALS['maxLength'] . "<br>";
	}
}
?>