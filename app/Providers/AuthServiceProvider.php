<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

use Spatie\Permission\Models\Role;
use App\Policies\RolePolicy;
use App\Model\Pricelist;
use App\Policies\PricelistPolicy;
use App\Model\Warehouse;
use App\Policies\WarehousePolicy;
use App\Model\Taxonomy;
use App\Policies\TaxonomyPolicy;
use App\Model\Category;
use App\Policies\CategoryPolicy;
use App\Model\Product;
use App\Policies\ProductPolicy;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Role::class => RolePolicy::class,
        Pricelist::class => PricelistPolicy::class,
        Warehouse::class => WarehousePolicy::class,
        Taxonomy::class => TaxonomyPolicy::class,
        Category::class => CategoryPolicy::class,
        Product::class => ProductPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        //Gate::before(function ($user, $ability) {
        //    return $user->hasRole('Super Admin') ? true : null;
        //});
    }
}
