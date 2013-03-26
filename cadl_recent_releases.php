<?php
global $titles;
$titles = array();
function add_titles ($element, $key, $type) {
   global $titles;
   //echo($element->plaintext);
   //print_r($element);
   $titles[] = array($element->plaintext, 'https://opac.cadl.org/' . $element->attr['href'], $type);
}
function is_isbn ($element) {
   return preg_match('/((?:\d{9}|\d{12}))/i'
      , $element->plaintext);
}
require 'vendor/autoload.php';

$listUrl = "http://opac.cadl.org/ftlist/";
$urlFormat =    "https://opac.cadl.org/search~S15?/ftlist^bib%d%%2C%d%%2C0%%2C%d/mode=2";

echo 'getting lists...' . "\n";

$html = file_get_contents($listUrl);
$dom = new simple_html_dom();
$dom->load($html);
$elements = $dom->find(".browseEntry");
$entries = array();
for ($index = 0; $index < count($elements); $index++) {
   $title =$elements[$index]->find(".browseEntryData a");
   //preg_match('/http:\/\/opac\.cadl\.org\/search~S15\?\/ftlist\^bib(\d+)%2C1%2C0%2C(\d+)\/mode=2/', $title[0]->href, $matches);
   preg_match('/bib(\d+)%2C1%2C0%2C(\d+)/', $title[0]->href, $matches);
   $id = $matches[1];
   $count = $matches[2];
   //$count =$elements[$index]->find(".browseEntryEntries");
   preg_match('/^New Orders for ((\w+)(\s\w+)?)\s(by\s)?(\w+)$/', $title[0]->plaintext, $matches);
   if (!array_key_exists($matches[2], $entries)) {
      $entries[$matches[2]] = array($id, 1, $count);
   }   //print_r($item[0]->plaintext);
}

foreach ($entries as $key=>$value) {
   echo $key . "\n";
   $pageCount = PHP_INT_MAX;
   for ($page = 0; $page < $pageCount; $page++) {
      if ($pageCount === PHP_INT_MAX) {
         $pageCount = count($dom->find(".browsePager a"))/2;
      }
      $value[1] = $page * 50 + 1;
      echo 'downloading page ' . ($page + 1) . ' ...' . "\n";
      $url = vsprintf($urlFormat, $value);
      $html = file_get_contents($url);
      //echo $url . "\n";
      $dom = new simple_html_dom();
      $dom->load($html);
      array_walk($dom->find(".briefcitTitle a"), 'add_titles', $key);
      echo 'completed parsing page ' . ($page + 1) . ' ...' . "\n";
   }
}

for ($index = 0; $index < count($titles); $index++) { //count($titles); $index++) {
   echo $index . "/" . count($titles) . "\n";
   $html = file_get_contents($titles[$index][1]);
   $dom = new simple_html_dom();
   $dom->load($html);
   //$elems = array_filter($dom->find('.bibInfoData'), 'is_isbn');
   $elems = $dom->find('.bibInfoData');
   //echo(count($elems));
   //print_r($elems);
   //echo $elems[1]->plaintext;
   //echo $elems[2]->plaintext;
   for ($jndex = 0; $jndex < count($elems); $jndex++) {
      if (preg_match('/((?:\d{9}|\d{12}))/i', $elems[$jndex]->plaintext)){
         $isbn = intval($elems[$jndex]->plaintext);
         break;
      }
   }
   $titles[$index][] = $isbn;
}

/*
echo 'downloading page ' . 1 . ' ...' . "\n";
$url = sprintf($urlFormat, 1);
$html = file_get_contents($url);
$dom = new simple_html_dom();
$dom->load($html);
$pages = count($dom->find(".browsePager a"))/2;
$titles = array();
//print_r($dom->find(".briefcitTitle a"));
for ($page = 0; $page < $pages; $page++) {
   if ($page > 0) {
      echo 'downloading page ' . ($page + 1) . ' ...' . "\n";
      $url = sprintf($urlFormat, $page*50 + 1);
      $html = file_get_contents($url);
      echo $url . "\n";
      $dom = new simple_html_dom();
      $dom->load($html);
   }
   array_walk($dom->find(".briefcitTitle a"), 'add_titles');
   echo 'completed parsing page ' . ($page + 1) . ' ...' . "\n";
}

*/
print_r($titles);
?>