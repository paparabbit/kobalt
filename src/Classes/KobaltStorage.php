<?php

namespace Hoppermagic\Kobalt\Classes;


use Hoppermagic\Kobalt\Helpers\ImageHelper;

class KobaltStorage
{
    //!TODO Handle stuff other than images
    /**
     * Updates any stored images if they are re-uploaded
     *
     * @param $request
     * @param $resource
     * @param null $ignore_thumbs
     */
    public function updateStoredImages($request, $resource, $ignore_thumbs = null)
    {
        $image_helper = new ImageHelper();

        $image_helper->checkImageStatus($request, $resource, $ignore_thumbs); //!TODO this is a LOT of stuff to pass through....
    }


    /**
     * Stores any images that have been uploaded with the form
     * We store the images after the resource has been saved as we can access image_meta from the resource
     *
     * @param $request
     * @param $resource
     * @param null $ignore_thumbs
     */
    public function storeImages($request, $resource, $ignore_thumbs = null)
    {
        if($request->allFiles()){
            //!TODO make sure they are images
            $image_helper = new ImageHelper();

            $image_helper->generateThumbs($request, $resource->getImageMeta(), $ignore_thumbs);
        }
    }
}
