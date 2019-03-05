<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
     
    $api->group(['prefix' => 'auth'], function(Router $api) {

        $api->post('signup', 'App\\Api\\V1\\Controllers\\Authentication\\SignUpController@signUp');

        $api->post('login', 'App\\Api\\V1\\Controllers\\Authentication\\LoginController@login');

        $api->get('logout', 'App\\Api\\V1\\Controllers\\Authentication\\LogoutController@logout');

        $api->post('recovery', 'App\\Api\\V1\\Controllers\\Authentication\\ForgotPasswordController@sendResetEmail');
        $api->post('reset', 'App\\Api\\V1\\Controllers\\Authentication\\ResetPasswordController@resetPassword');
        $api->get('refresh_token', 'App\\Api\\V1\\Controllers\\Authentication\\LoginController@refresh_token' );
    });

    $api->group(['middleware' => 'jwt.auth'], function(Router $api) {
        $api->get('protected', function() {
            return response()->json([
                'message' => 'Access to protected resources granted! You are seeing this text as you provided the token correctly.'
                ]);
        });

        $api->get('refresh', ['middleware' => 'jwt.refresh',
            function() {
                return response()->json([
                    'message' => 'By accessing this endpoint, you can refresh your access token at each request. Check out this response headers!'
                    ]);
            }
            ]);
    });

    $api->get('hello', function() {
        return response()->json([
            'message' => 'This is a simple example of item returned by your APIs. Everyone can see it.'
            ]);
    });

    $api->group( ['prefix'=> 'admin', 'middleware'=> 'jwt.auth'], function (Router $api) {

        $api->get('app_version','App\\Api\\V1\\Controllers\\Masters\\VersionController@get_version');
            /* Companies table*/
            $api->post('company', 'App\\Api\\V1\\Controllers\\Masters\\CompanyController@store');
            $api->post('company_other_details','App\\Api\\V1\\Controllers\\Masters\\CompanyController@storeOtherDetails');
            $api->post('setCompany/{id}','App\\Api\\V1\\Controllers\\Masters\\CompanyController@setCompany');
            $api->post('company_wizard','App\\Api\\V1\\Controllers\\Masters\\CompanyController@createCompanyWizard'); //Create Companies from Wizard
            $api->get('company_list','App\\Api\\V1\\Controllers\\Masters\\CompanyController@full_list');

            //Chart of Accounts
            $api->post('coa','App\\Api\\V1\\Controllers\\Masters\\ChartAccountsMaster@form');
            $api->get('coa','App\\Api\\V1\\Controllers\\Masters\\ChartAccountsMaster@index');
            $api->get('coa_full_list','App\\Api\\V1\\Controllers\\Masters\\ChartAccountsMaster@full_list');
            $api->get('coa/{id}','App\\Api\\V1\\Controllers\\Masters\\ChartAccountsMaster@show');

            //Godown
            $api->post('godown','App\\Api\\V1\\Controllers\\Masters\\GodownMasterController@form');
            $api->get('godown','App\\Api\\V1\\Controllers\\Masters\\GodownMasterController@index');
            $api->get('godown_full_list','App\\Api\\V1\\Controllers\\Masters\\GodownMasterController@full_list');

            //Bank
            $api->post('bank','App\\Api\\V1\\Controllers\\Masters\\BankMasterController@form');
            $api->get('bank','App\\Api\\V1\\Controllers\\Masters\\BankMasterController@index');
            $api->get('bank_full_list','App\\Api\\V1\\Controllers\\Masters\\BankMasterController@full_list');
            $api->get('bank/{id}','App\\Api\\V1\\Controllers\\Masters\\BankMasterController@show');

            //Branch
            $api->post('branch','App\\Api\\V1\\Controllers\\Masters\\BranchController@form');
            $api->get('branch','App\\Api\\V1\\Controllers\\Masters\\BranchController@index');
            $api->get('branch_full_list','App\\Api\\V1\\Controllers\\Masters\\BranchController@full_list');
            $api->get('branch/{id}','App\\Api\\V1\\Controllers\\Masters\\BranchController@show'); 
           
            //Unit Of Measurement
            $api->post('uom','App\\Api\\V1\\Controllers\\Masters\\UnitofMeasurementController@form');
            $api->get('uom','App\\Api\\V1\\Controllers\\Masters\\UnitofMeasurementController@index');       
            $api->get('uom_full_list','App\\Api\\V1\\Controllers\\Masters\\UnitofMeasurementController@full_list');
            $api->get('uom/{id}','App\\Api\\V1\\Controllers\\Masters\\UnitofMeasurementController@show'); 
            
            //Store Raw Products
            $api->post('raw_product','App\\Api\\V1\\Controllers\\Masters\\RawProductController@form');
            $api->get('raw_product','App\\Api\\V1\\Controllers\\Masters\\RawProductController@index');
            $api->get('raw_product_full_list','App\\Api\\V1\\Controllers\\Masters\\RawProductController@full_list');
            $api->get('raw_product/{id}','App\\Api\\V1\\Controllers\\Masters\\RawProductController@show');
            $api->get('raw_product_custom_list','App\\Api\\V1\\Controllers\\Masters\\RawProductController@getCustomProductsList');

            /* Attachment table*/
            $api->get('attachment', 'App\\Api\\V1\\Controllers\\AttachmentController@index');
            $api->post('attachment', 'App\\Api\\V1\\Controllers\\AttachmentController@store');
            $api->get('attachment/{id}', 'App\\Api\\V1\\Controllers\\AttachmentController@show');
            $api->delete('attachment/{id}', 'App\\Api\\V1\\Controllers\\AttachmentController@destroy');

            /* Taxes */
            $api->get('tax_full_list','App\\Api\\V1\\Controllers\\Masters\\TaxController@full_list');

            /*Product Categories*/
            $api->post('product_category','App\\Api\\V1\\Controllers\\Masters\\ProductCategoryController@form');
            $api->get('product_category','App\\Api\\V1\\Controllers\\Masters\\ProductCategoryController@index');
            $api->get('product_category_full_list','App\\Api\\V1\\Controllers\\Masters\\ProductCategoryController@full_list');
            $api->get('product_category/{id}','App\\Api\\V1\\Controllers\\Masters\\ProductCategoryController@show');
            /* Company Operations */
            
            //BOM
            $api->post('bom','App\\Api\\V1\\Controllers\\Masters\\BillOfMaterialMasterController@form');

            //CRM Features
            $api->group( ['prefix'=> 'crm', 'middleware'=> 'jwt.auth'], function (Router $api) {
                // Leads
                $api->post('leads','App\\Api\\V1\\Controllers\\CRM\\LeadController@form');
                $api->get('leads','App\\Api\\V1\\Controllers\\CRM\\LeadController@index');
                $api->get('leads_full_list','App\\Api\\V1\\Controllers\\CRM\\LeadController@full_list');
                $api->get('leads/{id}','App\\Api\\V1\\Controllers\\CRM\\LeadController@show');
                $api->get('lead_status_full_list','App\\Api\\V1\\Controllers\\Masters\\LeadStatusMasterController@full_list');
                // Appointment
                $api->post('appointment','App\\Api\\V1\\Controllers\\CRM\\AppointmentController@form');
                $api->get('appointment','App\\Api\\V1\\Controllers\\CRM\\AppointmentController@index');
                $api->get('appointment_full_list','App\\Api\\V1\\Controllers\\CRM\\AppointmentController@full_list');
                $api->get('appointment/{id}','App\\Api\\V1\\Controllers\\CRM\\AppointmentController@show');

                // Account
                $api->post('account','App\\Api\\V1\\Controllers\\CRM\\AccountController@form');
                $api->get('account','App\\Api\\V1\\Controllers\\CRM\\AccountController@index');
                $api->get('account_full_list','App\\Api\\V1\\Controllers\\CRM\\AccountController@full_list');
                $api->get('account/{id}','App\\Api\\V1\\Controllers\\CRM\\AccountController@show');

                //Contact
                $api->post('contact','App\\Api\\V1\\Controllers\\CRM\\ContactController@form');
                $api->get('contact','App\\Api\\V1\\Controllers\\CRM\\ContactController@index');
                $api->get('contact_full_list','App\\Api\\V1\\Controllers\\CRM\\ContactController@full_list');
                $api->get('contact/{id}','App\\Api\\V1\\Controllers\\CRM\\ContactController@show');

                //Quotation
                $api->post('quotation','App\\Api\\V1\\Controllers\\CRM\\QuotationController@form');
                $api->get('quotation_full_list','App\\Api\\V1\\Controllers\\CRM\\QuotationController@full_list');

                //Task
                $api->post('task','App\\Api\\V1\\Controllers\\CRM\\TaskController@form');
                $api->get('task','App\\Api\\V1\\Controllers\\CRM\\TaskController@index');
                $api->get('task_full_list','App\\Api\\V1\\Controllers\\CRM\\TaskController@full_list');
                $api->get('task/{id}','App\\Api\\V1\\Controllers\\CRM\\TaskController@show');

                //Deal
                $api->post('deal','App\\Api\\V1\\Controllers\\CRM\\DealController@form');
                $api->get('deal','App\\Api\\V1\\Controllers\\CRM\\DealController@index');
                $api->get('deal_full_list','App\\Api\\V1\\Controllers\\CRM\\DealController@full_list');
                $api->get('deal/{id}','App\\Api\\V1\\Controllers\\CRM\\DealController@show');
            });

        });

    $api->group( ['prefix'=> 'open'], function (Router $api) {
        $api->get('setting_by_option/app_version', 'App\\Api\\V1\\Controllers\\Authentication\\SettingController@app_version');
    });

});



?>