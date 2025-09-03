<?php

namespace Database\Seeders;

use App\Models\AcademyCategoryProduct;
use App\Models\SmHumanDepartment;
use App\Models\Tab;
use Illuminate\Database\Seeder;

class TabsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $parent = 1;

        Tab::create([
            'label' => 'Dashboard',
            'url' => '/',
            'icon' => 'mdi:home-outline',
            'parent_id' => null,
            'permission_required' => '',
            'order' => $parent++,
        ]);

        $scheduleIndex = 1;
        $schedule = Tab::create([
            'label' => 'Schedule',
            'url' => null,
            'icon' => 'mdi:calendar-clock',
            'permission_required' => null,
            'order' => $parent++,
        ]);


        $upediaIndex = 1;
        $upedia = Tab::create([
            'label' => 'UPEDIA',
            'url' => null,
            'icon' => 'mdi:book-open-page-variant',
            'permission_required' => null,
            'order' => $parent++,
        ]);

        $settingsIndex = 1;
        $settings = Tab::create([
            'label' => 'Settings',
            'url' => null,
            'icon' => 'mdi:cog-outline',
            'permission_required' => null,
            'order' => $parent++,
        ]);


        $demoIndex = 1;
        $demo = Tab::create([
            'label' => 'Demo',
            'url' => null,
            'icon' => 'mdi:play-circle-outline',
            'permission_required' => null,
            'order' => $parent++,
        ]);

        $scheduleProductsIndex = 1;
        $scheduleProducts = Tab::create([
            'label' => 'Schedule Products',
            'url' => null,
            'icon' => 'mdi:package-variant-closed',
            'permission_required' => null,
            'order' => $parent++,
        ]);


        Tab::create([
            'label' => 'All Products',
            'url' => config('app.Retention_URL') . 'products/all',
            'icon' => 'mdi:view-list',
            'parent_id' => $scheduleProducts->id,
            'permission_required' => 'academy_products_index',
            'order' => $scheduleProductsIndex++,
        ]);

        $scheduleTypes = AcademyCategoryProduct::select('name_en')->pluck('name_en');

        foreach ($scheduleTypes as $type) {
            Tab::create([
                'label' => ucfirst($type),
                'url' => config('app.Retention_URL') . 'products/all?' . http_build_query(['type' => $type]),
                'icon' => 'mdi:tag-outline',
                'parent_id' => $scheduleProducts->id,
                'permission_required' => 'academy_products_index',
                'order' => $scheduleProductsIndex++,
            ]);
        }

        $testerDepartment = SmHumanDepartment::where('slug', strtolower('tester'))->firstOrFail();
        $teacherDepartment = SmHumanDepartment::where('slug', strtolower('teacher'))->firstOrFail();

        Tab::create([
            'label' => 'Testers',
            'url' => config('app.HR_URL') . 'staffs?department_id=' . $testerDepartment->id,
            'icon' => 'mdi:school-outline',
            'parent_id' => null,
            'permission_required' => 'hr_staffs_index',
            'order' => $parent++,
        ]);

        Tab::create([
            'label' => 'Teachers',
            'url' => config('app.HR_URL') . 'staffs?department_id=' . $teacherDepartment->id,
            'icon' => 'mdi:account-check-outline',
            'parent_id' => null,
            'permission_required' => 'hr_staffs_index',
            'order' => $parent++,
        ]);

        Tab::create([
            'label' => 'Students',
            'url' => config('app.Retention_URL') . 'students',
            'icon' => 'mdi:school-outline',
            'parent_id' => null,
            'permission_required' => 'academy_students_index',
            'order' => $parent++,
        ]);
        Tab::create([
            'label' => 'Urgents',
            'url' => config('app.Retention_URL') . 'urgents',
            'icon' => 'mdi:alert-circle-outline',
            'parent_id' => null,
            'permission_required' => 'academy_urgents_index',
            'order' => $parent++,
        ]);



        Tab::create([
            'label' => 'Calendar Demo',
            'url' => config('app.Retention_URL') . 'calendar-demo',
            'icon' => 'mdi:calendar-star',
            'parent_id' => $demo->id,
            'permission_required' => 'academy_calendar_index',
            'order' => $demoIndex++,
        ]);


        Tab::create([
            'label' => 'Demo Schedule',
            'url' => config('app.Retention_URL') . 'schedule-demo',
            'icon' => 'mdi:calendar-check-outline',
            'parent_id' => $demo->id,
            'permission_required' => 'academy_schedule_demo_index',
            'order' => $demoIndex++,
        ]);
        Tab::create([
            'label' => 'View Scheduled Demo',
            'url' => config('app.Retention_URL') . 'show-demo',
            'icon' => 'mdi:eye-outline',
            'parent_id' => $demo->id,
            'permission_required' => 'academy_schedule_demo_show',
            'order' => $demoIndex++,
        ]);

        Tab::create([
            'label' => 'Schedule Calendar',
            'url' => config('app.Retention_URL') . 'calendar',
            'icon' => 'mdi:calendar-month',
            'parent_id' => $schedule->id,
            'permission_required' => 'academy_calendar_index',
            'order' => $scheduleIndex++,
        ]);

        Tab::create([
            'label' => 'Schedule',
            'url' => config('app.Retention_URL') . 'schedule',
            'icon' => 'mdi:calendar-plus',
            'parent_id' => $schedule->id,
            'permission_required' => 'academy_schedule_index',
            'order' => $scheduleIndex++
        ]);
        Tab::create([
            'label' => 'live-sessions',
            'url' => config('app.Retention_URL') . 'live-sessions',
            'icon' => 'mdi:video-outline',
            'parent_id' => $schedule->id,
            'permission_required' => 'academy_live_sessions_index',
            'order' => $scheduleIndex++
        ]);
        Tab::create([
            'label' => 'courses',
            'url' => config('app.Retention_URL') . 'courses',
            'icon' => 'mdi:book-education-outline',
            'parent_id' => $schedule->id,
            'permission_required' => 'academy_courses_index',
            'order' => $scheduleIndex++
        ]);
        Tab::create([
            'label' => 'Sessions',
            'url' => config('app.Retention_URL') . 'sessions',
            'icon' => ' mdi:calendar-multiple',
            'parent_id' => $schedule->id,
            'permission_required' => 'academy_sessions_index',
            'order' => $scheduleIndex++
        ]);



        Tab::create([
            'label' => 'Student Invoices',
            'url' => config('app.FINANCE_URL') . 'student_invoices',
            'icon' => 'mdi:receipt-text-outline',
            'parent_id' => null,
            'permission_required' => 'finance_student_invoices_index',
            'order' => $parent++,
        ]);

        Tab::create([
            'label' => 'Payroll Invoices',
            'url' => config('app.FINANCE_URL') . 'payroll_invoices',
            'icon' => 'mdi:currency-usd',
            'parent_id' => null,
            'permission_required' => 'finance_payroll_invoices_index',
            'order' => $parent++,
        ]);
        Tab::create([
            'label' => 'Expenses',
            'url' => config('app.FINANCE_URL') . 'expenses',
            'icon' => 'mdi:cash-minus',
            'parent_id' => null,
            'permission_required' => 'finance_expenses_index',
            'order' => $parent++,
        ]);
        Tab::create([
            'label' => 'Incomes',
            'url' => config('app.FINANCE_URL') . 'incomes',
            'icon' => 'mdi:cash-plus',
            'parent_id' => null,
            'permission_required' => 'finance_incomes_index',
            'order' => $parent++,
        ]);
        Tab::create([
            'label' => 'Profit & Loss',
            'url' => config('app.FINANCE_URL') . 'profit_loss',
            'icon' => 'mdi:chart-line',
            'parent_id' => null,
            'permission_required' => 'finance_profit_loss_index',
            'order' => $parent++,
        ]);
        Tab::create([
            'label' => 'Promocodes',
            'url' => config('app.FINANCE_URL') . 'promocodes',
            'icon' => 'mdi:ticket-percent-outline',
            'parent_id' => null,
            'permission_required' => 'finance_promocodes_index',
            'order' => $parent++,
        ]);

        Tab::create([
            'label' => 'Employees',
            'url' => config('app.HR_URL') . 'staffs',
            'icon' => 'mdi:account-group-outline',
            'parent_id' => $upedia->id,
            'permission_required' => 'hr_staffs_index',
            'order' => $upediaIndex++,
        ]);
        Tab::create([
            'label' => 'Departments',
            'url' => config('app.HR_URL') . 'departments',
            'icon' => 'mdi:office-building-outline',
            'parent_id' => $upedia->id,
            'permission_required' => 'hr_departments_index',
            'order' => $upediaIndex++,
        ]);
        Tab::create([
            'label' => 'Job Titles',
            'url' => config('app.HR_URL') . 'job_titles',
            'icon' => 'mdi:briefcase-outline',
            'parent_id' => $upedia->id,
            'permission_required' => 'hr_job_titles_index',
            'order' => $upediaIndex++,
        ]);
        Tab::create([
            'label' => 'Payrolls',
            'url' => config('app.HR_URL') . 'payrolls',
            'icon' => 'mdi:currency-usd',
            'parent_id' => $upedia->id,
            'permission_required' => 'hr_payrolls_index',
            'order' => $upediaIndex++,
        ]);
        Tab::create([
            'label' => 'Attendances',
            'url' => config('app.HR_URL') . 'attendences',
            'icon' => 'mdi:calendar-check',
            'parent_id' => null,
            'permission_required' => 'hr_attendences_index',
            'order' => $parent++,
        ]);

        Tab::create([
            'label' => 'Leave Defines',
            'url' => config('app.HR_URL') . 'leave_defines',
            'icon' => 'mdi:calendar-remove',
            'parent_id' => null,
            'permission_required' => 'hr_leave_defines_index',
            'order' => $parent++,
        ]);
        Tab::create([
            'label' => 'Leave Types',
            'url' => config('app.HR_URL') . 'leave_types',
            'icon' => 'mdi:calendar-blank-outline',
            'parent_id' => null,
            'permission_required' => 'hr_leave_types_index',
            'order' => $parent++,
        ]);
        Tab::create([
            'label' => 'Leave Requests',
            'url' => config('app.HR_URL') . 'leave_requests',
            'icon' => 'mdi:calendar-question',
            'parent_id' => null,
            'permission_required' => 'hr_leave_requests_index',
            'order' => $parent++,
        ]);
        Tab::create([
            'label' => 'Hr Letter',
            'url' => config('app.HR_URL') . 'hr_letter',
            'icon' => 'mdi:email-outline',
            'parent_id' => null,
            'permission_required' => 'hr_request_letter_index',
            'order' => $parent++,
        ]);
        Tab::create([
            'label' => 'Requests',
            'url' => config('app.HR_URL') . 'requests',
            'icon' => 'mdi:file-document-outline',
            'parent_id' => null,
            'permission_required' => 'hr_request_letter_index',
            'order' => $parent++,
        ]);
        Tab::create([
            'label' => 'Rules',
            'url' => config('app.HR_URL') . 'roles',
            'icon' => 'mdi:shield-account-outline',
            'parent_id' => null,
            'permission_required' => 'hr_roles_index',
            'order' => $parent++,
        ]);
        Tab::create([
            'label' => 'Roles',
            'url' => config('app.SETTINGS_URL') . 'roles',
            'icon' => 'mdi:shield-account',
            'parent_id' => null,
            'permission_required' => 'general_roles_index',
            'order' => $parent++,
        ]);
        Tab::create([
            'label' => 'Tasks',
            'url' => config('app.HR_URL') . 'tasks',
            'icon' => 'mdi:checkbox-marked-outline',
            'parent_id' => null,
            'permission_required' => 'hr_tasks_index',
            'order' => $parent++,
        ]);
        Tab::create([
            'label' => 'View Shared Center',
            'url' => config('app.HR_URL') . 'share-center-all',
            'icon' => 'mdi:share-variant',
            'parent_id' => null,
            'permission_required' => 'hr_share_center_index',
            'order' => $parent++,
        ]);



        Tab::create([
            'label' => 'Shared Center Folder',
            'url' => config('app.HR_URL') . 'share-center-folders',
            'icon' => 'mdi:folder-multiple-outline',
            'parent_id' => $settings->id,
            'permission_required' => 'hr_share_center_folder_index',
            'order' => $settingsIndex++,
        ]);


        Tab::create([
            'label' => 'Create Shared Center',
            'url' => config('app.HR_URL') . 'share-center',
            'icon' => 'mdi:share-variant',
            'parent_id' => $settings->id,
            'permission_required' => 'hr_share_center_index',
            'order' => $settingsIndex++,
        ]);
        Tab::create([
            'label' => 'Slots Employees',
            'url' => config('app.HR_URL') . 'slot_emp',
            'icon' => 'mdi:account-clock-outline',
            'parent_id' => $settings->id,
            'permission_required' => 'hr_slots_index',
            'order' => $settingsIndex++,
        ]);

        Tab::create([
            'label' => 'Categories',
            'url' => config('app.Retention_URL') . 'categories',
            'icon' => 'mdi:shape-outline',
            'parent_id' => $settings->id,
            'permission_required' => 'academy_categories_index',
            'order' => $settingsIndex++,
        ]);
        Tab::create([
            'label' => 'Track Types',
            'url' => config('app.Retention_URL') . 'track-types',
            'icon' => 'mdi:road-variant',
            'parent_id' => $settings->id,
            'permission_required' => 'academy_track_types_index',
            'order' => $settingsIndex++,
        ]);
        Tab::create([
            'label' => 'Tracks',
            'url' => config('app.Retention_URL') . 'tracks',
            'icon' => 'mdi:map-marker-path',
            'parent_id' => $settings->id,
            'permission_required' => 'academy_tracks_index',
            'order' => $settingsIndex++,
        ]);
        Tab::create([
            'label' => 'Pricing Plan Types',
            'url' => config('app.Retention_URL') . 'pricing_plan_types',
            'icon' => 'mdi:cash-100',
            'parent_id' => $settings->id,
            'permission_required' => 'academy_pricing_plan_types_index',
            'order' => $settingsIndex++,
        ]);
        Tab::create([
            'label' => 'Discount level Plan',
            'url' => config('app.Retention_URL') . 'discount_plans',
            'icon' => 'mdi:percent-outline',
            'parent_id' => $settings->id,
            'permission_required' => 'academy_discount_plans_index',
            'order' => $settingsIndex++,
        ]);
        Tab::create([
            'label' => 'Category Product',
            'url' => config('app.Retention_URL') . 'product_types',
            'icon' => 'mdi:package-variant',
            'parent_id' => $settings->id,
            'permission_required' => 'academy_product_types_index',
            'order' => $settingsIndex++,
        ]);
        Tab::create([
            'label' => 'Products',
            'url' => config('app.Retention_URL') . 'products',
            'icon' => 'mdi:cart-outline',
            'parent_id' => $settings->id,
            'permission_required' => 'academy_products_index',
            'order' => $settingsIndex++,
        ]);


        Tab::create([
            'label' => 'Email Settings',
            'url' => config('app.CRM_URL') . 'email-settings',
            'icon' => 'mdi:email-cog-outline',
            'parent_id' => $settings->id,
            'permission_required' => 'crm_email-settings_index',
            'order' => $settingsIndex++,
        ]);
        Tab::create([
            'label' => 'Social Media Settings',
            'url' => config('app.CRM_URL') . 'settings/index',
            'icon' => 'mdi:email-cog-outline',
            'parent_id' => $settings->id,
            'permission_required' => 'crm_media-settings_index',
            'order' => $settingsIndex++,
        ]);
        Tab::create([
            'label' => 'Referral Settings',
            'url' => config('app.CRM_URL') . 'referralsettings',
            'icon' => 'mdi:email-cog-outline',
            'parent_id' => $settings->id,
            'permission_required' => 'crm_referral-settings_index',
            'order' => $settingsIndex++,
        ]);

        Tab::create([
            'label' => 'Social Media Settings',
            'url' => config('app.CRM_URL') . 'settings',
            'icon' => 'mdi:account-network',
            'parent_id' => $settings->id,
            'permission_required' => 'crm_setting-view',
            'order' => $settingsIndex++,
        ]);
        Tab::create([
            'label' => 'Referral Setting',
            'url' => config('app.CRM_URL') . 'settings',
            'icon' => 'mdi:account-switch',
            'parent_id' => $settings->id,
            'permission_required' => 'crm_referral-view',
            'order' => $settingsIndex++,
        ]);
        Tab::create([
            'label' => 'Referral Activities',
            'url' => config('app.CRM_URL') . 'referralactivities',
            'icon' => 'mdi:account-star-outline',
            'parent_id' => $settings->id,
            'permission_required' => 'crm_referral-view',
            'order' => $settingsIndex++,
        ]);
        Tab::create([
            'label' => 'Not Interested reasons',
            'url' => config('app.CRM_URL') . 'reasons',
            'icon' => 'mdi:close-circle-outline',
            'parent_id' => $settings->id,
            'permission_required' => 'crm_reasons-view',
            'order' => $settingsIndex++,
        ]);
        Tab::create([
            'label' => 'Lead Action',
            'url' => config('app.CRM_URL') . 'leadactions',
            'icon' => 'mdi:play-circle',
            'parent_id' => $settings->id,
            'permission_required' => 'crm_leadactions-view',
            'order' => $settingsIndex++,
        ]);
        Tab::create([
            'label' => 'Lead Workflow',
            'url' => config('app.CRM_URL') . 'workflow',
            'icon' => 'mdi:play-circle',
            'parent_id' => $settings->id,
            'permission_required' => 'crm_leadactions-view',
            'order' => $settingsIndex++,
        ]);
        Tab::create([
            'label' => 'Reminder Settings',
            'url' => config('app.CRM_URL') . 'leads-reminder-settings',
            'icon' => 'mdi:bell-outline',
            'parent_id' => $settings->id,
            'permission_required' => 'crm_workflow-view',
            'order' => $settingsIndex++,
        ]);


        Tab::create([
            'label' => 'Leads',
            'url' => config('app.CRM_URL') . 'leads',
            'icon' => 'mdi:account-search-outline',
            'parent_id' => null,
            'permission_required' => 'crm_leads-view',
            'order' => $parent++,
        ]);

        Tab::create([
            'label' => 'Messanger',
            'url' => config('app.CRM_URL') . 'messangers/index',
            'icon' => 'mdi:chat-outline',
            'parent_id' => null,
            'permission_required' => 'crm_messanger-view',
            'order' => $parent++,
        ]);
        Tab::create([
            'label' => 'Instagram',
            'url' => config('app.CRM_URL') . 'instagram/index',
            'icon' => 'mdi:instagram',
            'parent_id' => null,
            'permission_required' => 'crm_instagram-view',
            'order' => $parent++,
        ]);
    }
}
