<?php
class wikireader
{
	public function loadFromDB($uri, $dbname = "", $servername = "localhost", $username = "root", $password = "", $tableName = "wikipages")
	//Please do not use root with no password, I probably should not be encouraging this
	{
		$conn = $this->database($servername, $username, $password, $dbname);
		echo $uri;
		$sql = "SELECT Contents FROM $tableName WHERE URI = '$uri'";
		$result = $conn->query($sql);
		$data = $result->fetch_row();
		echo $data[0];
	}
	public function loadPage($uri, $filePathToInsert = "")
	{
		$wikiPage = $this->getPage($uri);
		$newPage = $this->pageProcess($wikiPage, $filePathToInsert, false);
		return $newPage;
	}
	public function cache($uri, $firstLinkIndex, $dbname, $servername = "localhost", $username = "root", $password = "", $tableName = "wikipages")
	{
		$directory = $this->openPage($uri);
		$conn = $this->database($servername,$username,$password,$dbname);
		$this->getLinks($directory,$firstLinkIndex,$tableName,$conn);
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
	function pageProcess($page, $filePathToInsert, $caching)
	{
		$dom = new DOMDocument();
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
		$cssLink->setAttribute('href','/wikireader/wiki.css');
		//Improve method for adding files before release
		//Make into an array of inputs
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
		$pageContent = $dom->saveHTML();
		if($filePathToInsert!== "" && $filePathToInsert !== false)
		{
			if($caching = true)
			{
				$pageContent = preg_replace('#href="/wiki/#', 'onClick="openWikiPageCached(this.href);" href="'.$filePathToInsert.'?uri=', $pageContent);
			}
			else
			{
				$pageContent = preg_replace('#href="/wiki/#', 'onClick="openWikiPage(this.href);" href="'.$filePathToInsert.'?uri=', $pageContent);
			}
		}
		else
		{
			if($caching = true)
			{
				$pageContent = preg_replace('#href="/wiki/#', 'onClick="openWikiPageCached(this.href);" href="wikiLoad.php?uri=', $pageContent);
			}
			else
			{
				$pageContent = preg_replace('#href="/wiki/#', 'onClick="openWikiPage(this.href);" href="wikiLoad.php?uri=', $pageContent);
			}
		}
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
	function getLinks($page,$firstLinkIndex,$tableName,$conn)
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
					if($counter>=$firstLinkIndex)
					{
						echo $this->saveLink($link->nodeValue,$link->getAttribute('href'), $dom, $tableName,$conn);
					}
				}
			}
		}
		$conn->close();
	}
	function saveLink($title, $uri, $dom, $tableName,$conn)
	{
		if(strpos($uri,"index.php"))
		{
			return "";
		}
		//Crude way of finding broken links
		$title = ucwords($title);
		$uri = explode("/",$uri)[2];
		$pageContent = $this->getPage($uri);
		$pageContent = $this->pageProcess($pageContent,false, true);
		$pageContent = $this->extremeCache($pageContent);
		$pageContent = addslashes($pageContent);
		$sql = "INSERT INTO $tableName VALUES (\"$title\",\"$symbol\",\"$uri\",\"$pageContent\",0,".strlen($pageContent).");";
		if ($conn->query($sql) === TRUE) {
			echo "New record created successfully";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}

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
	function database($servername = "localhost", $username = "root", $password = "", $dbname = "")
	{
		$conn = new mysqli($servername, $username, $password, $dbname);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$conn->set_charset("utf8");
		return $conn;
	}
}
?>
