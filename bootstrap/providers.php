<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use Bugsnag\BugsnagLaravel\BugsnagServiceProvider;

return [
    BugsnagServiceProvider::class,
    AppServiceProvider::class,
    AdminPanelProvider::class,
];
