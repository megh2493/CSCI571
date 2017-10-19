<?php
require_once __DIR__ . '/php-graph-sdk-5.0.0/src/Facebook/autoload.php';
 ?>
 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>Facebook Search</title>

     <script type="text/javascript">
        //to display location and distance on selection of places
       function makechanges(that) {
         if (that.value == "place") {
           document.getElementById('placestag').style.display = "block";
         }
         else {
           document.getElementById("placestag").style.display = "none";
         }
       }
       //to toggle between accordian sections
       function toggleSection(){
         var acc = document.getElementsByClassName("accordion");
         var i;
         for (i = 0; i < acc.length; i++) {
           acc[i].onclick = function(){
             if(this && (this.id == "poststab")){
               var division  = document.getElementById("albumstab");
               var album = division.nextElementSibling;
               if(album){
                 album.style.display = "none";
               }
             }else{
               var division  = document.getElementById("poststab");
               var posts = division.nextElementSibling;
               if(posts){
                 posts.style.display = "none";
               }
             }
           this.classList.toggle("active");
           var panel = this.nextElementSibling;
           if (panel.style.display === "block") {
             panel.style.display = "none";
           } else {
             panel.style.display = "block";
           }
         }
        }
      }
      //clears the page when clear button is clicked
      function clearPage(){
        window.location = "search.php";
      }
      //retains location and distance
      function show()
      {
         document.getElementById('placestag').style.display = "block";
      }
     </script>
   </head>
   <style media="screen">
     .rectangle{
       background-color: #d3d3d3;
       border: 1px solid black;
       width: 700px;
       position: relative;
     }
     h2{
       font-style: italic;
       text-align: center;
     }
     input,label{
       position: relative;
       margin-left: 40px;
     }
     input{
       margin-left: 10px;
     }
     .general-style{
       width: 700px;
       text-align:center;
       margin-left: 410px;
     }
     button.accordion {
        cursor: pointer;
        text-align: left;
        border: 1px solid black;
        outline: none;
        transition: 0.4s;
        width: 700px;
        margin-left: 410px;
        color: blue;
        text-decoration: underline;
     }
     button.accordion.active, button.accordion:hover {
        background-color: #ddd;
     }
     div.panel {
        padding: 0px;
        background-color: white;
        display: none;
     }
   </style>
   <body id="body">
     <div id="rectangleData" align="center">
       <div id="rect" align="center">
         <div  style=" background-color: #d3d3d3;border: 1px solid black;width: 700px;" class="rectangle" align="left">
           <h2>FACEBOOK SEARCH</h2>
           <hr>
           <form  id="form"  action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="POST">
             <div style="margin-bottom:10px;">
               <label for="keywordName">KEYWORD:</label>
               <input type="text" id="keyword" name="keyword" value="<?php echo (isset($_POST['keyword'])) ? $_POST['keyword'] : '' ?>" required></input>
             </div>
             <div style="margin-top:10px;">
              <label for="typeNameData">TYPE:</label>
              <select name="type" id="type" style="margin-left:52px;" value="<?php echo (isset($_POST['type'])) ? $_POST['type'] : '' ?>" onchange="makechanges(this)">
                <option <?php if (isset($_POST["type"]) && $_POST["type"] == "user") { ?>selected="true" <?php }; ?> value="user">Users</option>
                <option <?php if (isset($_POST["type"]) && $_POST["type"] == "page") { ?>selected="true" <?php }; ?> value="page">Pages</option>
                <option <?php if (isset($_POST["type"]) && $_POST["type"] == "event") { ?>selected="true" <?php }; ?> value="event">Events</option>
                <option <?php if (isset($_POST["type"]) && $_POST["type"] == "group") { ?>selected="true" <?php }; ?> value="group">Groups</option>
                <option <?php if (isset($_POST["type"]) && $_POST["type"] == "place") { ?>selected="true" <?php }; ?> value="place">Places</option>
              </select>
            </div>
            <div id="placestag" style="clear:both; margin-top:10px; display: none;">
              <label id="locationtext" for="Location">LOCATION:</label>
              <input type="text" id="location" value="<?php echo (isset($_POST['Location'])) ? $_POST['Location'] : '' ?>"" name="Location">
              <label id="distancetag" for="Distance">DISTANCE(meters):</label>
              <input type="text" id="distance" value="<?php echo (isset($_POST['Distance'])) ? $_POST['Distance'] : '' ?>"" name="Distance" >
            </div>
            <div style="margin-top:10px; margin-left:30px;">
              <input type="submit" name="SUBMIT" value="SEARCH" />
              <input type="reset" name="CLEAR" value="CLEAR" onclick='clearPage()'/>
            </div>
            <br>
            <br>
          </form>
        </div>
      </div>
    </div>
    <?php
    //defines the access token for facebook and google API's
    define('APP_ID', '100135583850220');
    define('APP_SECRET', '4a1bcd7ad13ddad30dd585da27619fe2');
    define('FB_ACCESS_TOKEN', 'EAABbEqHnnuwBALrluaZCrOhgJZCjV5DfcJLrmrbaczS4BBEzAUUOaHjxHvoZAZAPOcF2tazFqADaEMZAmRt5tYxmRiqSawCxC8eEZCSBRw7i94nYk5GgqSURjLPuMmZCaUFOYFl5d5TET1Vg8bGKK5w');
    define('GOOGLE_API_KEY', 'AIzaSyBPT0iIkd-INAmFWJLY8N1P8YhZugi2IMk');

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
      $type=$_POST["type"];
      $url="/search?";
      if($type!="place"){
        CreateURL($fb,$url,$keyword,$type,$location=0,$distance=0);
      }
      else{
        $location=$_POST["Location"];
        $distance=$_POST["Distance"];
        if($location == '')
        {
          echo "<script> alert('no valid address'); </script>";
          return;
        }
        $google_url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($location).'&key='.GOOGLE_API_KEY;
        $response = file_get_contents($google_url);
        $jsondata = json_decode($response);
        if(!isset($jsondata)){
          echo "<script>alert('Invalid Address');</script>";
          return;
        }
        if (isset($jsondata->response->status) && $jsondata->response->status == 'ERROR') {
            die('error occured: ' . $jsondata->response->errormessage);
        }
        $convertedlocation = $jsondata->results[0]->geometry->location->lat.",".$jsondata->results[0]->geometry->location->lng;
        if(isset($location))
            echo "<script> show();</script>";
        CreateURL($fb,$url,$keyword,$type,$convertedlocation,$distance);
      }
    }

    //to create url for fetching the response
    function CreateURL($fb,$url,$keyword,$type,$location=0,$distance=0){
      if($type!="event"){
        if($location == 0 && $distance == 0){
          $urlNew = $url."q=".$keyword."&type=".$type."&fields=id,name,picture.width(700).height(700)";
        }
        else{
          $urlNew = $url."q=".$keyword."&type=". $type. "&center=" .$location. "&distance=" .$distance. "&fields=id,name,picture.width(700).height(700)";
        }
      }
      else{
        $urlNew = $url."q=".$keyword ."&type=". $type ."&fields=id,name,picture.width(700).height(700),place";
      }
      try {
        $response = $fb->get($urlNew);
        $userNode = $response->getGraphEdge();
      } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
      } catch(Facebook\Exceptions\FacebookSDKException $e) {
        exit;
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
      }
      //creates table once facebook search is run
      createTable($userNode,$keyword,$type,$location,$distance);
    }
    //Operations to perform when details link is clicked from table
    if(isset($_GET['action'])){
      $newurl = "/". $_GET['action']."?fields=id,name,picture.width(700).height(700),albums.limit(5){name,photos.limit(2){name, picture}},posts.limit(5)";
      try {
        $response = $fb->get($newurl);
        $userNode = $response->getGraphNode();
      } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
      }
      $location = $distance = "";
      //call to create the albums and posts page
      if(isset($_GET['location'])){
        $location = $_GET['location'];
      }
      if(isset($_GET['distance'])){
        $distance = $_GET['distance'];
      }
      createPage($userNode,$_GET['keyword'],$_GET['type'],$location,$distance);
      //to reset values of form fields
      textDataReset();
    }

    //operations to perform to get hd image
    if(isset($_GET['gethdimg'])){
      $imgurl ="/".$_GET['gethdimg']."/picture?redirect=false";
      try {
        $response = $fb->get($imgurl);
        $userNode = $response->getGraphNode();
        $json = json_decode($userNode);
        $link = "<script>window.open('".$json->url."');</script>";
        echo $link;
      } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
      }
      textDataReset();
    }
    //creates table based on type
    function createTable($data,$keyword,$type,$location,$distance){
      if(sizeof($data) != 0){
        echo "<table style='border: 1px solid black; margin-top:10px;' align='center'>
                <tr>
                  <th style='border: 1px solid black;'>Profile Photo</th>
                  <th style='border: 1px solid black;'>Name</th>";
        if($type == "event"){
          echo "<th style='border: 1px solid black;'>Places</th>";
        }
        else{
          echo "<th style='border: 1px solid black;'>Details</th>";
        }
        echo "</tr>";
        foreach ($data as $value) {
          $postdata = "<td style='border: 1px solid black;'><a href=?action=". $value["id"] . "&keyword=" . $keyword . "&type=" . $type;
          echo "<tr>";
          echo "<td style='border: 1px solid black;'><a href=". $value["picture"]["url"] ." ><img src=". $value["picture"]["url"] . " alt='profilepicture' style='height:30px; width:40px;'></a></td>";
          echo "<td style='border: 1px solid black;'>". $value["name"]. "</td>";
          if($type == "event"){
            echo "<td style='border: 1px solid black;'>". $value["place"]["name"] . "</td>";
          }
          else{
            if($location!=0 && $distance!=0){
              echo $postdata . "&location=". $location ."&distance=". $distance .">Details</a></td>";
            }
            elseif ($location!=0 && $distance == 0) {
              echo $postdata . "&location=". $location .">Details</a></td>";
            }
            else {
               echo $postdata . ">Details</a></td>";
            }
          }
          echo "</tr>";
        }
        echo"</table>";
      }
      else {
        echo "<div class='general-style' style='background-color: #d3d3d3; border: 1px solid black;'><h4 style='text-align:center; '>No Records Have Been Found</h4></div>";
      }
    }

    //function to create the albums and posts page
    function createPage($data,$keyword,$type,$location=0,$distance=0){
      //operations to perform for albums tab
      if (isset($data["albums"])){
        echo "<button class='accordion' onclick='toggleSection()' style='text-align:center; height:50px; background-color: #d3d3d3; margin-top:10px;' id='albumstab'>Albums</button>";
        echo "<div class='panel'>";
        foreach ($data["albums"] as $value) {
            echo "<button class='accordion' onclick='toggleSection()'> ". $value["name"] ."</button>";
            echo "<div class='panel' style='margin-left: 410px;border: 1px solid black; width:700px;'>";
            if(isset($value["photos"])){
              foreach ($value["photos"] as $images) {
                $imgdata = "<a href='?gethdimg=". $images["id"] . "&keyword=" . $keyword . "&type=" . $type;
                if($location!=0 && $distance!=0){
                  echo $imgdata . "&location=". $location ."&distance=". $distance ."' ><img src=". $images["picture"] . " alt='picture' style='height:80px; padding-right:10px; padding-top:10px; padding-left:10px width:80px;'></a>";;
                }
                elseif ($location!=0 && $distance == 0) {
                  echo $imgdata . "&location=". $location ."' ><img src=". $images["picture"] . " alt='picture' style='height:80px; padding-right:10px; padding-top:10px; padding-left:10px;  width:80px;'></a>";
                }
                else {
                   echo $imgdata . "' ><img src=". $images["picture"] . " alt='picture' style='height:80px; padding-right:10px; padding-top:10px; padding-left:10px;  width:80px;'></a>";
                }
              }
            }
            echo "</div>";
          }
          echo "</div>";
        }
      else {
        echo "<div class='general-style' style='background-color: #d3d3d3; border: 1px solid black; margin-top:10px;'><h4 style='text-align:center;'>No Albums Have Been Found</h4></div>";
      }

      //operations to perform for post tab
      if(isset($data["posts"])){
          echo "<button class='accordion' onclick='toggleSection()' style='text-align:center; height:50px; background-color: #d3d3d3; margin-top:10px;' id='poststab'>Posts</button>";
          echo "<div class='panel' >";
          echo "<br><div class='general-style' style='border:1px solid black;'><b>Messages</b></div>";
          foreach($data["posts"] as $post) {
            //isset is used inorder to check if message exists or not in JSON data
            if(isset($post["message"])){
             echo "<div class='general-style' style='border: 1px solid black; text-align: left;'>". $post["message"] . "<br></div>";
            }
          }
          echo "</div>";
      }
      else {
          echo "<div class='general-style' style='background-color: #d3d3d3; border: 1px solid black; margin-top:10px;'><h4 style='text-align:center;'>No Posts Have Been Found</h4></div>";
      }
    }

    //reset the data value in form fields once a new request is sent
    function textDataReset(){
     if(isset($_GET['type'])){
       echo "<script>document.getElementById('type').value='".$_GET['type']."';</script>";
     }
     if(isset($_GET['keyword'])){
       echo "<script>document.getElementById('keyword').value='".$_GET["keyword"]."';</script>";
     }
     if(isset($_GET['distance']) && $_GET['distance'] != ""){
       echo "<script>document.getElementById('distancetext').hidden=false;document.getElementById('distance').hidden=false; document.getElementById('distance').value=".$_GET['distance']."';</script>";
     }
     if(isset($_GET['location']) && $_GET['location'] != ""){
       echo "<script>document.getElementById('location').hidden=false;document.getElementById('locationtext').hidden=false;document.getElementById('location').value='".$_GET['location']."';</script>";
     }
    }

    ?>
  </body>
</html>
