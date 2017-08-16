<?php
    require_once 'init.php';

    use FacebookAlbumDownloader\Helper\Downloader;

    $downloader = new Downloader;
    try{
        if(isset($_REQUEST['id'])){
            $fileName = $downloader->downloadAlbums($_REQUEST['id'],$_SESSION['profile']['username']);
            $downloader->getZip($fileName);
        }
    }catch(\Google_Service_Exception $e){
        exit;
    }