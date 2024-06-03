<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('dispatch-tenant-jobs', function () {
    $this->info('Dispatching a job for every tenant:');
    foreach (\Stancl\Tenancy\Database\Models\Tenant::get() as $tenant) {
        $tenant->run(function() {
            dispatch(new \App\Jobs\TenantContextJob());
        });
        $this->info('Dispatched ' . $tenant->id);
    }
})->purpose('Display an inspiring quote')->hourly();
