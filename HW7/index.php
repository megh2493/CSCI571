<?php
require_once __DIR__ . '/php-graph-sdk-5.0.0/src/Facebook/autoload.php';

//defines the access token for facebook and google API's
define('APP_ID', '100135583850220');
define('APP_SECRET', '4a1bcd7ad13ddad30dd585da27619fe2');
define('FB_ACCESS_TOKEN', 'EAABbEqHnnuwBALrluaZCrOhgJZCjV5DfcJLrmrbaczS4BBEzAUUOaHjxHvoZAZAPOcF2tazFqADaEMZAmRt5tYxmRiqSawCxC8eEZCSBRw7i94nYk5GgqSURjLPuMmZCaUFOYFl5d5TET1Vg8bGKK5w');

 //set timezone to access the pages page correctly
date_default_timezone_set("America/Los_Angeles");

//to fetch response from facebook
$fb = new Facebook\Facebook([
        'app_id' => APP_ID,
        'app_secret' => APP_SECRET,
        'default_graph_version' => 'v2.5',
      ]);
$fb->setDefaultAccessToken(FB_ACCESS_TOKEN);

//function to perform on submit button clicked
if(isset($_GET["keyword"]))
{
  $keyword=$_GET["keyword"];
  if(isset($_GET["location"])){
    $location = $_GET["location"];
   }
  $url="/search?";
  $typearraylist = ["user", "page","event","place","group"];
  CreateURL($fb,$url,$keyword,$typearraylist,$location=0);
}
//to create url for fetching the response
function CreateURL($fb,$url,$keyword,$typearraylist,$location=0){
  $returnData = array();
  foreach ($typearraylist as $search) {
    if($location!=0){
      $urlNew = $url."q=".$keyword."&type=".$search."&center=" .$location. "&fields=id,name,picture.width(700).height(700)";
    }
    else{
      $urlNew = $url."q=".$keyword."&type=".$search."&fields=id,name,picture.width(700).height(700)";
    }
    try {
      $response = $fb->get($urlNew);
      $userNode = $response->getGraphEdge();
      $pagerData = $response->getGraphEdge()->getMetaData();
      $jsonData = array(
        "data" => json_decode($userNode),
        "pager" => $pagerData
      );
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
      echo 'Graph returned an error: ' . $e->getMessage();
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
      exit;
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }
    $returnData[$search]=$jsonData;
  }
echo json_encode($returnData);
}

//Operations to perform when details link is clicked from table
if(isset($_GET['action'])){
  $newurl = "/". $_GET['action']."?fields=id,name,picture.width(700).height(700),albums.limit(5){name,photos.limit(2){name, picture}},posts.limit(5)";
  try {
    $response = $fb->get($newurl);
    $userNode = $response->getGraphNode();
    $jsonDetailData = json_decode($userNode);
  } catch(Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
  }
  echo json_encode($jsonDetailData);
}

//operations to perform to get hd image
if(isset($_GET['id'])){
  $imgurl ="/".$_GET['id']."/picture?redirect=false";
  try {
    $response = $fb->get($imgurl);
    $userNode = $response->getGraphNode();
    $jsonimagedata = json_decode($userNode);

  } catch(Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
  }
  echo json_encode($jsonimagedata);
}
?>
