<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    public function content(Schema $schema): Schema
    {
        $user = auth()->user();

        if ($user && ! $user->isAdmin() && $user->albums()->doesntExist()) {
            return $schema->components([
                View::make('filament.pages.no-access-notice'),
            ]);
        }

        return parent::content($schema);
    }
}
