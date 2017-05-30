<?php

namespace Hoppermagic\Kobalt\Models;
use Illuminate\Database\Eloquent\Model;

abstract class KobaltModel extends Model
{
    protected static $image_meta;

    protected static $overview_meta;


    /**
     * Accessor
     * Returns the overview meta as a collection
     * Need to grab this one statically
     *
     * @return mixed
     */
    public static function getOverviewMeta()
    {
        return static::$overview_meta;
    }



    /**
     * Accessor
     * Returns the image_meta as a collection
     *
     * @return mixed
     */
    public function getImageMeta(){

        $collection = array();

        foreach (static::$image_meta as $image_name => $image){

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
