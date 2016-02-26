<?php
//Name: header.php
//Purpose: header page
//Author: Rohit Gupta rohit.gupta@colorado.edu
//Version: 1.0
//Date : 24-Feb-2016

$header = '';

$header = $header. "<html><head</head><body>";
$header = $header. "<div align=center><a href=index.php>Story List|</a>";
$header = $header . "<a href=index.php?s=50>Character List|</a>";
$header = $header . "<a href=add.php?s=4>Add Characters</a></div><hr>";
$header = $header . "<div align=center>";

echo $header;

?>