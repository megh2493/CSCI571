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
if(isset($_POST["SUBMIT"])){
  $keyword=$_POST["keyword"];
  $url="/search?";
  $lat=$_POST["latitude"];
  $lng=$_POST["longitude"];
  $location=$lat.",".$lng;
  $typearraylist = ["user", "page","event","place","group"];
  CreateURL($fb,$url,$keyword,$typearray,$location=0);
}

//to create url for fetching the response
function CreateURL($fb,$url,$keyword,$type,$location=0){
  foreach ($typearraylist as $search) {
    if($search == "place"){
      $urlNew = $url."q=".$keyword."&type=".$search."&center=" .$location. "&fields=id,name,picture.width(700).height(700)";
    }
    else{
      $urlNew = $url."q=".$keyword."&type=".$search."&fields=id,name,picture.width(700).height(700)";
    }
    // try {
      $response = $fb->get($urlNew);
      $userNode = $response->getGraphEdge();
    // } catch(Facebook\Exceptions\FacebookResponseException $e) {
    //   echo 'Graph returned an error: ' . $e->getMessage();
    // } catch(Facebook\Exceptions\FacebookSDKException $e) {
    //   exit;
    //   echo 'Facebook SDK returned an error: ' . $e->getMessage();
    //   exit;
    // }
    $returnData($search)=$usernode;
  }
echo $returnData;
}

