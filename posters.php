<!DOCTYPE html>
<html lang="en">
<head>

	<meta charset="utf-8">
	<!-- Always force latest IE rendering engine & Chrome Frame -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>Movie Grid</title>

	<!-- Responsive and mobile friendly stuff -->
	<meta name="HandheldFriendly" content="True">
	<meta name="MobileOptimized" content="320">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Stylesheets -->
	<link rel="stylesheet" href="../css/col.css" media="all">
	<style type="text/css">
	body { padding:0em; font : 100% 'Helvetica Neue', arial, helvetica, helve, sans-serif;}
	.header { padding:1em 0; }
	.col { background: #fff; padding:0em 0; text-align:center;}
	h3 {font-size: 100%; padding-bottom: 10px;}
	p {font-size: 75%; padding-bottom: 10px;}
	</style>

</head>

<body>

<div class='section group'>

<?php

// setup some preferences
$apikey = file_get_contents("api.txt");  // you must request an API key from http://omdbapi.com
$columns ="10";   // how many columns in the grid (1 to 12)
$width = "100%";  // width of displayed images
$path = "../posters/";  // path to folder of posters
$sort = "asc";  // sort order of movie list - random (rand), ascending (asc), descending (desc)

// setup some basic variables - don't change!
$columncount=1;
$movieCount=1;
$fileRaw = file_get_contents("movies.txt");  // read in list of movies from external file
$file = str_replace(" ", "+", $fileRaw);
$movielist = explode(PHP_EOL, $file);

// sort movielist array
switch ($sort) {   
    case "rand":
		shuffle($movielist);
        break;
    case "asc":
        sort($movielist);
        break;
    case "desc":
        rsort($movielist);
        break;
    default:
        ;
}

foreach ($movielist as $movie) {
// loop through list of movies from file and get some basic info
$movieRaw = str_replace("+", " ", $movie);
$movieDetails = explode(";",$movieRaw);
$movieTitleSearch = urlencode($movieDetails[0]);
$movieTitleFile = str_replace(" ", "_", $movieDetails[0]);
$movieTitlePretty = $movieDetails[0];
$movieYear = $movieDetails[1];
$posterFile = "$movieTitleFile.jpg";
$dataFile = "$movieTitleFile.txt";


if (file_exists($path.$posterFile)) {
	// checking to see if the poster artwork and accompanying text file has been previously downloaded.
  	$url = $path.$dataFile;
	$resultText = file_get_contents($url);
	$resultText = file_get_contents($url);
	$resultArray = (json_decode($resultText));
	$Title = $resultArray->{'Title'};
	$Director = $resultArray->{'Director'};
	$Year = $resultArray->{'Year'};
	$Poster = $path.$posterFile;
    
}else {
	// if no poster artwork is found in the directory, make an API call to try to find and download it.
   	$url = "http://omdbapi.com/?t=".$movieTitleSearch."&y=".$movieYear."&apikey=".$apikey;    	
	$resultText = file_get_contents($url);
	$resultArray = (json_decode($resultText));
	$Title = $resultArray->{'Title'};
	$Director = $resultArray->{'Director'};
	$Poster = $resultArray->{'Poster'};
	$Year = $resultArray->{'Year'};
		
	if (empty($Poster)){
		// if the API call does not return a poster image change $movieTitlePretty to 'NO POSTER ARTWORK FOUND' it will get displayed later on line 113
		$movieTitlePretty = "<h3><font color='red'>NO POSTER ARTWORK FOUND</font></br>".PHP_EOL.$movieTitlePretty."</h3>".PHP_EOL;		
		
		}else {
			// if the API call finds poster artwork, download it to a file along with a text file to store some basic info
			file_put_contents($path.$movieTitleFile.".jpg", file_get_contents($Poster));
			file_put_contents($path.$movieTitleFile.".txt", $resultText);
			$movieTitlePretty = "<font color='green'>FILE DOWNLOADED</font></br>".PHP_EOL.$movieTitlePretty;		
			$Poster = $path.$posterFile;
			}			
	}  
	
if ($columncount<$columns){
	// if the current column is less than the number of columns specified, open a new column DIV and display the info
	echo "<div class='col span_1_of_$columns'>".PHP_EOL;
	echo "<p>".$movieCount++."</p>".PHP_EOL;
	echo '<img src="'.$Poster.'" alt="'.$Title.'" width="'.$width.'"></br>'.PHP_EOL;
	echo "<p><strong>".$movieTitlePretty."</strong> - ".$Year."</p>".PHP_EOL;
	echo "</div>".PHP_EOL;
	$columncount++;
	
	}else{
		// if the current column is equal to the maximum number of columns specified - display info, then close the group and start a new group
		echo "<div class='col span_1_of_$columns'>".PHP_EOL;
		echo "<p>".$movieCount++."</p>".PHP_EOL;
		echo '<img src="'.$Poster.'" alt="'.$Title.'" width="'.$width.'"></br>'.PHP_EOL;
		echo "<p><strong>".$movieTitlePretty."</strong> - ".$Year."</p>".PHP_EOL;
		echo "</div>".PHP_EOL;	
		echo "</div>".PHP_EOL;
		echo "<div class='section group'>".PHP_EOL;
		$columncount=1;
		}
}

//github test

?>
</div>
</div>
</body>
</html>


