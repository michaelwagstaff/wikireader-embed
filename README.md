# wikireader
This is a clean wikipedia reader, designed for use on my website [periodicstats.com](https://periodicstats.com).

This is currently prerelease so use at your own risk, it may contain bugs.

This wikireader is designed to load wikipedia pages, presenting the data in a more user friendly manner.

## Usage

Firstly create a new instance of the class as follows:

```php
$wikireader = new wikireader();
```

The class has two main functions that can be called (currently).

They are as follows:

```php
public function loadPage(string $uri,[bool $useCaching])
public function cache(string $uri)
```

The parameter for the cache function must currently link to a directory page.

## Installation

Download the repository or add as a submodule to the root of your existing code

You can then import the code, using 'require "wikireader/index.php";'.
