<?php
/**
 * The file contains main logic of the application
 */

require __DIR__ . '/vendor/autoload.php';

$firstFilePath = '/Users/narek_vardzelyan/Documents/ten hour long Training Session is created.xhtml';
$secondFilePath = '/Users/narek_vardzelyan/Documents/search_wikipedia_html_testcase.html';
$thirdFilePath = '/Users/narek_vardzelyan/Documents/Testing php_net test two.html';
$fourthFilePath = '/Users/narek_vardzelyan/Documents/Testing php_net test three.html';
$fifthFilePath = '/Users/narek_vardzelyan/Documents/github_test_two.xhtml';

$BehatTestGenerator = new Fouraitch\SeleniumIdeFormatter\BehatTestGenerator($thirdFilePath);
$BehatTestGenerator->generate();