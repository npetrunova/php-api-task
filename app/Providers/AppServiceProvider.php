<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('email_domain', 'App\Rules\EmailDomain@passes');
        Validator::extend('does_field_exist', 'App\Rules\FieldValidation@checkIfFieldExists');
        Validator::extend('check_value_type', 'App\Rules\FieldValidation@checkIfValueTypeCorrect');
        Validator::extend('check_for_duplicate', 'App\Rules\FieldValidation@checkForFieldDuplicate');
        
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
