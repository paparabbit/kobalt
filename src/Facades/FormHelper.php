<?php  namespace Hoppermagic\Kobalt\Facades;

use Illuminate\Support\Facades\Facade;

class FormHelper extends Facade {

    public static function getFacadeAccessor()
    {
        return 'Hoppermagic\Kobalt\Helpers\FormHelper';
    }
}
