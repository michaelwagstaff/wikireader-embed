<?php
class wikireader
{
	public function loadPage($uri = "Silver_nitrate", $useCaching = false)
	{
		$wikiPage = $this->getPage($uri);
		$newPage = $this->pageProcess($wikiPage);
		return $newPage;
	}
	public function cache($uri)
	{
		$directory = $this->openPage($url);
		$conn = $this->database();
		$this->getLinks($directory);
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
		$page = $this->openPage($url);
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
	function extremeCache($pageContent)
	{
		$pageContent = str_replace(array("\n","\r","\t"),'',$pageContent);
		$pageContent = preg_replace('/<!--(.*)-->/Uis', '', $pageContent);
		$dom = new DOMDocument();
		$dom->loadHTML($pageContent);
		$xpath = new DOMXpath($dom);
		$toRemove = [];
		$toRemove[] = $xpath->query("//*[contains(concat(' ', @class, ' '), ' mw-indicators ')]");
		$toRemove[] = $xpath->query("//*[contains(concat(' ', @class, ' '), ' plainlinks ')]");
		$toRemove[] = $xpath->query("//*[contains(concat(' ', @class, ' '), ' hatnote ')]");
		$toRemove[] = $xpath->query("//*[contains(concat(' ', @class, ' '), ' reflist ')]");
		$toRemove[] = $xpath->query("//*[contains(concat(' ', @class, ' '), ' navbox ')]");
		$toRemove[] = $xpath->query("//*[contains(concat(' ', @class, ' '), ' mw-editsection ')]");
		$toRemove[] = $dom->getElementById('siteNotice');
		$toRemove[] = $dom->getElementById('contentSub');
		$toRemove[] = $dom->getElementById('jump-to-nav');
		$remove = [];
		foreach ($toRemove as $individualItem) {
			foreach($individualItem as $item)
			{
				$remove[] = $item;
			}
		}
		foreach ($remove as $item)
		{
				$item->parentNode->removeChild($item);
		}
		$pageContent = $dom->saveHTML();
		return $pageContent;
	}
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
						echo $link->parentNode->parentNode->firstChild->c14n();
						echo $this->saveLink($link->nodeValue,$link->getAttribute('href'), "", $dom);
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
		$pageContent = $this->constructor($uri);
		$pageContent = $this->extremeCache($pageContent);
		$conn = $this->database();
		$pageContent = addslashes($pageContent);
		$sql = "INSERT INTO wikipages VALUES (\"$title\",\"$symbol\",\"$url\",\"$pageContent\",0,0);";
		if ($conn->query($sql) === TRUE) {
			echo "New record created successfully";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
		$conn->close();

		return $title . "  " . $uri . "<br>" . "<br>";
	}
	function openPage($url)
	{
		set_time_limit(0);
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
	public function database()
	{
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "periodic";
		$conn = new mysqli($servername, $username, $password, $dbname);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$conn->set_charset("utf8");
		return $conn;
	}
}
?>