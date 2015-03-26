<?php
namespace jdRoll\service;

/**
 * Manage information and listing of character
 *
 * @package thumbnailService
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */

class ThumbnailService {

    public function __construct($db, $session)
    {
        $this->db = $db;
        $this->session = $session;
    }

    /**
     * Generate thumbnail for all required items
     */
    public function generateThumbnails(){
        //Size of the thumbnail
        $sql = "SELECT id, avatar FROM personnages WHERE 1";

        foreach($this->db->fetchAll($sql) as $perso){
            $this->generateThumbnail('perso', $perso['id'], $perso['avatar'], $force=false);
        }
    }

    /**
     * Creates one thumbnail.
     * if $force=FALSE, will not overwrite existing thumbnail
     * @param $type
     * @param $id
     * @param $avatarPath
     * @param bool $force
     */
    public function generateThumbnail($type, $id, $avatarPath, $force=true){

        //Square size
        $size = 64;

        //Create directory if does not exist
        $folder = FOLDER_FILES . '/thumbnails/';
        if( ! file_exists($folder)){
            mkdir(FOLDER_FILES, true);
            mkdir($folder, true);
        }

        $isRemote = strpos($avatarPath, 'http') === 0;

        //Compute pathes & extension
        $thumbPath = $folder . $type . "_{$id}.png";
        $filepath = $avatarPath;
        $extension = null;

        if(file_exists($thumbPath) && !$force){
            //Thumbnail exists, continue
            return;
        }
        else if( ! $isRemote){
            //For now, ignore local files as I do not have them
            $filepath = FOLDER_FILES . '/' . basename($avatarPath);
            if( ! file_exists($filepath)){
                $this->_generateDefaultThumbnail($type, $thumbPath);
            }
        }
        else{
            //Get extension for remote files, sometimes it is not present in the URL and can cause errors
            $headers = @get_headers($filepath, 1);
            //If redirection for image, get latest content type
            $contentType = is_array($headers['Content-Type']) ? $headers['Content-Type'][count($headers['Content-Type'])-1]:$headers['Content-Type'];
            $extension = str_replace('image/', '', $contentType);
        }

        //Create thumbnail
        set_time_limit(30);
        $result = $this->_makeThumb($filepath, $thumbPath, $size, 100, $extension);

        //If an error arose
        if($result === false){
            //Use default empty image
            $this->_generateDefaultThumbnail($type, $thumbPath);
        }
    }

    /**
     * Shortcut to create a default profile thumbnail
     * @param $type
     * @param $thumbPath
     */
    protected function _generateDefaultThumbnail($type, $thumbPath){
        copy(FOLDER_FILES . '/../img/default'.ucfirst($type).'.png', $thumbPath);
    }

    /**
     * Create a squared thumbnail
     * @param $source
     * @param $destination
     * @param int $square_size
     * @param int $quality
     * @param $forceExtension
     * @return bool
     */
    protected function _makeThumb($source, $destination, $square_size=167, $quality=90, $forceExtension) {

        $status  = false;
        list($width, $height, $type, $attr) = @getimagesize($source);

        if( ! $width || ! $height){
            //Problem loading
            return false;
        }

        if($width< $height) {
            $width_t =  $square_size;
            $height_t    =   round($height/$width*$square_size);
            $off_y       =   ceil(($width_t-$height_t)/2);
            $off_x       =   0;

        } elseif($height< $width) {

            $height_t    =   $square_size;
            $width_t =   round($width/$height*$square_size);
            $off_x       =   ceil(($height_t-$width_t)/2);
            $off_y       =   0;

        } else {

            $width_t    =   $height_t   =   $square_size;
            $off_x      =   $off_y      =   0;
        }

        $thumb_p    = imagecreatetruecolor($square_size, $square_size);

        $extension  = $forceExtension ? $forceExtension : pathinfo($source, PATHINFO_EXTENSION);

        if($extension == "gif" or $extension == "png"){

            imagecolortransparent($thumb_p, imagecolorallocatealpha($thumb_p, 0, 0, 0, 127));
            imagealphablending($thumb_p, false);
            imagesavealpha($thumb_p, true);
        }

        if ($extension == 'jpg' || $extension == 'jpeg')
            $thumb = imagecreatefromjpeg($source);
        else if ($extension == 'gif')
            $thumb = imagecreatefromgif($source);
        else if ($extension == 'png')
            $thumb = imagecreatefrompng($source);
        else
        {echo "Wrong extension: $source ($extension)"; return;}

        $bg = imagecolorallocate ( $thumb_p, 255, 255, 255 );
        imagefill ($thumb_p, 0, 0, $bg);

        imagecopyresampled($thumb_p, $thumb, $off_x, $off_y, 0, 0, $width_t, $height_t, $width, $height);

        $destinationExtension = pathinfo($destination, PATHINFO_EXTENSION);
        if ($destinationExtension == 'jpg' || $destinationExtension == 'jpeg')
            $status = @imagejpeg($thumb_p,$destination,$quality);
        if ($destinationExtension == 'gif')
            $status = @imagegif($thumb_p,$destination,$quality);
        if ($destinationExtension == 'png')
            $status = @imagepng($thumb_p,$destination,9);

        imagedestroy($thumb);
        imagedestroy($thumb_p);

        return $status;
    }
}

