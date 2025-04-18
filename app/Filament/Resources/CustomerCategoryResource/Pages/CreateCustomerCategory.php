<?php

namespace App\Filament\Resources\CustomerCategoryResource\Pages;

use App\Filament\Resources\CustomerCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerCategory extends CreateRecord
{
    protected static string $resource = CustomerCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return CustomerCategoryResource::getUrl(); // Redirect ke halaman daftar customer
    }
}
