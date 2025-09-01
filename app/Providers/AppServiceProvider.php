<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;

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

        
     
         $host = Request::getHost();
         
             if (str_contains($host, 'alkarim')) {
                Config::set('app.client_name', 'Alkarim');
                   Config::set('app.client_name', 'Alkarim');
               config::set('app.general_link', 'https://alkarim-setting.thetailors.tech/systems') ;
               
                config::set('app.SETTINGS_URL', 'https://alkarim-setting.thetailors.tech/') ;
                config::set('app.ACADEMY_URL', 'https://alkarim-academic.thetailors.tech/') ;
                config::set('app.HR_URL', 'https://alkarim-hr.thetailors.tech/') ;
                config::set('app.FINANCE_URL', 'https://alkarim-finance.thetailors.tech/') ;
                config::set('app.CRM_URL', 'https://alkarim-crm.thetailors.tech/') ;
                config::set('app.Retention_URL', 'https://alkarim-retention.thetailors.tech/') ;

   
            } elseif (str_contains($host, 'upedia')) {
                Config::set('app.client_name', 'Upedia');
                 config::set('app.general_link', 'https://upedia-setting.thetailors.tech/systems') ;
                 
                config::set('app.SETTINGS_URL', 'https://upedia-setting.thetailors.tech/') ;
                config::set('app.ACADEMY_URL', 'https://upedia-academic.thetailors.tech/') ;
                config::set('app.HR_URL', 'https://upedia-hr.thetailors.tech/') ;
                config::set('app.FINANCE_URL', 'https://upedia-finance.thetailors.tech/') ;
                config::set('app.CRM_URL', 'https://upedia-crm.thetailors.tech/') ;
                config::set('app.Retention_URL', 'https://upedia-retention.thetailors.tech/') ;
                
            }else {
                Config::set('app.client_name', 'tailors');
                 config::set('app.general_link', 'https://tailors-setting.thetailors.tech/systems') ;
                config::set('app.SETTINGS_URL', 'https://tailors-setting.thetailors.tech/') ;
                config::set('app.ACADEMY_URL', 'https://tailors-academic.thetailors.tech/') ;
                config::set('app.HR_URL', 'https://tailors-hr.thetailors.tech/') ;
                config::set('app.FINANCE_URL', 'https://tailors-finance.thetailors.tech/') ;
                config::set('app.CRM_URL', 'https://tailors-crm.thetailors.tech/') ;
                config::set('app.Retention_URL', 'https://tailors-retention.thetailors.tech/') ;
            }
            
      
            


 
        View::composer('*', function ($view) {
            if(auth()->check()){
                if (!Session::has('menuTabs')) {
                      
        
        
                    $user = Auth::user();
                    $department = $user->staff->department;
    
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
            }
       
         }); 

    }
}
