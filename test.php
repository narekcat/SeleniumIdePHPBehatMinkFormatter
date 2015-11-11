<?php
/**
 * The file contains main logic of the application
 */

require __DIR__ . '/vendor/autoload.php';

$firstFilePath = '/Users/narek_vardzelyan/Documents/ten hour long Training Session is created.xhtml';
$secondFilePath = '/Users/narek_vardzelyan/Documents/search_wikipedia_html_testcase.html';

$BehatTestGenerator = new Fouraitch\BehatTestGenerator($firstFilePath);
$BehatTestGenerator->generate();