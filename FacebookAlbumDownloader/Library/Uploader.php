<?php
namespace FacebookAlbumDownloader\Library;

use FacebookAlbumDownloader\Helper\GoogleClient;
use FacebookAlbumDownloader\Helper\FacebookClient;

class Uploader{
    public function __construct(){}

    public function uploadAlbums($albumIds,$userName)
    {
        // Check albumID is string or array
        if(gettype($albumIds)!="array") $albumIds = array($albumIds); // If single album id. Make single Array

        // Get Google Client
        $google = new GoogleClient();
        

        // Initiate Google Drive Service
        $google->initDriveService();

        // Set Folder to create in Google Drive as facebook_<username>_album
        $folderName = 'facebook_'.$userName.'_albums';

        // Check if folder is already created in Google Drive and get the folder id
        if($google->listFilesFolders($folderName,'root','folders') == FALSE)
            $albumsFolder = $google->createFolder('root',$folderName);
        else
            $albumsFolder = array_flip($google->listFilesFolders($folderName,'root','folders'))[$folderName];

        // Set temporary directory path
        $path = __DIR__.'/../../tmp/';

        // Created Facebook Client
        $fb = new FacebookClient;
        $fb->authenticate();
        $uploadCount = 0;
        // Loop to start upload each albums of array
        foreach ($albumIds as $albumId) {
            // Get Album Data
            $album = $fb->getAlbum($albumId);

            // Senitize album Name to create directory of album name
            $albumName = $album['name'];
            $albumName = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $albumName);
            $albumName = mb_ereg_replace("([\.]{2,})", '', $albumName);

            // Check if the photos are already uplodaded or not
            // or which photos are already uploaded, so those photo could be skipped
            if($google->listFilesFolders($albumName,$albumsFolder,'folders') != FALSE){
                // Getting all folders which are already created in facebook_<username>_album folder
                $albumFolder = array_flip($google->listFilesFolders($albumName,$albumsFolder,'folders'))[$albumName];

                // Get the list of photos already uploaded
                $photoList = $google->listFilesFolders('',$albumFolder,'files');
                
                // Itterate each photo
                foreach($album['photos'] as $photo) {
                    $file = $photo['id'].'.jpg';
                    // Check if the photo is already not uploaded
                    if(!in_array($file, $photoList)){
                        // Copy photo to server
                        copy($photo['images'][0]['source'],$path.$file);
                        // Copy to Google Drive
                        $google->uploadFile($albumFolder,$path.$file,$file);
                        $uploadCount++;
                        // Delete from server
                        unlink($path.$file);
                    }
                }
                // If there is no folder of that album name
            }else{
                // Create Album folder
                $albumFolder = $google->createFolder($albumsFolder,$albumName);
                // Upload all photow
                foreach($album['photos'] as $photo) {
                    $file = $photo['id'].'.jpg';
                    // Copy photo to server
                    copy($photo['images'][0]['source'],$path.$file);
                    // Copy to Google Drive
                    $google->uploadFile($albumFolder,$path.$file,$file);
                    $uploadCount++;
                    // Delete from server
                    unlink($path.$file);
                }
            }
        }
        echo "Total Uploaded Photos : ".$uploadCount;
    }
}