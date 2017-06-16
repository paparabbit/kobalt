<?php  namespace Hoppermagic\Kobalt\Facades;

use Illuminate\Support\Facades\Facade;

class KobaltStorage extends Facade {

    public static function getFacadeAccessor()
    {
        return 'Hoppermagic\Kobalt\Classes\KobaltStorage';
    }
}
