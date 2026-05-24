<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_site')
                ->label('View Site')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(route('gallery'))
                ->openUrlInNewTab()
                ->color('gray'),
        ];
    }

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
