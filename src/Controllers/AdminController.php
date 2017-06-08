<?php

namespace Hoppermagic\Kobalt\Controllers;

use App\Http\Controllers\Controller;
use Hoppermagic\Kobalt\Services\LaravelFormBuilder\Form;
use Hoppermagic\Kobalt\Helpers\FormHelper;
use Hoppermagic\Kobalt\Helpers\ImageHelper;
use Hoppermagic\Kobalt\Helpers\RouteHelper;
use Illuminate\Support\Collection;


abstract class AdminController extends Controller
{
    protected $title;
    protected $back_link;
    protected $back_title = '';
    protected $model;
    protected $form_class;
    protected $deletable;


    /**
     * Generates the overview view
     *
     * @param Collection $collection
     * @return $this
     */
    protected function getOverviewView(Collection $collection)
    {
        $model = $this->model;

        $route_helper = new RouteHelper();

        return view('kobalt::overview')->with([
            'data' => $collection,
            'create_path' => $route_helper->getNamedRoute('create'),
            'edit_path' => "/" . app('router')->getCurrentRoute()->uri,
            'title' => $this->title,
            'meta' => $model::getOverviewMeta()
        ]);
    }



    /**
     * Generates the create view
     *
     * @param Form $create_form
     * @return $this
     */
    protected function showCreateView(Form $create_form)
    {
        $create_form->add('save_field', 'submit', [
            'label' => 'Save ' . $this->title,
            'attr' => ['class' => 'btn btn-primary']
        ]);

        return view('kobalt::create')->with([
            'title' => $this->title,
            'back_link' => $this->back_link,
            'back_title' => $this->back_title,
            'create_form' => $create_form
        ]);
    }


    //!TODO should this be a helper class??

    /**
     * Stores any files that have been uploaded with the form
     * We store the files after the resource has been saved as we can access image_meta from the resource
     * !TODO need to handle stuff other than images
     *
     * @param $request
     * @param $resource
     */
    protected function storeFiles($request, $resource)
    {
        if($request->allFiles()){
            //!TODO make sure they are images
            $image_helper = new ImageHelper();

            $ignore_thumbs = $this->generateIgnoreList($request);

            $image_helper->generateThumbs($request, $resource->getImageMeta(), $ignore_thumbs);
        }
    }



    /**
     * Updates any stored files if they are re-uploaded
     *
     * @param $request
     * @param $resource
     */
    protected function updateStoredFiles($request, $resource)
    {
        $image_helper = new ImageHelper();

        $ignore_thumbs = $this->generateIgnoreList($request);

        $image_helper->checkImageStatus($request, $resource, $ignore_thumbs); //!TODO this is a LOT of stuff to pass through....
    }



    /**
     * Persists the resource
     * adds on any additional data not present in the request, like file name extensions
     *
     * @param $request
     * @param array $additional_data
     * @return mixed
     */
    protected function storeResource($request, Array $additional_data = null)
    {
        $model = $this->model;

        $resource = $model::create(array_merge($request->all(), $additional_data));

        $this->showCreatedMessage();

        return $resource;
    }



    /**
     * Flashes the created message on screen
     *
     */
    protected function showCreatedMessage()
    {
        flash('Your ' . $this->title . ' was successfully created', 'success');
    }



    /**
     * Shows the edit view
     *
     * @param $edit_form
     * @return $this
     */
    protected function showEditView($edit_form)
    {
        $route_params = app('router')->getCurrentRoute()->parameters;
        $route_helper = new RouteHelper();

        // If we cant delete this resource

        if($this->deletable === false) {
            $delete_link = null;
        }else {
            $delete_link = $route_helper->getNamedRoute('confirmdel', $route_params);
        }

        // Append save button

        $edit_form->add('save_field', 'submit', [
            'label' => 'Save ' . $this->title,
            'attr' => ['class' => 'btn btn-primary']
        ]);


        return view('kobalt::edit')->with(
            array(
                'title' => $this->title,
                'back_title' => $this->back_title,
                'back_link' => $this->back_link,
                'delete_link' => $delete_link,
                'edit_form' => $edit_form,
            )
        );
    }



    /**
     * Updates the resource in storage
     * adds on any additional data not present in the request, like the full file name rabbit.jpg
     *
     * @param $resource
     * @param $request
     * @param array $additional_values
     * @return mixed
     */
    protected function updateResource($resource, $request, Array $additional_values = null)
    {
        $resource->update(array_merge($request->all(), $additional_values));

        flash('Your ' . $this->title . ' was successfully updated', 'success');

        return $resource;
    }



    /**
     * Shows the confirm delete view
     *
     * @return $this
     */
    protected function showConfirmDeleteView()
    {
        // Gets the resource through the current route, could just pass it in if needs be
        $route_params = app('router')->getCurrentRoute()->parameters;
        $resource = array_last($route_params);

        $form_helper = new FormHelper();
        $route_helper = new RouteHelper();

        $delete_form = $form_helper->generateDeleteForm(
            $resource,
            $route_helper->getNamedRoute('destroy', $route_params)
        );

        return view('kobalt::delete')->with(
            array(
                'title' => $this->title,
                'back_link' => $route_helper->getNamedRoute('edit', $route_params),
                'back_title' => $this->back_title,
                'delete_form' => $delete_form
            )
        );
    }



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
     * Triggered by ajax called when lightbox has closed and we need to refresh
     * the image grid
     */
    protected function imageLoad()
    {
        $model = $this->model;

        $id = $_POST['resource_id'];

        $resource = $model::with('images')->find($id);
        $images = $resource->images->toArray();

        return response()->json($images);
    }



    /**
     * Triggered by ajax called when sorting overview page
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function overviewSort(){

        $model = $this->model;

        $ids = explode(',', ($_POST['ids']));

        foreach ($ids as $index => $id) {

            $resource = $model::find($id);
            $resource->sort_on = $index;
            $resource->save();
        }

        return response()->json(['status' => 'success']);
    }



    /**
     * Triggered by ajax called when sorting the image grid
     *
     * @param $model
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sortImages($model)
    {
        $ids = explode(',', ($_POST['ids']));

        foreach ($ids as $index => $id) {
            $image = $model::find($id);
            $image->sort_on = $index;
            $image->save();
        }

        return response()->json(['status' => 'success']);
    }



    /**
     * Does nothing needs to be overridden in child
     *
     * @param $request
     * @return null
     */
    protected function generateIgnoreList($request)
    {
        return null;
    }
}
