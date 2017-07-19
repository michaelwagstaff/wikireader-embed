<?php
class utils
{
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