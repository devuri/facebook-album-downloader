<?php
include 'init.php';

use FacebookAlbumDownloader\Library\View;
use FacebookAlbumDownloader\Library\FacebookClient;

if(!isset($_SESSION['FACEBOOK_ACCESS_TOKEN'])){
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

$fb = new FacebookClient();
if(!$fb->authenticate()){
    header('Location: '.$fb->getAuthUrl());
    exit;
}

//Get Template & Assign Vars
$view = new View('FacebookAlbumDownloader/View/album.php');

$view->album = $fb->getAlbum($id);

echo $view;