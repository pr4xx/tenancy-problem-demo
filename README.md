# Setup

- `git clone`
- `cp .env.example .env`
- `composer install`
- `php artisan key:generate`
- `php artisan migrate --seed`

# Intended behaviour

- `php artisan reset-queue-timestamp`
  - Only to make sure there is no previous timestamp
- `php artisan dispatch-tenant-jobs`
- `php artisan queue:work`

The work command will process 3 jobs, each job dumping info about the current tenant.
This is intended behaviour.

# Problem

- `php artisan queue:restart`
- `php artisan dispatch-tenant-jobs`
- `php artisan queue:work`

The work command will only process one job and then exit. You can run it multiple times, and it will process all 3 jobs.

Reason: when running `queue:restart`, Laravel will set the current timestamp to the `illuminate:queue:restart` cache key.
The queue worker, as soon as at least one job with tenant context has been run, tries to get the current value of that cache key.
But since the `QueueTenancyBootstrapper` never calls `tenancy()->end()` after completing a job (only at the beginning of the next job),
the redis prefix never gets reverted.

You can get intended behaviour by running `php artisan reset-queue-timestamp`. This will simply delete the timestamp and the queue worker
will compare `null` with `null`.

Also: this problem only appears when the queue worker runs jobs which have been dispatched within a tenant context.
Because then, when the job runs, it will switch to the tenant context but not back.

# The configuration

Main problem is probably adding the `cache` connection to `prefixed_connections` in the tenancy config.
I found no way of telling Laravel to use a different cache store for the `illuminate:queue:restart` key, this would prevent this issue.

# Workaround

As stancl mentioned in Discord, a current workaround is to update the if statement in this file:
https://github.com/archtechx/tenancy/blob/8f9c7efa4584007f41048448d0a11f572a1d3239/src/Bootstrappers/QueueTenancyBootstrapper.php#L74
To `($runningTests || static::$forceRefresh)`.
This will trigger running the revert operation after each job, removing the Redis prefix, allowing the worker to fetch the correct value.


