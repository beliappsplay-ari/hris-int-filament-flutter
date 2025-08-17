<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)
            ->setPermissionClass(Permission::class)
            ->setRoleClass(Role::class);

        Blade::directive('currency', function ( $expression ) { return "<?php echo number_format($expression,0,',','.'); ?>"; });
         Blade::directive('currency', function ($amount) {
        return "<?php echo 'Rp ' . number_format($amount ?? 0, 0, ',', '.'); ?>";
         });
    }
}
