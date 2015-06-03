<?php
ini_set('max_execution_time', 0);

error_reporting(E_ALL);
ini_set('display_errors', '1');


require_once './vendor/autoload.php';
require_once './error.php';

use Goutte\Client;

$poor_bastard = "http://www.azlyrics.com/";


$client = new Client();

$crawler = $client->request('GET', $poor_bastard);

$crawler->filterXPath('//*[@id="artists-collapse"]/li/div/a')->each(function ($node) use ($client) {

  $page = $client->click($node->link());

  $page->filterXPath('//html/body/div[2]/div/div/a')->each(function ($node) use ($client) {

    $artist = $client->click($node->link());
    $artistDetails = $node->text();

    $artist->filterXPath('//*[@id="listAlbum"]/a[@target="_blank"]')->each(function ($node) use (
      $client,
      $artistDetails
    ) {
      $songTitle = $node->text();

      $song = $client->click($node->link());

      $text = $song->filterXPath('//html/body/div[3]/div/div[2]/div[6]')->text();

      //pisi u fajl
      var_dump(array(
        "artist" => $artistDetails,
        "title" => $songTitle,
        "lyrics" => $text
      ));
    });


  });


});


