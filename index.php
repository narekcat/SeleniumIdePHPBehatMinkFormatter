<?php

require_once 'SeleniumRCToBehatMinkFormatter.php';

$fileOne = '/Users/narek_vardzelyan/Documents/10 hour long Training Session is created.xhtml';
$fileTwo = '/Users/narek_vardzelyan/Documents/search_wikipedia_html_testcase.html';

$formatter = new SeleniumRCToBehatMinkFormatter($fileOne);
$formatter->format();