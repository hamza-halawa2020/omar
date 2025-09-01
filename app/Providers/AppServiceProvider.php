<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use App\Models\Tab;
use App\Models\SmHumanDepartment;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
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
        Model::unguard();

        Paginator::useBootstrapFive();
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

      //  dd(auth()->check());
        View::composer('*', function ($view) {
            if(auth()->check()){
        
                $department = auth()->user()->staff->department;


                $user = Auth::user();


                $tabs = Tab::whereHas('departments', function ($query) use ($user, $department) {
                    $query->where('sm_human_departments.id', $department->id);
                })
                ->with(['children' => function ($q) use ($user, $department) {
                    $q->whereHas('departments', function ($query) use ($user, $department) {
                        $query->where('sm_human_departments.id', $department->id);
                    });
                }])
                ->orderBy('order')
                ->get();

     
                $tabs = $tabs->filter(fn($tab) => !$tab->permission_required || $user->can($tab->permission_required))
                            ->map(function ($tab) use ($user) {
                                $tab->children = $tab->children->filter(fn($child) => !$child->permission_required || $user->can($child->permission_required));
                                return $tab;
                            });

                session(['menuTabs' => $tabs]);
         
            }   
       
         }); 
    }
}
