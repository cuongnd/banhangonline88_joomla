<?php

var_dump(filter_var("qwertyu123456dfghj", FILTER_SANITIZE_NUMBER_INT));
var_dump(filter_var("asd123123.asd123.23", FILTER_SANITIZE_NUMBER_INT));
var_dump(filter_var("123,23", FILTER_SANITIZE_NUMBER_INT));
var_dump(filter_var("", FILTER_SANITIZE_NUMBER_INT));
var_dump(filter_var("0", FILTER_SANITIZE_NUMBER_INT));
var_dump(filter_var("asd123.2asd", FILTER_SANITIZE_NUMBER_INT));
var_dump(filter_var("qwertyuiop", FILTER_SANITIZE_NUMBER_INT));
var_dump(filter_var("123.4", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
var_dump(filter_var("123,4", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
var_dump(filter_var("123.4", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND));
var_dump(filter_var("123,4", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND));
var_dump(filter_var("123.4e", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_SCIENTIFIC));
var_dump(filter_var("123,4E", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_SCIENTIFIC));
var_dump(filter_var("qwe123,4qwe", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
var_dump(filter_var("werty65456.34", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
var_dump(filter_var("234.56fsfd", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
var_dump(filter_var("", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));

echo "Done\n";
?>
