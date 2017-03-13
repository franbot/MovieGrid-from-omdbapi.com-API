# MovieGrid-from-omdbapi.com-API
Create a grid of 100 favorite movie posters using omdbapi.com API.

This project will hopefully turn into a Wordpress plug-in at some point, but for now it's just a proof of concept. I have a list of my 100 or so favorite movies, and I want to display them in a grid. Rather than hunting Google for every single individual movie poster, I am using the API from omdbapi.com to search for and download movie posters. Since posters are downloaded, only new movies added to the list will result in an API call. 

*** I have NOT included my API key here publicly, you can request your own by making a small donation at http://www.omdbapi.com. Once you have it - create a plain text file named "api.txt" containing your api key - save it in the same directory as the "posters.php" document. The code will not work until you have an API key***

For styling I am using the responsive grid CSS code found at http://www.responsivegridsystem.com.  I have merged each of the stylesheets into one big stylesheet.

BTW - I am a total noob with PHP.  More than anything this is a project to cut my teeth on a real world project.  I'm what you might call a Google programmer - I google search snippets of code and put it in my program. 

live working copy at http://franciscrossman.com/posters/posters.php

Installation:
- Download everything
- Upload everything to an empty directory - the name doesn't matter.
- Request an API key by making a small donation at http://www.omdbapi.com. Once you have it - create a plain text file named "api.txt" containing your api key.
- Create a subdirectory named "posters" - this name must match

Usage:
visit the "posters.php" page on your server.  The script will parse through "movies.txt" and look in the "/posters" directory for a text document and jpg poster image for each movie name.  Anything not found in there will result in an API call to try to retrieve the info from OMBDB.  If the info is found, a jpeg file will be downloaded to the directory "/posters" and an accompanying text file will be created containing movie info.

Sorting:
You can change the sorting by passing variables in the url.
- Available sort options: asc, desc, rand
- Available sorters: title, year, director, rating

Sorting Examples:
Sort ascending by title:
- http://franciscrossman.com/posters/posters.php?sort=asc&sorter=title

Sort descending by IMDB rating:
- http://franciscrossman.com/posters/posters.php?sort=desc&sorter=rating

Sort random:
- http://franciscrossman.com/posters/posters.php?sort=rand