<?php

namespace Hoppermagic\Kobalt\Services\LaravelFormBuilder;

class Form extends \Kris\LaravelFormBuilder\Form
{

    /**
     * Renders the rest of the form UP until the specified field name
     * This works in the expected way as opposed to the proper version
     *
     * @param string $field_name
     * @param bool   $showFormEnd
     * @param bool   $showFields
     * @return string
     */
    public function renderUntil($field_name, $showFormEnd = true, $showFields = true)
    {
        if (!$this->has($field_name)) {
            $this->fieldDoesNotExist($field_name);
        }

        $fields = $this->getUnrenderedFields();

        $i = 1;
        foreach ($fields as $key => $value) {
            if ($value->getRealName() == $field_name) {
                break;
            }
            $i++;
        }

        $fields = array_slice($fields, 0, $i-1, true);

        return $this->render([], $fields, false, $showFields, $showFormEnd);
    }
}