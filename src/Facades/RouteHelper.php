<?php  namespace Hoppermagic\Kobalt\Facades;

use Illuminate\Support\Facades\Facade;

class RouteHelper extends Facade {

    public static function getFacadeAccessor()
    {
        return 'Hoppermagic\Kobalt\Helpers\RouteHelper';
    }
}
