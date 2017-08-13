<?php
namespace FacebookAlbumDownloader\Helper;

use FacebookAlbumDownloader\Library\FacebookClient as FacebookClient;

class Downloader{
    public function __construct(){}

    public function downloadAlbums($albumIds,$profileId)
    {
        // Check albumID is string or array
        if(gettype($albumIds)!="array") $albumIds = array($albumIds); // If single album id. Make single Array

        // Initilized temporary directory tmp directory in project root
        $tmp_dir = __DIR__.'/../../tmp/';

        // If tmp directory is not created then create it.
        if (!is_dir($tmp_dir)) mkdir($tmp_dir,0777);

        // Created temporary subdirectory as profile id
        // So multiple album with same name will not collide
        if (!is_dir($tmp_dir.$profileId))    mkdir($tmp_dir.$profileId,0777);

        // Created Zip with the filename <user_profile_id>.zip
        $zip = new \ZipArchive;
        $zipFile = $tmp_dir.$profileId.'.zip';
        if ($zip->open($zipFile, \ZipArchive::CREATE)!==TRUE) {
            exit("cannot open <$zipFile>\n");
        }

        $tmp_dir .= $profileId.'/';

        $fb = new FacebookClient;
        $fb->authenticate();
        
        // Loop to start download each albums of array
        foreach ($albumIds as $albumId) {

            // Get Album Data
            $album = $fb->getAlbum($albumId);

            // Senitize album Name to create directory of album name
            $albumName = $album['name'];
            $albumName = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $albumName);
            $albumName = mb_ereg_replace("([\.]{2,})", '', $albumName);

            // Create directory with album name
            $path = $tmp_dir.$albumName.'-'.$albumId.'/';
            mkdir($path,0777);

            // Fore Each photo of album
            foreach($album['photos'] as $photo) {
              // Initilized blank photo name if there is no caption for the photo
              $photoName = "";

              // If there is name of photo
              if(isset($photo['name'])){
                // Senitize it
                $photoName = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $photo['name']);
                $photoName = mb_ereg_replace("([\.]{2,})", '', $photoName);  
              }
              // Set filename as <photo_caption>-<photo_id>.jpg
              $file = $photoName.'-'.$photo['id'].'.jpg';

              // Copy to the server
              copy($photo['images'][0]['source'],$path.$file);
            }

            // Set parameters for zip i.e. to save each album with their saperate folder
            $options = array('add_path' => $albumName.'-'.$albumId.'/', 'remove_all_path' => TRUE);
            $zip->addGlob($path.'*.jpg',GLOB_BRACE, $options);
    
        }

        // Close Zip after all the album is archived
        $zip->close();

        // Delete whole temprory directory with downloaded photos
        $this->removeRecursive($tmp_dir);

        return $zipFile;
    }


    public function getZip($fileName){
        // Set headers to send a file to the client
        header('Content-type:  application/zip');   // Set file type as zip
        header('Content-Length: ' . filesize($fileName));   // Set file size
        header('Content-Disposition: attachment; filename="album.zip"');    // Set filename for the client
        
        // Transfer the filedata as body
        readfile($fileName);

        // Delete file after whole file is transfered
        unlink($fileName);
    }


    private function removeRecursive($dir) {
        // Remove . and .. firectories from the directory list
        $files = array_diff(scandir($dir), array('.','..')); 

        // Delete all files one by one
        foreach ($files as $file) { 
            // If current file is directory then recurse it 
            (is_dir("$dir/$file")) ? $this->removeRecursive("$dir/$file") : unlink("$dir/$file"); 
        } 

        // Remove blank directory after deleting all files
        return rmdir($dir); 
    }
}