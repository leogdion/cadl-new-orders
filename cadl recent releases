<?php
global $titles;
$titles = array();
function add_titles ($element) {
   global $titles;
   //echo($element->plaintext);
   //print_r($element);
   $titles[] = array($element->plaintext, 'https://opac.cadl.org/' . $element->attr['href']);
}
require 'simple_html_dom.php';
$urlFormat =    "https://opac.cadl.org/search~S15?/ftlist^bib121%%2C%d%%2C0%%2C286/mode=2";
$bluRayFormat = "https://opac.cadl.org/search~S15?/ftlist^bib122%%2C%d%%2C0%%2C135/mode=2";
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
      $dom = new simple_html_dom();
      $dom->load($html);
   }
   array_walk($dom->find(".briefcitTitle a"), 'add_titles');
   echo 'completed parsing page ' . ($page + 1) . ' ...' . "\n";
}


print_r($titles);
?>