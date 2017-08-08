# wikireader
This is a clean wikipedia reader, designed for use on my website [periodicstats.com](https://periodicstats.com).

It is designed to be used for embedding wikipedia pages in web applications, and offers custmomisability in how it is implemented. 

It is also designed to allow caching in a database, to speed up page loads, and contains some basic CSS (still in progress) to get you started.

It is designed to be generic, so should work in most use cases.

## Installation

Download the repository or add as a submodule to the root of your existing code

You can then import the code, using:
```php
require "./wikireader/index.php";
```
## Usage

Firstly create a new instance of the class as follows:

```php
$wikireader = new wikireader();
```

The class contains three public functions, one for loading a page, using your server as a proxy working in real time. The other two functions are for caching pages, with approprate modification, into a database, and for loaing them on demand.

They are as follows:

```php
public function loadPage(string $uri,[string $filePathToInsert])

public function cache(string $uri, int $firstLinkIndex, string $dbname, [string $servername], [string $username], [string $password], [string $tableName])

public function loadPageFromDB(string $uri, string $dbname, [string $servername], [string $username], [string $password], [string $tableName])
```

The expected formatting and defaults are as follows:

$uri should contain the section of the url after the final slash. Internally https://wikipedia.org/wiki/ is inserted before this.

$filePathToInsert defaults to "wikiLoad.php" if there is no input. This is the file that will handle the implementation of my class.

$firstLinkIndex should be the index of the first link on the page you want to cache (can be found by looking at code in dev tools).

$dbname should be the name of your database.

$servername defaults to "localhost".

$username defaults to "root".

$password defaults to an empty string.

$tableName defaults to "wikipages".



### Database Structure

For using the database related function you should use mysql.

Your table should contain four columns which are detailed as follows:

```
VARCHAR Title
VARCHAR URI
MEDIUMTEXT Contents (Datatype depends on size of pages)
MEDIUMINT Length
```



### Notes:

+ $uri in the cache function should link to a directory page, currently. I may later offer the ability to cache pages based on other metrics.

+ It is strongly advised you use a different username and password combination to the one listed above.

+ If you are looking to save chemical symbols check out the 'periodicstats' branch. This is non-generic and includes my caching of symbols.

## To-Do

+ Add a demo - will probably be on the site listed above, when fully integrated.

+ I would like to add the ability to cache individual pages, rather than from a directory page

+ Improve CSS to make the design more responsive, and to cover more elements

+ Add further options for caching, such as redis for greater speed, although this would be memory intensive, and could take some time to properly program
