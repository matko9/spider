<?php
ini_set('max_execution_time', 0);

error_reporting(E_ALL);
ini_set('display_errors', '1');


require_once './vendor/autoload.php';

use Goutte\Client;



function getProxy(){

  $proxies = array(
    'http://107.182.17.149:7808',
    'http://104.41.151.86:80',
    'http://45.55.131.56:3128',
  );

  $proxy =  $proxies[mt_rand(0, count($proxies) - 1)];

  echo "Using proxy: ".$proxy . PHP_EOL;

  return $proxy;
}


$poor_bastard = "http://www.azlyrics.com/";


$client = new Client();


$client->setHeader('User-Agent', "Googlebot");


//$guzzle = $client->getClient();

//$guzzle->setDefaultOption('proxy', getProxy());
//$client->setClient($guzzle);


$crawler = $client->request('GET', $poor_bastard);

$lyricsTxt = fopen("lyrics.txt", "w");

$crawler->filterXPath('//*[@id="artists-collapse"]/li/div/a')->each(function ($node) use ($client, $lyricsTxt) {
  sleep(1);
  $page = $client->click($node->link());

  $page->filterXPath('//html/body/div[2]/div/div/a')->each(function ($node) use ($client, $lyricsTxt) {

    $artist = $client->click($node->link());
    $artistDetails = $node->text();

    $artist->filterXPath('//*[@id="listAlbum"]/a[@target="_blank"]')->each(function ($node) use (
      $client,
      $artistDetails,
      $lyricsTxt
    ) {
      sleep(0.5);
      $songTitle = $node->text();

      $song = $client->click($node->link());

      $text = $song->filterXPath('//html/body/div[3]/div/div[2]/div[6]')->text();

      $data = json_encode(array(
        "artist" => $artistDetails,
        "title" => $songTitle,
        "lyrics" => $text
      ));

      fwrite($lyricsTxt, $data);
      sleep(0.2);
      echo("\n\n" . $data);

    });


  });


});


