<?php

namespace ZaxCMS\Modules\WelcomePage;

interface IWelcomePageModuleFactory {

    /** @return WelcomePageModuleControl */
    public function create();

}
