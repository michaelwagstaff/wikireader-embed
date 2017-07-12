<?php
require "utils.php";
//This script is for caching pages from a given dictionary page.
$url = "Dictionary_of_chemical_formulas";
$utils = new utils;
$directory = $utils->openPage($url);
$conn = $utils->database();
$cache = new cache;
$cache->getLinks($directory);

class cache 
{
	function getLinks($page)
	{
		$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($page);
		$toRemove = [];
		/*$toRemove[] = $dom->getElementById('toc');
		$toRemove[] = $dom->getElementById('collapsibleTable0');*/
		foreach ($toRemove as $item)
		{
			$item->parentNode->removeChild($item);
		}
		$tables[] = $dom->getElementsByTagName('table');
		$links = [];
		foreach($tables as $table)
		{
			$links[] = $dom->getElementsByTagName('a');
			foreach($links as $tableLinks)
			{

				foreach($tableLinks as $link)
				{
					echo $link->nodeValue;
	    			echo $link->getAttribute('href'), '<br>';
				}
			}
		}
	}
}
?>