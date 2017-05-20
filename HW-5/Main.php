<?php


include ('simple_html_dom.php');

header('Content-Type: text/html; charset=utf-8');
$limit = 10;
$query = isset($_REQUEST['q']) ? $_REQUEST['q'] : false;
$results = false;
if ($query)
{
  // The Apache Solr Client library should be on the include path
  // which is usually most easily accomplished by placing in the
  // same directory as this script ( . or current directory is a default
  // php include path entry in the php.ini)
  require_once('solr-php-client/Apache/Solr/Service.php');
  // create a new solr service instance - host, port, and webapp
  // path (all defaults in this example)
  $solr = new Apache_Solr_Service('localhost', 8983, '/solr/myexample/');
  // if magic quotes is enabled then stripslashes will be needed
  if (get_magic_quotes_gpc() == 1)
  {
    $query = stripslashes($query);
  }
  #$param = [];
  #$selected_radio = $_POST['pagerank'];
if (array_key_exists("pagerank", $_REQUEST)) {
        #$param['sort'] ="pageRankValues desc";
        $additionalParameters = array(
        'sort' => 'pageRankValues desc',
'facet' => 'true',
        
        'facet.field' => array(
              'date',
              'author'
        )   
);
    }
    else{
      $additionalParameters = array(
        'facet' => 'true',
        'facet.field' => array(
              'date',
              'author'
        )       
);
    }
    
    
  // in production code you'll always want to use a try /catch for any
  // possible exceptions emitted  by searching (i.e. connection
  // problems or a query parsing error)
  try
  {
    $results = $solr->search($query, 0, $limit,$additionalParameters);
  }
  catch (Exception $e)
  {
    // in production you'd probably log or email this error to an admin
    // and then show a special message to the user but for this example
    // we're going to show the full exception
    die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
  }
}
?>
<html>
  <head>
    <title>PHP Solr Client Example</title>
    <script>
var x = document.getElementById("mySelect");
   // document.getElementById("livesearch").innerHTML = x;
     //document.getElementById("q").value = x;
    x.onchange = function() {
    document.getElementById("q").value = x.value;
}
function showResult(str) {
  if (str.length==0) { 
    document.getElementById("livesearch").innerHTML="";
    document.getElementById("livesearch").style.border="0px";
    return;
  }
 
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else {  // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
    var suggestions=xmlhttp.responseText;
    var res = suggestions.split(",");
    var len=res.length;
    var text = "";
//document.getElementById("livesearch").innerHTML=xmlhttp.responseText;
  //    document.getElementById("livesearch").style.border="1px solid #A5ACB2";
    document.getElementById("demo").innerHTML = xmlhttp.responseText;
   
    }
  }
  xmlhttp.open("GET","a.php?q="+str,true);
  xmlhttp.send();   
}
</script>
<script>
function myFunction() {
    var x = document.getElementById("mySelect").value;
   // document.getElementById("livesearch").innerHTML = x;
     document.getElementById("q").value = x;
}
</script>
  </head>
  <body>
    
<!--<form><input type="text" size="30" onkeyup="showResult(this.value)">
<div id="livesearch"></div>
<p id="demo"></p>
<button type="button" onclick="myFunction()">Try it</button>
</form>-->

    <form  accept-charset="utf-8" method="get" autocomplete="off">
      <label for="q">Search:</label>
      <input id="q" name="q" type="text" onkeyup="showResult(this.value)" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8'); ?>"/>
      </br>
      <input type="radio" name="pagerank" value="pg">Page Rank
     <div id="livesearch"></div>
<p id="demo"></p>

      
      <input type="submit"/>
    </form>
<?php
// display results
if ($results)
{
  $total = (int) $results->response->numFound;
  $start = min(1, $total);
  $end = min($limit, $total);
?>
<?php
if ($total == 0) {
    include 'SpellCorrector.php';
     echo "Did you mean? "; 
     
//     echo $query;
     $arr =  explode(" ", $query);
//print all the value which are in the array
     $new_query="";
foreach($arr as $v){
    
    $new_query=$new_query.SpellCorrector::correct($v)." ";
  // echo $new_query;
}
     $new_query=SpellCorrector::correct($query);
   // echo SpellCorrector::correct($query);
} 
?>
<a href="main.php?q=<?php echo $new_query; ?> ">
<?php echo $new_query; ?></a>

    <div>Results <?php echo $start; ?> - <?php echo $end;?> of <?php echo $total; ?>:</div>
    <ol>
<?php
  // iterate result documents
  foreach ($results->response->docs as $doc)
  {
?>
<?php 
                $id = $doc->id;
                $url = $doc->og_url;
                $url = urldecode($url);
            ?>


      <li>
       
         <tr>
            <td>Title:</td>
            <td><?php echo htmlspecialchars($doc->title? $doc->title : "No Title", ENT_NOQUOTES, 'utf-8'); ?></td>
           
          </tr>
          
          <br/>
<tr>
<td><a href="<?php echo $url; ?>">Document:</a>
            </td>
            
</tr>
      
                Author: <?php echo $doc->author ? $doc->author : "None"; ?> 
                | Size (in KB): <?php echo $doc->stream_size ? $doc->stream_size : "None"; ?> 
                | Date: <?php echo $doc->creation_date ?$doc->creation_date : "None"; ?>
               
            
      </li>
<?php
  }
?>
    </ol>
<?php
}
?>

  </body>
</html>