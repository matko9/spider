<?php

ini_set('max_execution_time', 0);

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once './vendor/autoload.php';

use Goutte\Client;

class Sleep
{
  private $count = 0;
  private $tc;

  public static function getInstance()
  {
    static $instance = null;
    if (null === $instance) {
      $instance = new static();
    }

    return $instance;
  }

  protected function __construct()
  {
    $this->tc = new TorControl\TorControl(
      array(
        'server' => '127.0.0.1',
        'port'   => 9051,
        'password' => 'test',
        'authmethod' => 1
      )
    );
  }

  private function __clone()
  {
  }

  private function __wakeup()
  {
  }

  public function sleep(){

    sleep(1);
    $this->count++;
    if($this->count > 20) {

      $this->tc->connect();

      $this->tc->authenticate();

      // Renew identity
      $res = $this->tc->executeCommand('SIGNAL NEWNYM');

      // Echo the server reply code and message
      echo $res[0]['code'].': '.$res[0]['message'];

      // Quit
      $this->tc->quit();
      echo "new tor identity\n";
      sleep(10);
    }
  }

}

$poor_bastard = "http://www.azlyrics.com/";

$client = new Client();

$client->setHeader('User-Agent', "Googlebot");

//$guzzle = $client->getClient();

//$guzzle->setDefaultOption('proxy', 'socks5://127.0.0.1:9050');
//$client->setClient($guzzle);

$crawler = $client->request('GET', $poor_bastard);

$lyricsTxt = fopen("songLyrics.txt", "w");

$status_code = $client->getResponse()->getStatus();
echo $status_code . "\n";

$crawler->filterXPath('//*[@id="artists-collapse"]/li/div/a')->each(function ($node) use ($client, $lyricsTxt) {
  Sleep::getInstance()->sleep();
  $page = $client->click($node->link());

  $page->filterXPath('//html/body/div[2]/div/div/a')->each(function ($node) use ($client, $lyricsTxt) {

    $artist = $client->click($node->link());
    $artistDetails = $node->text();

    $artist->filterXPath('//*[@id="listAlbum"]/a[@target="_blank"]')->each(function ($node) use (
      $client,
      $artistDetails,
      $lyricsTxt
    ) {
      Sleep::getInstance()->sleep();

      $songTitle = $node->text();

      $song = $client->click($node->link());

      $text = $song->filterXPath('//html/body/div[3]/div/div[2]/div[6]')->text();

      fwrite($lyricsTxt, $text);
      Sleep::getInstance()->sleep();

    });

  });

});

fclose($lyricsTxt);
