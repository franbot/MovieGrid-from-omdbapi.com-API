<?php

$myFile = "movies.txt";	
$text = trim($_GET['data']);
$text = str_replace("
$fh = fopen($myFile, 'a+');
fwrite($fh, PHP_EOL);
fwrite($fh, $text);	
fclose($fh);									
header('Location: posters.php'); 			
exit;

?>