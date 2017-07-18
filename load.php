<?php
include "../wikiHeader.php";
require "utils.php";
require "pageFetch.php";
$utils = new utils;
$pageFetch = new pageFetch;
echo $pageFetch->constructor("");
include "../wikiFooter.php";
?>