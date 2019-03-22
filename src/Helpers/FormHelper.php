<?php

namespace Hoppermagic\Kobalt\Helpers;

use Kris\LaravelFormBuilder\FormBuilderTrait;

class FormHelper
{
    use FormBuilderTrait;

    /**
     * Generates the create form
     * For many to many's eg Images Belonging to a Project we need to pass through the current resource
     *
     * @param $form_class
     * @param array $data
     * @param array $remove
     * @return mixed
     */
    public function generateCreateForm($form_class, Array $data = [], Array $remove = [])
    {
        $route_params = app('router')->getCurrentRoute()->parameters;
        $route_helper = new RouteHelper();

        $create_form = $this->form($form_class,[
            'method' => 'POST',
            'url' => $route_helper->getNamedRoute('store', $route_params)
        ],$data);

        foreach ($remove as $item){
            $create_form->remove($item);
        }

        return $create_form;
    }


    /**
     * Generates the edit form
     *
     * @param $form_class
     * @param $resource
     * @param array $data
     * @param array $remove
     * @return mixed
     */
    public function generateEditForm($form_class, $resource, Array $data = [], Array $remove = [])
    {
        $route_helper = new RouteHelper();

        $edit_form = $this->form($form_class,[
            'method' => 'PATCH',
            'model' => $resource,
            'url' => $route_helper->getNamedRoute('update', $route_params)
        ],$data);

        foreach ($remove as $item){
            $edit_form->remove($item);
        }

        return $edit_form;
    }


    /**
     * Delete button needs to be wrapped by form, this generates the form and button
     * @param $resource
     * @param $delete_route
     * @return \Kris\LaravelFormBuilder\Form
     */
    public function generateDeleteForm($resource, $delete_route){

        $delete_form = $this->plain([
            'method' => 'DELETE',
            'id' => 'delete_form',
            'model' => $resource,
            'url' => $delete_route
        ]);

        return $delete_form;
    }
}
