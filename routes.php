<?php

$GLOBALS['api_routes'] = array(
    'api/testing' => array('functions.php', 'testingFunction'),
    'api/printEpisodes' => array('functions.php', 'printEpisodes'),
    'api/admin' => array('functions.php', 'admin'),
    'api/game' => array('game.php', 'game'),
    'api/browse' => array('game.php', 'browse'),
    'api/joinGame' => array('game.php', 'joinGame'),
    'api/newGame' => array('game.php', 'newGame'),
);

$GLOBALS['routes'] = array(
    '' => 'home.php',
    'essays' => 'essays.php',
    'browse' => 'browse.php',
    'blog' => 'blog.php',
    'search' => 'search.php',
    'searchResults' => 'searchResults.php',
    'carousel' => 'carousel.php',
    'drawers' => 'drawers.php',
    'fullRecord' => 'fullrecord.php',
    'fullRecord-2' => 'fullRecord-2.php',
    'imageCardGrid' => 'imageCardGrid.php',
    'imageCardGrid-2' => 'imageCardGrid-2.php',
    'mediaRecords' => 'mediaRecords.php',
    'mediaRecords-2' => 'mediaRecords-2.php',
    'mediaRecord-one-image-ex' => 'mediaRecord-one-image-ex.php',
    'mediaRecord' => 'mediaRecord.php',
    'tabs-2' => 'tabs-2.php',
    'tabs-many' => 'tabs-many.php',
    'text-modal' => 'text-modal.php',
    'text-with-nav' => 'text-with-nav.php',
    'admin' => 'admin.php',
    'module-test' => 'module-test.php',
	'searchbar-results' => '../modules/searchbar/searchbar-results.php'
);

//$location = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$location = "http://$_SERVER[HTTP_HOST]";
$path = parse_url($_SERVER['REQUEST_URI'])['path'];
$actualLink = $location . $path;
$currentFile = str_replace(BASE_URL, '', $actualLink);
if( substr($currentFile, -1) == '/' ){
    $currentFile = rtrim($currentFile,"/");
}


if( isset($GLOBALS['api_routes'][$currentFile]) ){
    $currentApiFile = $GLOBALS['api_routes'][$currentFile];
    include_once(BASE_FUNCTIONS_PATH.$currentApiFile[0]);
    echo $currentApiFile[1]();
    die;
}elseif( !isset($GLOBALS['routes'][$currentFile]) ){
    header('HTTP/1.0 404 Not Found');
    define('CURRENT_VIEW', '404.php');
}else{
    define('CURRENT_VIEW', $GLOBALS['routes'][$currentFile]);
}
