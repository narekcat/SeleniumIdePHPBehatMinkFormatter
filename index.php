<?php
/**
 * The file contains main logic of the application
 */

require_once 'BehatTestGenerator.php';

$fileOne = '/Users/narek_vardzelyan/Documents/10 hour long Training Session is created.xhtml';
$fileTwo = '/Users/narek_vardzelyan/Documents/search_wikipedia_html_testcase.html';

$BehatTestGenerator = new BehatTestGenerator($fileOne);
$BehatTestGenerator->generate();