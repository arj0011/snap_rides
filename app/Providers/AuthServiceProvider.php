<?php

namespace App\Providers;

use App\Booking;
use App\Category;
use App\Customer;
use App\Driver;
use App\Permission;
use App\Plan;
use App\Role;
use App\Setting;
use App\User;
use App\Notification;
use App\Faq;
use App\Sms;
use App\OfferCode;
use App\Payment;

use App\Policies\BookingPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\DriverPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\PlanPolicy;
use App\Policies\RiderPolicy;
use App\Policies\RolePolicy;
use App\Policies\SettingPolicy;
use App\Policies\UserPolicy;
use App\Policies\NotificationPolicy;
use App\Policies\FaqPolicy;
use App\Policies\SmsPolicy;
use App\Policies\OfferPolicy;
use App\Policies\PaymentPolicy;


use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
         Booking::class => BookingPolicy::class,
         Category::class => CategoryPolicy::class,
         Customer::class => RiderPolicy::class,
         Driver::class => DriverPolicy::class,
         Permission::class => PermissionPolicy::class,
         Plan::class => PlanPolicy::class,
         Role::class => RolePolicy::class,
         Setting::class => SettingPolicy::class,
         User::class => UserPolicy::class,
           Notification::class => NotificationPolicy::class,
         Faq::class => FaqPolicy::class,
         Sms::class => SmsPolicy::class,
         OfferCode::class => OfferPolicy::class,
         Payment::class => PaymentPolicy::class,
    ];


    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}

