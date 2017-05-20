<?php
header('Content-Type: text/html; charset=utf-8');
$limit = 10;
$query = isset($_REQUEST['q']) ? $_REQUEST['q'] : false;
$rank = array('sort'=>'pageRankFile desc');
$results = false;

$arr = array();

$f = fopen("mapLATimesDataFile.csv","r");
if($f!==false){
while($line = fgetcsv($f,0,","))
{
	
	$key = $line['0'];
	$value = $line['1'];
	$arr[$key] = $value;
}
fclose($f);
}

if ($query)
{
 require_once('solr-php-client/Apache/Solr/Service.php');
 $solr = new Apache_Solr_Service('localhost', 8983,'solr/myexample');
 if (get_magic_quotes_gpc() == 1)
 {
	 $query = stripslashes($query);
 }
 try { 
		if($_REQUEST['sort']=='solr')
			$results = $solr->search($query, 0, $limit); 
		else
			$results = $solr->search($query, 0, $limit,$rank);
  } 
  catch (Exception $e) { 
		die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
  } 
}
?>

<html> <head> <title>PHP Solr Client Example</title> </head>
<body> <form accept-charset="utf-8" method="get"> <br><label for="q">Search:</label> 
<input id="q" name="q" type="text" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8'); ?>"/> 
<br>
<br> 
<table>
<tr>
<td><input type="radio" name="sort" value="solr" <?php if(!isset($_REQUEST[ 'sort']) || $_REQUEST[ 'sort']=='solr' ) echo "checked"; ?>></td>
<td>Solr Default(LUCENE)</td>
</tr>
<br> 
<tr>
<td><input type="radio" name="sort" value="pageRank" <?php if($_REQUEST[ 'sort']=='pageRank' ) echo "checked"; ?>></td>
<td>External PageRank</td>
</tr>
</table>
            <br>
            <br>
<input type="submit"/>
</form> 
<?php 
if ($results) { 
	$total = (int) $results->response->numFound; 
	$start = min(1, $total); 
	$end = min($limit, $total); 
?> 
<div>
	Total Results: <?php echo $total;?><br>
	Results <?php echo $start; ?> - <?php echo $end;?> of <?php echo $total; ?>:
</div> 
<ol> 
	<?php 
		foreach ($results->response->docs as $doc) {

	?> 
	<li> 
		 <table>
		<?php
		 
		$docId = "N/A";
 		$docLink="N/A";
		$docDesc = "N/A";
		$docTitle = "N/A";
		foreach ($doc as $field => $value) { 
			if($field== "id" ){
				$docId=$value;
			}
			if($field == "title"){
				$docTitle=$value;
			}
			if($field == "description"){
				$docDesc=$value;
			}

		
		
		  } 
			echo "<tr>";
			$docLink = $arr[trim(substr($docId,57))];
                        
			echo "Title:".'<a href='.$docLink.'>'.$docTitle.'</a>'."<br>";
			echo "Link: ".'<a href='.$docLink.' target="_blank">'.$docLink.'</a>';
			echo "<br>";
			echo "ID: ".$docId."<br>";			
			echo "Description: ".$docDesc."<br>";
			echo "</tr>";
		 ?>
			

		</table> 
	</li> 
	<?php } ?> 
</ol> 
<?php } ?> 
</body> 
</html>