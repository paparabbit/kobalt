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






//    public function createPreviewImage($file_name, $meta_path){
//
//        if ($file_name) {
//
////            dd(config('imagepaths.'.getenv('IMAGE_PATH').'.url') . $meta_path . '/');
//
//            $image = view('kobalt::image')->with(
//                array(
//                    'image' => config('imagepaths.'.getenv('IMAGE_PATH').'.url') . $meta_path . '/' . $file_name   //!TODO We dont want to use imagepaths!!!!!!!!!!!!
//                )
//            );
//
////            dd($image);
//
//            return $image;
//        }
//
//        return null;
//    }


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

//        dd($image_meta);
//        dd('WE FAIL HERE');

        foreach($files as $file_name => $file_data) {

            foreach($image_meta as $image_name => $thumb_data) {
                if($file_name == $image_name){

//                    dd($thumb_data);
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
//    public function generateExtensionArray($request)
//    {
//        $files = $request->allFiles();
//        $extension_array = [];
//
////        dd($request);
//
//        foreach($files as $file_name => $file_data) {
//
//            $extension = $file_data->getClientOriginalExtension();
//
//            $extension_column_name = $this->findExtensionColumn($file_name);
//
////            if( !Schema::hasColumn('projects', $extension_column_name)){
////
//            //!TODO HOW CAN WE MAKE THIS CHECK
////                //!TODO THROW ERROR ABOUT NAMING CONVENSIONS
////            }
//
////            $request->request->add([$extension_column_name => $extension]);
////
////            dd($extension_column_name);
//
//            $extension_array += [$extension_column_name => $extension];
//
//        }
//
//        return $extension_array;
//    }



    /**
     * In order for this to work we need to stick to the following naming conventions
     * overviewimage_file -> The uploaded file name
     * overviewimage_name ->  The column that stores the image name
     * overviewimage -> Column that stores the image_name + extension
     * Will return an array in the format of ['overviewimage_src' => 'rabbit.jpg',]
     *
     * @param $request
     * @return array
     */
    public function getNameColumns($request)
    {
        $files = $request->allFiles();
        $filename_array = [];


        foreach($files as $file_name => $file_data) {

            $extension = $file_data->getClientOriginalExtension();

            $column_name = $this->findImageColumn($file_name);

            $image_name = $this->findImageName($request, $file_name);

            $filename_array += [$column_name => $image_name.'.'.$extension];
        }

        return $filename_array;
    }



    /**
     * This problem never existed in the last version.
     * If the image stays the same and the name changes, or the file is re-uploaded we need to update the column
     * that contains the full filename eg rabbit.jpg
     * I've not come up with a clean way to do this other than iterating over the _names in the resource
     * looking to match it to a value in the Request and seeing if its changed.
     * Will return an array in the format of ['overviewimage' => 'rabbit.jpg',]
     *
     * @param $resource
     * @param $request
     * @return array
     */
    public function getUpdatedNameColumns($resource, $request)
    {
        $files = $request->allFiles();
        $filename_array = [];

        // Find the number of _name's in the resource

        $attributes = $resource->getAttributes();

        foreach ($attributes as $key => $attribute) {

            if (ends_with($key, '_name')) {

                $column_name = preg_replace('/_name$/', '', $key);
                $extension = null;

//                dd($request->$key);

                if($attribute != $request->$key){

                    // So is there a file uploaded grab the new extension from that file
                    foreach($files as $file_name => $file_data) {

                        $base_file_name = preg_replace('/_file$/', '', $file_name);

                        // If the file uploaded is for this column - We really need to figure the comments out for this

                        if ($base_file_name == $column_name){

                            $extension = $file_data->getClientOriginalExtension();
                        }
                    }

                    // No file uploaded in which case we use the extension from the exisiting

                    if($extension == null){
                        $extension = substr(strrchr($resource->$column_name,'.'),1);
                    }

                    $filename_array += [$column_name => $request->$key.'.'.$extension];

//                    dd($filename_array);

                }

//                dd('NO NAME CHANGE');


            }
        }


        return $filename_array;
        // ['overviewimage_src' => 'rabbit.jpg',]
    }





    /**
     * On an edit decide what changes we need to do the image data
     *
     * @param $request
     * @param $original
     * @param $image_meta
     * @param null $ignore_thumbs
     * @internal param Blogpost $post
     */
    public function checkImageStatus ($request, $original, Collection $image_meta, $ignore_thumbs = null)
    {
        $files = $request->allFiles();

//        foreach($image_meta as $meta_name => $thumb_data) {
//            Log::info('>>META TEST: ' . $meta_name);
//        }
////
//        dd($image_meta);

        foreach($image_meta as $meta_name => $thumb_data) {

//            Log::info('>>META NAME ' . $meta_name);

            foreach($files as $file_name => $file_data) {

//                Log::info('>>CHECKING FILENAME: ' . $file_name);

                if($file_name == $meta_name) {

//                    dd('>>WE HAVE AN IMAGE CHANGE');
//                    Log::info('>>WE HAVE AN IMAGE CHANGE ON ' . $file_name);

                    // There is an image uploaded for this meta
                    // Delete existing and replace

                    $this->deleteImages($original, $meta_name, $thumb_data);
                    $this->generateThumbs($request, $image_meta, $ignore_thumbs);

                    continue 2;
                }
            }
            // We get here there must be NO image uploaded for this Image meta
//            dd('>>IF THIS CODE RUNS THERE MUST BE NO IMAGE UPLOADED');

//            dd($thumb_data);
//            Log::info('>>WE ARE CHECKING FOR A NAME CHANGE' . $meta_name);

            $this->checkForNameChanges($request, $original, $meta_name, $thumb_data);
        }
    }



    /**
     * Deletes all images using the meta info
     *
     * @param $original
     * @param $image_meta
     */
    public function deleteMetaImages($original, $image_meta)
    {
        foreach($image_meta as $meta_name => $thumb_data) {

            $this->deleteImages($original, $meta_name, $thumb_data);
        }
    }



    /**
     * On edit, if no image has been uploaded, check to see if the name has changed
     *
     * @param $request
     * @param $original
     * @param $file_fieldname
     * @param $thumb_data
     */
    private function checkForNameChanges($request, $original, $file_fieldname, Collection $thumb_data)
    {
//        dd($thumb_data);

        $new_image_name = $this->findImageName($request, $file_fieldname);
        $current_image_name = $this->findImageName($original, $file_fieldname);

//        dd($current_image_name);

        $current_image_ext = $this->findExtensionName($original, $file_fieldname);

        if($new_image_name != $current_image_name){

            // The file name must have been changed
            // loop through all the image sizes 'thumbs' in the meta item and change name
            // Note to self if the image hasnt changed the extension must be the same

            foreach ($thumb_data as $thumb){

//                $suffix = array_key_exists('suffix', $thumb) ? $thumb['suffix'] : '';
                $suffix = $thumb->has('suffix') ? $thumb->get('suffix') : '';

                $current_image = $thumb->get('path') .'/'. $current_image_name. $suffix . '.' . $current_image_ext;
                $new_image = $thumb->get('path')  .'/'. $new_image_name. $suffix . '.' . $current_image_ext;

                // If thumbs are ignored its possible $current_image won't exist so need to check first

                if(Storage::has($current_image)){
                    Storage::move($current_image, $new_image);
                }
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
        $current_image_ext = $this->findExtensionName($original, $file_fieldname);

        // If the name or extension is null (empty db column) there is nothing to delete
        if(is_null($current_image_name) || is_null($current_image_ext)){
            return false;
        }

        foreach ($thumb_data as $thumb){

//            $suffix = array_key_exists('suffix', $thumb) ? $thumb['suffix'] : '';
            $suffix = $thumb->has('suffix') ? $thumb->get('suffix') : '';

            $current_image =  $thumb->get('path') .'/'. $current_image_name. $suffix . '.' . $current_image_ext;

//            dd($current_image);
            // If thumbs are ignored its possible they won't exist so need to check first

            if(Storage::has($current_image)){
                Storage::delete($current_image);
            }else{
                // Doesn't exist
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
    private function processThumbs($file, $name, Collection $thumb_data) //!TODO GOT TO HERE!!!
    {
//        dd('>>HERE');
        $extension = $file->getClientOriginalExtension();

//        dd($thumb_data);

        $image = Image::make($file->getRealPath());
        $image->backup();

        foreach ($thumb_data as $thumb_name => $thumb) {

//            dd($thumb);

            $image->reset();

//        dd('>>HERE!!');
//            Log::info('>> POINT 1: ' . memory_get_usage());

            // TODO We need to set the meta up using objects

            //!TODO I dont think we need this as if item at key doesnt exist it reurns null anyway????
//            if(!$thumb->has('suffix')){
//                $thumb->put('suffix', null);
//            }

//            if(!array_key_exists('suffix', $thumb)){
//                $thumb['suffix'] = null;
//            }

//            dd($thumb);

//            dd('>>CHASING THROUGH TO HERE!!!!');

            // Are there transforms....

            if($thumb->has('transformations')){
//            if(array_key_exists('transformations', $thumb)){

                // Loop through and add each transform

                foreach ($thumb->get('transformations') as $trans_name => $trans_data) {

//                    dd('>>DOING A TRANSFORM');

                    $image = $this->addTransformation($image, $trans_name, $trans_data);

                    // Lee mawdsley specific - sharpen a touch
//                    $image->sharpen(5);
//                    dd($thumb['transformations']);
                }
            }


//            dd($thumb->get('path'));

            //!TODO FOR MAWDS IMPORT WE CAN CATCH LANDSCAPE AND PORTRAIT AND USE ORIGINALS TOO????
            if($thumb_name == 'original' || $thumb->has('use-as-is')){     //array_key_exists('use-as-is', $thumb) /*|| $thumb_name == 'landscape' || $thumb_name == 'portrait'*/){

                // If its the original, or is specified to 'use-as-is' just move the original file into the folder
                // if we use intervention it will get compressed

                Storage::put($thumb->get('path') .'/'. $name . $thumb->get('suffix') . '.'. $extension, File::get($file));
//                Log::info('>> POINT 2: ' . memory_get_usage());

            }else{

                Storage::put($thumb->get('path') .'/'. $name . $thumb->get('suffix') . '.'. $extension, (string) $image->encode($extension, 96));
//                Log::info('>> POINT 3: ' . memory_get_usage());
            }
        }

        // All saved free up memory

        $image->destroy();
        gc_collect_cycles();
    }


    /**
     * Add the requested transform to the image and return the image
     * ready for the next transform to be added only have 2 trans types at the mo
     *
     * @param $image
     * @param $trans_name
     * @param $trans_data
     * @return mixed
     */
    private function addTransformation($image, $trans_name, $trans_data)
    {
//        dd($trans_name);

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
            //!TODO THROW A REMINDER EXCEPTION ABOUT NAMING CONVENTIONS
        }

        $split_name = explode('_', $name);
        $name = $split_name[0] . '_name';

//        dd($request->$name);

        if(!isset($request->$name)){
            //!TODO Throw an ERROR ABOT NAMING CONVENTIONS
        }

        return $request->$name;
    }



    /**
     * Finds the column in the table that's storing the extension
     *
     * @param $name
     * @return string
     */
//    private function findExtensionColumn($name)
//    {
//        if(strpos($name, '_') === FALSE){
//            //!TODO THROW A REMINDER EXCEPTION ABOUT NAMING CONVENTIONS
//        }
//
//        $split_name = explode('_', $name);
//        $name = $split_name[0] . '_ext';
//
////        dd($request->$name);
//
//        return $name;
//    }


    /**
     * Finds the column that's storing the full image details, name + extension
     *
     * @param $name
     * @return string
     */
    private function findImageColumn($name)
    {
        if(strpos($name, '_') === FALSE){
            //!TODO THROW A REMINDER EXCEPTION ABOUT NAMING CONVENTIONS
        }

        $split_name = explode('_', $name);
        $name = $split_name[0];

//        dd($request->$name);

        return $name;
    }







    /**
     * Finds the actual value of the extension
     *
     * @param $request
     * @param $name
     * @return string
     */
    private function findExtensionName($request, $name)
    {
//        dd($name);
        // Remove '_file' to get the column were interested in
        $base_name = preg_replace('/_file$/', '', $name);
        $file_name = $request->$base_name;

        $extension = substr(strrchr($file_name,'.'),1);

//        dd($extension);
//        $name = $this->findExtensionColumn($name);

        return $extension;
    }
}
