<?php
ini_set('max_execution_time', 0);

error_reporting(E_ALL);
ini_set('display_errors', '1');


require_once './vendor/autoload.php';

use Goutte\Client;

class Sleep
{
  private $count = 0;

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
      $this->tor_new_identity();
      $this->count = 0;
      echo "new tor identity";
      sleep(10);
    }
  }

  /**
   * Switch TOR to a new identity.
   **/
  public function tor_new_identity($tor_ip='127.0.0.1', $control_port='9051', $auth_code=''){
    $fp = fsockopen($tor_ip, $control_port, $errno, $errstr, 30);
    if (!$fp) return false; //can't connect to the control port

    fputs($fp, "AUTHENTICATE $auth_code\r\n");
    $response = fread($fp, 1024);
    list($code, $text) = explode(' ', $response, 2);
    if ($code != '250') return false; //authentication failed

    //send the request to for new identity
    fputs($fp, "signal NEWNYM\r\n");
    $response = fread($fp, 1024);
    list($code, $text) = explode(' ', $response, 2);
    echo $text;
    if ($code != '250') return false; //signal failed

    fclose($fp);
    return true;
  }
}


$poor_bastard = "http://www.azlyrics.com/";

Sleep::getInstance()->tor_new_identity();

$client = new Client();


$client->setHeader('User-Agent', "Googlebot");


$guzzle = $client->getClient();

$guzzle->setDefaultOption('proxy', 'socks5://127.0.0.1:9050');
$client->setClient($guzzle);


$crawler = $client->request('GET', $poor_bastard);

$lyricsTxt = fopen("lyrics.txt", "w");

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

      $data = json_encode(array(
        "artist" => $artistDetails,
        "title" => $songTitle,
        "lyrics" => $text
      ));

      fwrite($lyricsTxt, $data);
      Sleep::getInstance()->sleep();

    });


  });


});


