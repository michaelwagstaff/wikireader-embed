<?php
class pageFetch
{
	public function constructor($uri)
	{
		//Import heading if applicable here
		$wikiPage = $this->getPage($uri);
		$newPage = $this->pageProcess($wikiPage);
		return $newPage;
	}
	function getPage($uri)
	{
		
		if(isset($_GET['url'])) {
			$url = $_GET["url"];
		}
		else if ($uri !== "")
		{
			$url = $uri;
		}
		else
		{
			$url = "Silver_nitrate";
		}
		$page = $GLOBALS['utils']->openPage($url);
		return $page;
	}
	function pageProcess($page)
	{
		$dom = new DOMDocument();

		//libxml_use_internal_errors(true);
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
		$stringToSplit = $dom->saveHTML($tagToSplit);
		$pageContent = $dom->saveHTML();
		/*
		if(strlen($stringToSplit))
		{
			$toSplitString = $dom->saveHTML($tagToSplit);
			

			$pageContent = preg_split("[".$toSplitString."]", $pageContent)[0];
			$pageContent = substr($pageContent, 0, -10);
		}
		*/
		$pageContent = preg_replace('#href="/wiki/#', 'href="load.php?url=', $pageContent);
		return $pageContent;
		$dom->loadHTML($pageContent);
	}
}
?>