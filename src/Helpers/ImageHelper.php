<?php

namespace Hoppermagic\Kobalt\Helpers;

use Hoppermagic\Kobalt\Classes\Transforms;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageHelper
{
    /**
     * Generates an array of images and a preview view in the form of
     * [computerimage => view,]
     * which can be fed into the edit view
     *
     * @param $images
     * @return array
     */
    public function generateImageArray($images){

        $image_array = array();

        foreach ($images as $name => $image){

            $image_array += [$name => $this->createPreviewImage($image)];
        }

        return $image_array;
    }



    /**
     * Returns either null or a preview image view
     *
     * @param $file_name
     * @return $this|null
     */
    public function createPreviewImage($file_name){

        if ($file_name) {

            $image = view('kobalt::image')->with(
                array(
                    'image' => $file_name
                )
            );

            return $image;
        }

        return null;
    }



    /*
    public function generateBulkThumb($image, $new_name, $field_name, $image_meta, $ignore_thumbs = null)
    {
        foreach($image_meta as $image_name => $thumb_data) {
            if($field_name == $image_name){

                // If a thumb is to be ignored, remove it from the $thumb_data

                if ($ignore_thumbs != null) {
                    if ($image_name == $ignore_thumbs['imageref']) {

                        foreach ($thumb_data as $thumb_name => $thumb) {
                            if (in_array($thumb_name, $ignore_thumbs['thumbs'])) {
                                unset($thumb_data[$thumb_name]);
                            }
                        }
                    }
                }

                $this->processThumbs($image, $new_name, $thumb_data);
            }
        }
    }
    */



    /**
     * Generates the requested thumbnails based on supplied meta
     * option to ignore particular thumbs/sizes when creating thumbs
     *
     * @param Request $request
     * @param $image_meta
     * @param null $ignore_thumbs
     */
    public function generateThumbs($request, Collection $image_meta, $ignore_thumbs = null)
    {
        $files = $request->allFiles();

        foreach($files as $file_name => $file_data) {

            foreach($image_meta as $image_name => $thumb_data) {
                if($file_name == $image_name){

                    // If a thumb is to be ignored, remove it from the $thumb_data

                    if ($ignore_thumbs != null) {
                        if ($image_name == $ignore_thumbs['imageref']) {

                            foreach ($thumb_data as $thumb_name => $thumb) {
                                if (in_array($thumb_name, $ignore_thumbs['thumbs'])) {
                                    unset($thumb_data[$thumb_name]);
                                }
                            }
                        }
                    }

                    $name = $this->findImageName($request, $image_name);
                    $this->processThumbs($file_data, $name, $thumb_data);
                }
            }
        }
    }



    /**
     * Provides an array in the format of
     * field_name => extension
     *
     * @param Request $request
     * @return Array
     */
    public function generateExtensionArray($request)
    {
        $files = $request->allFiles();
        $extension_array = [];

        foreach($files as $file_name => $file_data) {

            $extension = $file_data->getClientOriginalExtension();

            $extension_column_name = $this->findExtensionColumn($file_name);

            $extension_array += [$extension_column_name => $extension];
        }

        return $extension_array;
    }



    /**
     * On an edit decide what changes we need to do the image data
     *
     * @param $request
     * @param $original
     * @param null $ignore_thumbs
     */
    public function checkImageStatus ($request, $original, $ignore_thumbs = null)
    {
        $files = $request->allFiles();
        $image_meta = $original->getImageMeta();

        foreach($image_meta as $meta_name => $thumb_data) {

            foreach($files as $file_name => $file_data) {

                if($file_name == $meta_name) {

                    // There is an image uploaded for this meta
                    // Delete existing and generate thumbs for the new

                    $this->deleteImages($original, $meta_name, $thumb_data);
                    $this->generateThumbs($request, $image_meta, $ignore_thumbs);

                    continue 2;
                }
            }

            // If we get here there must be NO image uploaded for this asset, so check for name change

            $new_name = $this->findImageName($request, $meta_name);
            $current_name = $this->findImageName($original, $meta_name);
            $current_ext = $this->findExtension($original, $meta_name);

            if($new_name != $current_name){

                $this->changeThumbnailName($current_name, $current_ext, $new_name, $thumb_data);
            }
        }
    }



    /**
     * Changes the filename of an image assets thumbnails
     *
     * @param $current_name
     * @param $current_ext
     * @param $new_name
     * @param Collection $thumb_data
     */
    private function changeThumbnailName($current_name, $current_ext, $new_name, Collection $thumb_data)
    {
        foreach ($thumb_data as $thumb){

            $current_image = $thumb->get('path') .'/'. $current_name. $thumb->get('suffix') . '.' . $current_ext;
            $new_image = $thumb->get('path')  .'/'. $new_name. $thumb->get('suffix') . '.' . $current_ext;

            // If thumbs are ignored its possible $current_image won't exist so need to check first

            if(Storage::has($current_image)){
                Storage::move($current_image, $new_image);
            }
        }
    }


    /**
     * Loop through all the image sizes 'thumbs' in the meta item and change name
     *
     * @param $original
     * @param $file_fieldname
     * @param $thumb_data
     * @return bool
     */
    private function deleteImages($original, $file_fieldname, Collection $thumb_data)
    {
        $current_image_name = $this->findImageName($original, $file_fieldname);
        $current_image_ext = $this->findExtension($original, $file_fieldname);

        // If the name or extension is null (empty db column) there is nothing to delete
        if(is_null($current_image_name) || is_null($current_image_ext)){
            return false;
        }

        foreach ($thumb_data as $thumb){

            $suffix = $thumb->has('suffix') ? $thumb->get('suffix') : '';

            $current_image =  $thumb->get('path') .'/'. $current_image_name. $suffix . '.' . $current_image_ext;

            // If thumbs are ignored its possible they won't exist so need to check first

            if(Storage::has($current_image)){
                Storage::delete($current_image);
            }
        }
    }



    /**
     * Run through each thumbnail size and save the image
     *
     * @param $file
     * @param $name
     * @param $thumb_data
     */
    private function processThumbs($file, $name, Collection $thumb_data)
    {
        $extension = $file->getClientOriginalExtension();

        $image = Image::make($file->getRealPath());
        $image->backup();

        foreach ($thumb_data as $thumb_name => $thumb) {

            $image->reset();

            // Are there transforms....

            if($thumb->has('transformations')){

                // Loop through and add each transform

                foreach ($thumb->get('transformations') as $trans_name => $trans_data) {

                    $image = $this->addTransformation($image, $trans_name, $trans_data);

                    // Sharpen a touch??
                    $image->sharpen(5);
                }
            }

            if($thumb_name == 'original' || $thumb->has('use-as-is')){

                // If its the original, or is specified to 'use-as-is' just move the original file into the folder
                // if we use intervention it will get compressed

                Storage::put($thumb->get('path') .'/'. $name . $thumb->get('suffix') . '.'. $extension, File::get($file));

            }else{

                Storage::put($thumb->get('path') .'/'. $name . $thumb->get('suffix') . '.'. $extension, (string) $image->encode($extension, 96));
            }
        }

        // All saved free up memory

        $image->destroy();
        gc_collect_cycles();
    }



    /**
     * Add the requested transform to the image and return the image
     * ready for the next transform to be added
     *
     * @param $image
     * @param $trans_name
     * @param $trans_data
     * @return mixed
     */
    private function addTransformation($image, $trans_name, $trans_data)
    {
        if($trans_name == Transforms::FIT){

            $image->fit( $trans_data[0], $trans_data[1] );
        }

        if($trans_name == Transforms::CROP){

            $image->crop( $trans_data[0], $trans_data[1] );
        }


        if($trans_name == Transforms::FIT_TO_BOX){

            if($image->width() > $image->height()){

                $image->fit( $trans_data[0], $trans_data[1], function ($constraint) {
                    $constraint->upsize();
                });

            }else{

                $image->resize( $trans_data[0], $trans_data[1], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
        }


        if($trans_name == Transforms::RESIZE){

            $image->resize( $trans_data[0], $trans_data[1], function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        return $image;
    }



    /**
     * We need to rename the image, but to what?
     * Try and find a 'name' field within the submitted data that matches the filename
     *
     * @param Request $request
     * @param $name
     * @return string
     */
    private function findImageName($request, $name)
    {
        if(strpos($name, '_') === FALSE){
            throw new Exception("Naming conventions problem on findImageName");
        }

        $split_name = explode('_', $name);
        $name = $split_name[0] . '_name';

        if(!isset($request->$name)){
            throw new Exception("Name is not set on finsImageName");
        }

        return $request->$name;
    }



    /**
     * Finds the column in the table that's storing the extension
     *
     * @param $name
     * @return string
     */
    private function findExtensionColumn($name)
    {
        if(strpos($name, '_') === FALSE){
            throw new Exception("Naming conventions problem on findExtensionColumn");
        }

        $split_name = explode('_', $name);
        $name = $split_name[0] . '_ext';

        return $name;
    }



    /**
     * Given the asset name returns its extension
     *
     * @param $original
     * @param $name
     * @return mixed
     */
    private function findExtension($original, $name)
    {
        $column_name = $this->findExtensionColumn($name); //!TODO not a good name model attribute????

        $extension = $original->$column_name;

        return $extension;
    }
}
