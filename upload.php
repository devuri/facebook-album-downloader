<?php
    require_once 'init.php';

    use FacebookAlbumDownloader\Helper\Uploader;

    $uploader = new Uploader;
    try{
        if(isset($_POST['id'])){
            echo $uploader->uploadAlbums($_POST['id'],$_SESSION['profile']['username']);
        }
    }catch(Exception $e){
        echo $e;
        exit;
    }