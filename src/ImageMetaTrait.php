<?php

namespace Hoppermagic\Kobalt;


trait ImageMetaTrait
{
    /**
     * Returns the image_meta as a collection
     *
     * @param $meta
     * @return mixed
     */
    protected function getImageMetaAsCollection($meta){

        $collection = array();

        foreach ($meta as $image_name => $image){

            $thumbnail_array = array();
            $thumbnails = $image['thumbnails'];

            foreach ($thumbnails as $thumb_name => $thumbnail){
                $thumbnail_array += [$thumb_name => collect($thumbnail)];
            }

            $collection += [$image_name => collect($thumbnail_array)];
        }
        
        return collect($collection);
    }
}
