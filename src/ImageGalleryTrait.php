<?php

namespace Hoppermagic\Kobalt;


use Hoppermagic\Kobalt\Helpers\RouteHelper;

trait ImageGalleryTrait
{
    /**
     * Generates a nested image view usually a ManyToMany Images
     *
     * @param $images
     * @param $nested_wildcard
     * @return $this
     */
    protected function generateNestedImageView($images, $nested_wildcard)
    {
        // !TODO if this fails we can just pass in the resource
        // Resource should be the last param....

        $route_params = app('router')->getCurrentRoute()->parameters;
        $resource = array_last($route_params);
        $route_helper = new RouteHelper();

        return view('kobalt::image_overview')->with(
            array(
                'images' => $images, //!TODO what if theres only one image!!!!!
                'resource_id' => $resource->id,
                'addimage_action' => route('admin.' . $nested_wildcard . '.create', $resource), //Todo change this in the view
                'editimage_path' => $route_helper->getNestedEditPath($nested_wildcard)
            )
        );
    }



    /**
     * Triggered by ajax called when sorting the image grid
     *
     * @param $model
     * @param $ids
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sortImages($model, $ids)
    {
        $ids = explode(',', $ids);

        foreach ($ids as $index => $id) {
            $image = $model::find($id);
            $image->sort_on = $index;
            $image->save();
        }

        return response()->json(['status' => 'success']);
    }



    /**
     * Triggered by ajax called when lightbox has closed and we need to refresh the image grid
     *
     * @param $model
     * @param $related
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    protected function loadImages($model, $related, $id)
    {
        $resource = $model::with($related)->find($id);
        $images = $resource->images->toArray();

        return response()->json($images);
    }
}