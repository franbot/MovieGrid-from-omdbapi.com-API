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
	<link rel="stylesheet" href="col.css" media="all">
	<style type="text/css">
	body { padding:2em; font : 100% 'Helvetica Neue', arial, helvetica, helve, sans-serif;}
	.header { padding:1em 0; }
	.col { background: #fff; padding:0em 0; text-align:center;}
	h3 {font-size: 100%; padding-bottom: 10px;}
	p {font-size: 75%; padding-bottom: 10px;}
	</style>

</head>

<body>

<?php
// setup some preferences
$apikey = file_get_contents("api.txt");  // you must request an API key from http://omdbapi.com
$columns ="10";   // how many columns in the grid (1 to 12)
$width = "100%";  // width of displayed images
$path = "posters/";  // path to folder of posters
$sort = "asc";  // default sort order - random (rand), ascending (asc), descending (desc)
$sorter = new FieldSorter('Title'); // default value to sort by - Title, Year, Director, Rating
$tempSort = $_GET["sort"];
$tempSorter = $_GET["sorter"];

if (isset($tempSort)){
$sort = $tempSort;     // sort order passed in URL - random (rand), ascending (asc), descending (desc)
echo "custom sorting <strong>".$sort;
}

if (isset($tempSorter)){
$tempSorter = ucwords($tempSorter);
$sorter = new FieldSorter($tempSorter); // sort value passed in URL - Title, Year, Director, Rating
echo "</strong> by <strong>".$tempSorter."</strong>";
}

// setup some basic variables - don't change!
$arrContextOptions=array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false,),);
$columncount=1;
$movieCount=1;
$fileRaw = file_get_contents("movies.txt");  // read in list of movies from external file
$file = str_replace(" ", "+", $fileRaw);
$movielist = explode(PHP_EOL, $file);
$movieListDetails = array();

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
	$resultArray = (json_decode($resultText));
	$Title = $resultArray->{'Title'};
	$Director = $resultArray->{'Director'};
	$Year = $resultArray->{'Year'};
	$Poster = $path.$posterFile;
	$Rating = $resultArray->{'imdbRating'};
    
}else {
	// if no poster artwork is found in the directory, make an API call to try to find and download it.
   	$url = "http://omdbapi.com/?t=".$movieTitleSearch."&y=".$movieYear."&apikey=".$apikey;    	
	$resultText = file_get_contents($url);
	$resultArray = (json_decode($resultText));
	$Title = $resultArray->{'Title'};
	$Director = $resultArray->{'Director'};
	$Year = $resultArray->{'Year'};
	$Poster = $resultArray->{'Poster'};
	$Rating = $resultArray->{'imdbRating'};
		
	if (empty($Poster)){
		// if the API call does not return a poster image change $movieTitlePretty to 'NO POSTER ARTWORK FOUND' it will get displayed later in the page rendering loop
		$movieTitlePretty = "<h3><font color='red'>NO POSTER ARTWORK FOUND</font></br>".PHP_EOL.$movieTitlePretty."</h3>".PHP_EOL;		
		
		}else {
			// if the API call finds poster artwork, download it to a file along with a text file to store some basic info
			file_put_contents($path.$movieTitleFile.".jpg", file_get_contents($Poster, false, stream_context_create($arrContextOptions)));
			file_put_contents($path.$movieTitleFile.".txt", $resultText);
			$movieTitlePretty = "<font color='green'>FILE DOWNLOADED</font></br>".PHP_EOL.$movieTitlePretty;		
			$Poster = $path.$posterFile;
			}			
	}  
	
	$movieArray = array("Title" => $Title, "movieTitlePretty" => $movieTitlePretty,"Year" => $Year, "Director" => $Director, "Poster" => $Poster, "Rating" => $Rating);
	array_push($movieListDetails, $movieArray);
}
   
usort($movieListDetails, array($sorter, "cmp"));  // sort multidimensional array by sending to function below

class FieldSorter {
// sort multidimensional array by the specified key (Title, Year, Director, Rating) - variable $sorter set above.
    public $field;

    function __construct($field) {
        $this->field = $field;
    }

    function cmp($a, $b) {
        if ($a[$this->field] == $b[$this->field]) return 0;
        return ($a[$this->field] > $b[$this->field]) ? 1 : -1;
    }
}

// sort movielist array
switch ($sort) {   
    case "rand":
		shuffle($movieListDetails);
        break;
    case "asc":
        // no need to sort ascending because the array is already in that order
        break;
    case "desc":
    	// reverse the order of elements in the array to arrange by descending order
        $movieListDetails = array_reverse($movieListDetails,true);
        break;
    default:
        ;
}

// bellow here is where the actual page is rendered
echo "<div class='section group'>";

// loop through each sub array, get all the details, then reder each cell in the table
foreach ($movieListDetails as $movie){
	$Title = $movie['Title'];
	$movieTitlePretty = $movie['movieTitlePretty'];
	$Director = $movie['Director'];
	$Year = $movie['Year'];
	$Poster = $movie['Poster'];
	$Rating = $movie['Rating'];

if ($columncount<$columns){
	// if the current column is less than the number of columns specified, open a new column DIV and display the info
	echo "<div class='col span_1_of_$columns'>".PHP_EOL;
	//echo "<p>".$movieCount++."</p>".PHP_EOL;
	echo '<img src="'.$Poster.'" alt="'.$Title.'" width="'.$width.'"></br>'.PHP_EOL;
	echo "<p><strong>".$movieTitlePretty."</strong> - </br> - ".$Year." - </br>".$Director."</br>IMDB Rating = ".$Rating."</p>".PHP_EOL;
	echo "</div>".PHP_EOL;
	$columncount++;
	
	}else{
		// if the current column is equal to the maximum number of columns specified - display info, then close the group and start a new group
		echo "<div class='col span_1_of_$columns'>".PHP_EOL;
		//echo "<p>".$movieCount++."</p>".PHP_EOL;
		echo '<img src="'.$Poster.'" alt="'.$Title.'" width="'.$width.'"></br>'.PHP_EOL;
		echo "<p><strong>".$movieTitlePretty."</strong> - </br> - ".$Year." - </br>".$Director."</br>IMDB Rating = ".$Rating."</p>".PHP_EOL;
		echo "</div>".PHP_EOL;	
		echo "</div>".PHP_EOL;
		echo "<div class='section group'>".PHP_EOL;
		$columncount=1;
		}
}   

// display preformatted array of movie details for debugging   
// echo "<div class='section group'>".PHP_EOL."<pre>".PHP_EOL;
// print_r($movieListDetails);
// echo "</div>".PHP_EOL."<pre>".PHP_EOL;

?>
</div>
</div>
</body>
</html>


