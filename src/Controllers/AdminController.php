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
    protected $form_helper;
    protected $form_class;
    protected $deletable;


    /**
     * Generates the overview view
     *
     * @param Collection $collection
     * @param array $settings
     * @return $this
     */
    protected function getOverviewView(Collection $collection, $settings = [])
    {
        $route_helper = new RouteHelper();

        return view('kobalt::overview')->with([
            'data' => $collection,
            'create_path' => $route_helper->getNamedRoute('create'),
            'edit_path' => "/" . app('router')->getCurrentRoute()->uri,
            'title' => $this->title,
            'meta' => $this->model->getOverviewMeta()
        ] + $settings);
    }



    /**
     * Generates the create view
     *
     * @param Form $create_form
     * @return $this
     */
    protected function getCreateView(Form $create_form)
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



    /**
     * Persists the resource
     * adds on any additional data not present in the request, like file name extensions
     *
     * @param $request
     * @param array $additional_data
     * @return mixed
     */
    protected function storeResource($request, Array $additional_data = [])
    {
        $resource = $this->model->create(array_merge($request->all(), $additional_data));

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
    protected function getEditView($edit_form)
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
    protected function updateResource($resource, $request, Array $additional_values = [])
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
    protected function getConfirmDeleteView()
    {
        // Gets the resource through the current route, could just pass it in if needs be
        $route_params = app('router')->getCurrentRoute()->parameters;
        $resource = array_last($route_params);

        $route_helper = new RouteHelper();

        $delete_form = $this->form_helper->generateDeleteForm(
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
     * Triggered by ajax called when sorting overview page
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function overviewSort()
    {
        $ids = explode(',', ($_POST['ids']));

        foreach ($ids as $index => $id) {

            $resource = $this->model->find($id);
            $resource->sort_on = $index;
            $resource->save();
        }

        return response()->json(['status' => 'success']);
    }



    /**
     * Does nothing needs to be overridden in child
     *
     * @param $request
     * @return null
     */
    protected function generateThumbnailIgnoreList($request)
    {
        return null;
    }
}
