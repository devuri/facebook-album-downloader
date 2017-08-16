<?php
include 'init.php';

use FacebookAlbumDownloader\Library\View;
use FacebookAlbumDownloader\Library\FacebookClient;


$fb = new FacebookClient();
$albums = [];
if($fb->authenticate()){
    if(!isset($_SESSION['profile'])){
        $_SESSION['profile'] = $fb->getUserDetails();
        $_SESSION['profile']['username'] = $fb->getUsername($_SESSION['profile']['id']);
    }
    $albums = $fb->getAlbums();
}else{
    header('Location: '.$fb->getAuthUrl());
    exit;
}

//Get Template & Assign Vars
$view = new View('FacebookAlbumDownloader/View/index.php');

$albumVar = [];
$albumIds = '';
foreach ($albums as $album) {
    $albumIds.=$album['id'].',';
    $album['cover_photo'] = $fb->getLowestResolutionImage($album['cover_photo']['id']);
    $albumVar[] = $album;
}

$view->allIds = rtrim($albumIds,','); 
$view->isGoogle = isset($_SESSION['GOOGLE_ACCESS_TOKEN'])?true:false;
$view->albums = $albumVar;

echo $view;