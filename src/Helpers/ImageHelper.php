<?php

namespace Hoppermagic\Kobalt\Helpers;

use Exception;
use Hoppermagic\Kobalt\Classes\Transforms;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageHelper
{
    private $resource;

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
}
