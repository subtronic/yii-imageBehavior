<?php

class ImageBehavior extends CActiveRecordBehavior {

    /**
     * Max dimenstions for tumbnailed image
     * @var array
     */
    public $maxDimensions = array();
    
    /**
     * Prefixs for 2 type of file
     * @var array
     */
    public $typePrefix = array();


    /**
     * Placeholder image, relate on gender
     * @var array
     */
    public $noPhotoArray = array();
    
    /**
     * Path from app root to dir, where images has saved
     * @var string
     */
    public $partPathFromBase = null;
    
    /**
     * Part of URL to image dir
     * @var string
     */
    public $partUrlFromHome = null;
    
    /**
     * Image attribute name
     * @var string
     */
    public $propertyName = null;

    /**
     * Attribute name, which use
     * for identify image name
     * @var string
     */
    public $uniqeAttrName = null;

    /**
     * Return unique identify for image
     * P.S. You can define our own rule
     * @return string 
     */
    public function getUniqueName()
    {
        if($uniqeAttrName){
            return $this->owner->{$uniqeAttrName};
        } else {
            return null;

        }
    }
    /**
     * Return path to image
     * @param  string $type 
     * @return string       
     */
    public function getPath($type = null)
    {
        if($type){
            if($type == 'tmb'){
                return Yii::app()->basePath . $this->owner->partPathFromBase . $this->owner->typePrefix['tmb'] . $this->owner->{$this->owner->propertyName};
            } elseif($type == 'origin'){
                return Yii::app()->basePath . $this->owner->partPathFromBase . $this->owner->typePrefix['origin'] . $this->owner->{$this->owner->propertyName};
            }
        }
        return null;
    }

    public function getUrl($type = null)
    {
        if($type){
            if($type == 'tmb'){
                return $this->owner->urlToImageFolder . $this->owner->typePrefix['tmb'] . $this->owner->{$this->owner->propertyName};
            } elseif($type == 'origin'){
                return $this->owner->urlToImageFolder . $this->owner->typePrefix['origin'] . $this->owner->{$this->owner->propertyName};
            }
        }
        return null;
    }
    /** 
     * Return path to dir with image
     * @return string 
     */
    public function getPathToImageFolder()
    {
        return Yii::app()->basePath . $this->owner->partPathFromBase;
    }
    
    /**
     * Return path to original image
     * @return string 
     */
    public function getPathToImage()
    {
        $path = $this->getPath('origin');
        if(!file_exists($path)){
            $path = false;
        }
        return $path;
    }

    /**
     * Return path to thumbnailed image 
     * @return string 
     */
    public function getPathToMiniImage()
    {
        $path = $this->getPath('tmb');
        
        if(!file_exists($path)){
            $path = false;
        }
        return $path;
    }

    /**
     * Return URL to image dir
     * @return string
     */
    public function getUrlToImageFolder()
    {
        return Yii::app()->homeUrl . $this->owner->partUrlFromHome;
    }


    /**
     * Return URL to original image
     * @return string
     */
    public function getImageUrl()
    {
        $path = $this->getPath('origin'); 
        
        $url =  $this->getUrl('origin'); 
        if(!file_exists($path)){
            $url = false; 
        } 
        return $url;
    }
    
    /**
     * Return URL to tumbnailed imageя
     * @return string 
     */
    public function getMiniImageUrl()
    {
        $path = $this->getPath('tmb'); 
        $url = $this->getUrl('tmb'); 
        if(!file_exists($path)){
            $url = false;
        }
        return $url;
    }


    /**
     * Model has image?
     * @return boolean
     */
    public function hasImage()
    {
        $path =  $this->getPath('origin'); 
        return file_exists($path);
    }

    /**
     * Before Save handler for model
     */
    public function beforeSave($event)
    {
        $file = CUploadedFile::getInstance($this->owner, $this->owner->propertyName);
        if ($file instanceof CUploadedFile) {
            if(isset($this->owner->{$this->owner->propertyName}) && file_exists($this->owner->pathToImage)){
                $this->owner->deleteImage($this->owner->{$this->owner->propertyName});
            }

            $file->saveAs($this->owner->pathToImageFolder . 'origin_' . $this->owner->id . '.' . $file->extensionName);
            $this->owner->{$this->owner->propertyName} = $file;
            
            $this->owner->resize();
            
            $this->owner->{$this->owner->propertyName} = $this->owner->id . '.' . $file->extensionName;      
        }
        return parent::beforeSave($event);
    }

    /**
     * Change image size
     */
    public function resize()
    {
        $path = $this->owner->pathToImageFolder . $this->owner->typePrefix['origin'] . $this->owner->id . '.' . $this->owner->{$this->owner->propertyName}->extensionName;
        $file_name = $this->owner->typePrefix['tmb']. $this->owner->id . '.' . $this->owner->{$this->owner->propertyName}->extensionName;
       
        $image = new Imagick($path);
        
        if($image){

            $imageprops = $image->getImageGeometry();

            $fitbyWidth = (($this->owner->maxDimensions['width'] / $imageprops['width']) < ($this->owner->maxDimensions['height'] / $imageprops['height'])) ? true : false;
            
            if($fitbyWidth){
                $image->thumbnailImage($this->owner->maxDimensions['width'], 0, false);
            } else {
                $image->thumbnailImage(0, $this->owner->maxDimensions['height'], false);
            }

            $image->writeImage($this->owner->pathToImageFolder.$file_name);
            $image->destroy();

        } else {
            throw new CHttpExtension(503, 'Изображение '.$pathToImage.' не существует');
        }
    }
    
    /**
     * Delete tumbnailed and original image
     */         
    public function deleteImage($imgName = null)
    {
        $name = $imgName ? : $this->owner->{$this->owner->propertyName};

        if($name){
            if( file_exists($this->owner->pathToImageFolder.$this->owner->typePrefix['tmb'].$name) ) unlink($this->owner->pathToImageFolder.$this->owner->typePrefix['tmb'].$name);
            if( file_exists($this->owner->pathToImageFolder.$this->owner->typePrefix['origin'].$name) ) unlink($this->owner->pathToImageFolder.$this->owner->typePrefix['origin'].$name);
            $this->owner->{$this->owner->propertyName} = '';
            $this->owner->save();
        }
    }
}