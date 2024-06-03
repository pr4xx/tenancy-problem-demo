<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('dispatch-tenant-jobs', function () {
    $this->info('Dispatching a job for every tenant:');
    foreach (\Stancl\Tenancy\Database\Models\Tenant::get() as $tenant) {
        $tenant->run(function() {
            dispatch(new \App\Jobs\TenantContextJob());
        });
        $this->info('Dispatched ' . $tenant->id);
    }
});

Artisan::command('reset-queue-timestamp', function () {
    cache()->delete('illuminate:queue:restart');
});
