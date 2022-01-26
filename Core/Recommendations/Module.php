<?php

namespace Minds\Core\Recommendations;

use Minds\Interfaces\ModuleInterface;

class Module implements ModuleInterface
{
    public array $submodules = [];

    public function onInit()
    {
        (new Provider())->register();
        (new Routes())->register();
    }
}
