<?php
require_once __DIR__ . '/php-graph-sdk-5.0.0/src/Facebook/autoload.php';
 ?>
 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>Facebook Search</title>
     <script type="text/javascript">
       function makechanges(that) {
         if (that.value == "Places") {
           document.getElementById('placestag').style.display = "block";
         }
         else {
           document.getElementById("placestag").style.display = "none";
         }
       }
     </script>
   </head>
   <style media="screen">
     .rectangle{
       background-color: #d3d3d3;
       width: 700px;
       position: absolute;
       margin-left: 450px;
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
   </style>
   <body>
     <div class="rectangle">
       <h2>FACEBOOK SEARCH</h2>
       <hr>
       <form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post">
         <div style="float:left; margin-bottom:10px;">
           <label for="keyword">KEYWORD:</label>
           <input type="text" name="keyword">
         </div>
          <div style="clear:both; margin-top:10px;">
              <label style="margin-left:82px;"for="type">TYPE:</label>
              <select name="listelements" value="Users" style="margin-left:10px;" onchange="makechanges(this)">
                <option value="Users">Users</option>
                <option value="Pages">Pages</option>
                <option value="Events">Events</option>
                <option value="Groups">Groups</option>
                <option value="Places">Places</option>
              </select>
          </div>
          <div id="placestag" style="clear:both; margin-top:10px; display: none;">
            <label for="Location">LOCATION:</label>
            <input type="text" name="Location">
            <label for="Distance">DISTANCE(meters):</label>
            <input type="text" name="Distance">
          </div>
          <div style="clear:both; margin-left:130px;margin-top:10px;">
              <input type="submit" name="SUBMIT" value="SEARCH" >
              <input type="submit" name="CLEAR" value="CLEAR">
          </div>
        </div>
       </form>
       <?php
        define('APP-ID', '100135583850220');
        define('APP-SECRET', '4a1bcd7ad13ddad30dd585da27619fe2');
        define('FB-ACCESS-TOKEN', 'EAABbEqHnnuwBALrluaZCrOhgJZCjV5DfcJLrmrbaczS4BBEzAUUOaHjxHvoZAZAPOcF2tazFqADaEMZAmRt5tYxmRiqSawCxC8eEZCSBRw7i94nYk5GgqSURjLPuMmZCaUFOYFl5d5TET1Vg8bGKK5w');
        define('GOOGLE-API-KEY', 'AIzaSyBPT0iIkd-INAmFWJLY8N1P8YhZugi2IMk');
        if(isset($_POST['SUBMIT'])){
          $fb = new Facebook\Facebook([
                  'app_id' => 'APP-ID',
                  'app_secret' => 'APP-SECRET',
                  'default_graph_version' => 'v2.5',
                ]);
          $fb->setDefaultAccessToken('FB-ACCESS-TOKEN');
          $keyword=$_GET['keyword'];
          $type=$_GET['type'];
          $url="/search";
          if($type[0]!="Places"){
            CreateURL($fb,$url,$keyword,$type,$location=0,$distance=0);
          }
          else{
            $location=$_GET['$location'];
            $distance=$_GET['$distance'];
            $google_url = 'http://maps.googleapis.com/maps/api/geocode/json?address='.$location.'&key='.GOOGLE-API-KEY;
            $curl = curl_init($google_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $curl_response = curl_exec($curl);
            if ($curl_response === false) {
                $info = curl_getinfo($curl);
                curl_close($curl);
                die('error occured during curl exec. Additioanl info: ' . var_export($info));
            }
            curl_close($curl);
            $jsondata = json_decode($curl_response);
            if (isset($jsondata->response->status) && $jsondata->response->status == 'ERROR') {
                die('error occured: ' . $jsondata->response->errormessage);
            }
            echo 'response ok!';
            var_export($jsondata->response);
            CreateURL($fb,$url,$keyword,$type,$location,$distance);
          }
        }
          function CreateURL($fb,$url,$keyword,$type,$location,$distance){
            $url.="q=$keyword"."&type=$type"."&fields=id,name,picture.width(700).height(700)&access_token=Your_Access_Token".FB-ACCESS-TOKEN;
            try {
              $response = $fb->get('$url');
              $userNode = $response->getGraphUser();
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
              // When Graph returns an error
              echo 'Graph returned an error: ' . $e->getMessage();
              exit;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
              // When validation fails or other local issues
              echo 'Facebook SDK returned an error: ' . $e->getMessage();
              exit;
            }
        }

       ?>
     </div>
   </body>
 </html>
