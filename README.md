New students, go to this link first: https://gitlab.matrix.msu.edu/matrix/new-programmer-training/training/wikis/

Usage:

- Clone the repo: 
  - New Websites: ```git clone git@direct.gitlab.matrix.msu.edu:matrix/website-template.git```
  - Training: ```git clone git@direct.gitlab.matrix.msu.edu:matrix/new-programmer-training/training.git```
- copy /.dist.htaccess to /.htaccess and fix
- copy /config.dist.php to /config.php and fix
- check that the url works

New Pages:

- create new urls by setting up new routes in routes.php
- create a new php view file in /views/
- possibly make new sass partial and js files to go with the view too

SASS/CSS:

- ```css is compiled``` only edit files in /assets/scss/partials/* for all css
- changes will only take effect after running the ```compass compile``` command in putty
  
Javascript:   
- don't be affraid to make new files for new functions. They go in /assets/javascripts/
- your new js files will still need to be included:
    - use index.php to make your js run on every page or use your current view file for just that page.

General:

- if css of js changes still aren't showing, try the clear cache chrome extension.
- use the global urls whenever possible!
    - ex-GOOD: ```<img src="<?php echo BASE_IMAGE_URL;?>eye.svg"/>```
    - ex-BAD: ```<img src="../../../eye.svg"/>```


File access order goes like this:
- .htaccess- routes all traffic (exempt for assets and modules) to index.php
- index.php- sets up basic html structure, includes config
- config.php- sets up all global variables in php and js, includes routes, kora wrapper, sql wrapper
- route.php- decides either, which view to include or which php api route to connect
- index.php- includes css, theme, jquery, scripts, header, current view, footer


What your supervisor should explain to you:
- what a htaccess does
- why we use config files
- how routes files works
- where to get the code for the slider (carrousel.php) and to include it in your task
- what is js and jquery
- how to modify js file to create a second slide
- where to get an example of an ajax call (admin.js) and include it in your code
- explain how to do the kora section with the kora wrapper