// //Operations to perform when details link is clicked from table
// if(isset($_GET['action'])){
//   $newurl = "/". $_GET['action']."?fields=id,name,picture.width(700).height(700),albums.limit(5){name,photos.limit(2){name, picture}},posts.limit(5)";
//   try {
//     $response = $fb->get($newurl);
//     $userNode = $response->getGraphNode();
//   } catch(Facebook\Exceptions\FacebookResponseException $e) {
//     echo 'Graph returned an error: ' . $e->getMessage();
//     exit;
//   }
//   $location = $distance = "";
//
//   //call to create the albums and posts page
//   if(isset($_GET['location'])){
//     $location = $_GET['location'];
//   }
//   if(isset($_GET['distance'])){
//     $distance = $_GET['distance'];
//   }
//   createPage($userNode,$_GET['keyword'],$_GET['type'],$location,$distance,$_GET['action']);
//   //to reset values of form fields
//   textDataReset();
// }
//
// //operations to perform to get hd image
// if(isset($_GET['gethdimg'])){
//   $imgurl ="/".$_GET['gethdimg']."/picture?redirect=false";
//   $newurl = "/". $_GET['actionid']."?fields=id,name,picture.width(700).height(700),albums.limit(5){name,photos.limit(2){name, picture}},posts.limit(5)";
//   try {
//     $response = $fb->get($imgurl);
//     $userNode = $response->getGraphNode();
//     $json = json_decode($userNode);
//     $link = "<script>window.open('".$json->url."');</script>";
//     echo $link;
//     $userData = $fb->get($newurl);
//     $response = $userData->getGraphNode();
//
//     $location = $distance = "";
//
//     //call to create the albums and posts page
//     if(isset($_GET['location'])){
//       $location = $_GET['location'];
//     }
//     if(isset($_GET['distance'])){
//       $distance = $_GET['distance'];
//     }
//     createPage($response,$_GET['keyword'],$_GET['type'],$location,$distance,$_GET['actionid']);
//
//   } catch(Facebook\Exceptions\FacebookResponseException $e) {
//     echo 'Graph returned an error: ' . $e->getMessage();
//     exit;
//   }
//   textDataReset();
// }
// //creates table based on type
// function createTable($data,$keyword,$type,$location=0,$distance=0){
//   if(sizeof($data) != 0){
//     echo "<table style='border: 1px solid black; margin-top:10px;' align='center'>
//             <tr>
//               <th style='border: 1px solid black;'>Profile Photo</th>
//               <th style='border: 1px solid black;'>Name</th>";
//     if($type == "event"){
//       echo "<th style='border: 1px solid black;'>Places</th>";
//     }
//     else{
//       echo "<th style='border: 1px solid black;'>Details</th>";
//     }
//     echo "</tr>";
//     foreach ($data as $value) {
//       $postdata = "<td style='border: 1px solid black;'><a href=\"?action=". $value["id"] . "&keyword=" . $keyword . "&type=" . $type;
//       echo "<tr>";
//       echo "<td style='border: 1px solid black;'><a href=". $value["picture"]["url"] ." ><img src=". $value["picture"]["url"] . " alt='profilepicture' style='height:30px; width:40px;'></a></td>";
//       echo "<td style='border: 1px solid black;'>". $value["name"]. "</td>";
//       if($type == "event"){
//         echo "<td style='border: 1px solid black;'>". $value["place"]["name"] . "</td>";
//       }
//       else{
//         if(isset($location) && $location!=""){
//            $postdata .= "&location=". $location;
//         }
//
//         if(isset($distance) && $distance !=0){
//           $postdata .= "&distance=". $distance;
//         }
//         $postdata .= "\">Details</a></td>";
//         echo $postdata;
//       }
//       echo "</tr>";
//     }
//     echo"</table>";
//   }
//   else {
//     echo "<div class='general-style' style='background-color: #d3d3d3; border: 1px solid black; margin-top:10px; '><h4 style='text-align:center; '>No Records Have Been Found</h4></div>";
//   }
// }
//
// //function to create the albums and posts page
// function createPage($data,$keyword,$type,$location=0,$distance=0,$actionid){
//   //operations to perform for albums tab
//   if (isset($data["albums"])){
//     echo "<button class='accordion' onclick='toggleSection()' style='text-align:center; height:50px; background-color: #d3d3d3; margin-top:10px;' id='albumstab'>Albums</button>";
//     echo "<div class='panel'>";
//     foreach ($data["albums"] as $value) {
//         echo "<button class='accordion' onclick='toggleSection()'> ". $value["name"] ."</button>";
//         echo "<div class='panel' style='margin-left: 410px;border: 1px solid black; width:700px;'>";
//         if(isset($value["photos"])){
//           foreach ($value["photos"] as $images) {
//             $imgdata = "<a href=\"?gethdimg=". $images["id"] . "&keyword=" . $keyword . "&type=" . $type. "actionid=".$actionid;
//
//             if(isset($location) && $location!=0){
//                $imgdata .= "&location=". $location;
//             }
//
//             if(isset($distance) && $distance !=0){
//               $imgdata .= "&distance=". $distance;
//             }
//             $imgdata .= "\" ><img src=". $images["picture"] . " alt='picture' style='height:80px; padding-right:10px; padding-top:10px; padding-left:10px width:80px;'></a>";
//             echo $imgdata;
//           }
//         }
//         echo "</div>";
//       }
//       echo "</div>";
//     }
//   else {
//     echo "<div class='general-style' style='background-color: #d3d3d3; border: 1px solid black; margin-top:10px;'><h4 style='text-align:center;'>No Albums Have Been Found</h4></div>";
//   }
//
//   //operations to perform for post tab
//   if(isset($data["posts"])){
//       echo "<button class='accordion' onclick='toggleSection()' style='text-align:center; height:50px; background-color: #d3d3d3; margin-top:10px;' id='poststab'>Posts</button>";
//       echo "<div class='panel' >";
//       echo "<br><div class='general-style' style='border:1px solid black;'><b>Messages</b></div>";
//       foreach($data["posts"] as $post) {
//         //isset is used inorder to check if message exists or not in JSON data
//         if(isset($post["message"])){
//          echo "<div class='general-style' style='border: 1px solid black; text-align: left;'>". $post["message"] . "<br></div>";
//         }
//       }
//       echo "</div>";
//   }
//   else {
//       echo "<div class='general-style' style='background-color: #d3d3d3; border: 1px solid black; margin-top:10px;'><h4 style='text-align:center;'>No Posts Have Been Found</h4></div>";
//   }
// }
//
// //reset the data value in form fields once a new request is sent
// function textDataReset(){
//  if(isset($_GET['type'])){
//    echo "<script>document.getElementById('type').value='".$_GET['type']."';</script>";
//  }
//  if(isset($_GET['keyword'])){
//    echo "<script>document.getElementById('keyword').value='".$_GET["keyword"]."';</script>";
//  }
//
//  if(isset($_GET['distance']) && $_GET['distance'] != ""){
//    echo "<script> document.getElementById('placestag').style.display='block'; document.getElementById('distance').value='".$_GET['distance']."';</script>";
//  }
//  if(isset($_GET['location']) && $_GET['location'] != ""){
//    echo "<script>document.getElementById('placestag').style.display='block';document.getElementById('location').value='".$_GET['location']."';</script>";
//  }
// }

?>
