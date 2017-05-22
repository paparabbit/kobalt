<?php  namespace Hoppermagic\Kobalt\Facades;

use Illuminate\Support\Facades\Facade;

class ImageHelper extends Facade {

    public static function getFacadeAccessor()
    {
        return 'Hoppermagic\Kobalt\Helpers\ImageHelper';
    }
}
