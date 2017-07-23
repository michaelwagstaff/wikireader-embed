# wikireader
This is a clean wikipedia reader, designed for use on my website [periodicstats.com](https://periodicstats.com).

This is currently prerelease so use at your own risk, it may contain bugs.

This wikireader is designed to load wikipedia pages, presenting the data in a more user friendly manner.

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

The class contains three public functions, of which I recommend one for use.

They are as follows:

```php
public function loadPage(string $uri,[string $filePathToInsert])
```

The following functions were designed for my use, so I am currently finishing up some customisation. They will not work for most pages.

```php
public function cache(string $uri, string $dbname, [string $servername], [string $username], [string $password], [string $tableName])
public function loadPageFromDB(string $uri, string $dbname, [string $servername], [string $username], [string $password], [string $tableName])
```

The expected formatting and defaults are as follows:

$uri should contain the section of the url after the final slash. Internally https://wikipedia.org/wiki/ is inserted before this.

$filePathToInsert defaults to "wikiLoad.php" if there is no input. This is the file that will handle the implementation of my class.

$dbname should be the name of your database.

$servername defaults to "localhost".

$username defaults to "root".

$password defaults to an empty string.

$tableName defaults to "wikipages".




Notes:
$uri in the cache function should link to a directory page, currently. I may later offer the ability to cache pages based on other metrics.
It is strongly advised you use a different username and password combination
