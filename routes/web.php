<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* route links */
//use App\Http\Controllers\Api\UserController;
use Spatie\Permission\Models\Permission;
Route::get('/add-signed-template-meta/{id}', function ($id) {
    return add_signed_parameter_in_my_templates($id);
});
Route::get('/fix-ruc-template', function () {
    return fix_ruc_signature_in_my_templates();
});
Route::get('/fix-end-warranty', function () {
    return fix_end_warranty_in_my_templates();
});
Route::get('/add-expiration-option', function () {
    return add_expiration_option_in_my_templates();
});
Route::get('/fix-expiration-option', function () {
    return fix_expiry_text_in_my_templates();
});
Route::get('/add-page-count-in-template', function () {
    return add_show_page_option_in_my_templates();
});
Route::get('/remove-stripe-from-all', function () {
    return removeStripeFromAllCompanies();
});
Route::get('/fix-subscription-status', function () {
    return fixstripeStatusAllCompanies();
});
Route::get('/fix-template-name', function () {
    return fix_template_name();
});
Auth::routes();
Route::group(['middleware' => 'auth'], function() {
    Route::get('/', function () {
        return view('backend.dashboard');
    });
    
    Route::resource('users', UserController::class);
    Route::get('user-data', 'UserController@getUserdata')->name('getUserdata');
    Route::get('/logout', function(){
        \Auth::logout();
        return redirect('/login');
    });
    Route::resource('companies', CompanyController::class);
    
    /* Delete Links */
    Route::post('users/delete', 'UserController@deleteAll')->name('users.batch-delete');  
    Route::post('companies/delete', 'CompanyController@batchDelete')->name('companies.batch-delete');

    /* get and update profile links */
    Route::get('/profile', 'UserController@getProfile');
    Route::put('/update-profile/{id}', 'UserController@updateProfile');

    /* get and update password */
    Route::get('/change-password', 'UserController@changePassword');
    Route::post('/change-password', 'UserController@changePassword');

    /* Subscriptions */
    Route::resource('subscriptions', SubscriptionController::class);
    Route::resource('termsconditions', TermsConditionsController::class);
    Route::resource('activity-type', ActivityTypeController::class);

    Route::resource('roles', RoleController::class);
    Route::group(['prefix' => 'settings'], function(){
        Route::get('/smtp', 'SettingController@getSmtpDetails');
        Route::post('/smtp', 'SettingController@storeSmtpDetails')->name('smtp.store');
        Route::get('/subscription-trial', 'SettingController@getSubscriptionTrialDetails');
        Route::post('/subscription-trial', 'SettingController@storeSubscriptionTrialDetails')->name('subscription-trial.store');
    });
    Route::group(['prefix' => 'company'], function(){
        Route::get('{id}/user/edit/{user_id}', 'CompanyController@editUser');
        Route::put('{id}/user/update/{user_id}', 'CompanyController@updateUser');
        Route::delete('{id}/user/delete/{user_id}', 'CompanyController@deleteUser');
    });
});


Route::get('login', function () { return view('backend.pages.auth.login'); })->name('login');
Route::post('login', 'Auth\LoginController@login');

Route::get('/forgot-password', 'Auth\ForgotPasswordController@getForgetPassword')->name('password.request');

Route::post('/forgot-password', 'Auth\ForgotPasswordController@updateForgetPassword')->name('password.email');

Route::get('/reset-password/{token}', 'Auth\ResetPasswordController@getResetPassword')->name('password.reset');

Route::post('/reset-password', 'Auth\ResetPasswordController@updateResetpassword')->name('password.update');

Route::get('assign-roles', function(){
    (new UserController())->setConfig(5);
    foreach(Permission::get() as $permission){
        $permission->assignRole(4);
    }
});

/* Oringinal theme links */
Route::get('/dev', function () {
    return view('dashboard');
});

Route::group(['prefix' => 'email'], function(){
    Route::get('inbox', function () { return view('pages.email.inbox'); });
    Route::get('read', function () { return view('pages.email.read'); });
    Route::get('compose', function () { return view('pages.email.compose'); });
});

Route::group(['prefix' => 'apps'], function(){
    Route::get('chat', function () { return view('pages.apps.chat'); });
    Route::get('calendar', function () { return view('pages.apps.calendar'); });
});

Route::group(['prefix' => 'ui-components'], function(){
    Route::get('accordion', function () { return view('pages.ui-components.accordion'); });
    Route::get('alerts', function () { return view('pages.ui-components.alerts'); });
    Route::get('badges', function () { return view('pages.ui-components.badges'); });
    Route::get('breadcrumbs', function () { return view('pages.ui-components.breadcrumbs'); });
    Route::get('buttons', function () { return view('pages.ui-components.buttons'); });
    Route::get('button-group', function () { return view('pages.ui-components.button-group'); });
    Route::get('cards', function () { return view('pages.ui-components.cards'); });
    Route::get('carousel', function () { return view('pages.ui-components.carousel'); });
    Route::get('collapse', function () { return view('pages.ui-components.collapse'); });
    Route::get('dropdowns', function () { return view('pages.ui-components.dropdowns'); });
    Route::get('list-group', function () { return view('pages.ui-components.list-group'); });
    Route::get('media-object', function () { return view('pages.ui-components.media-object'); });
    Route::get('modal', function () { return view('pages.ui-components.modal'); });
    Route::get('navs', function () { return view('pages.ui-components.navs'); });
    Route::get('navbar', function () { return view('pages.ui-components.navbar'); });
    Route::get('pagination', function () { return view('pages.ui-components.pagination'); });
    Route::get('popovers', function () { return view('pages.ui-components.popovers'); });
    Route::get('progress', function () { return view('pages.ui-components.progress'); });
    Route::get('scrollbar', function () { return view('pages.ui-components.scrollbar'); });
    Route::get('scrollspy', function () { return view('pages.ui-components.scrollspy'); });
    Route::get('spinners', function () { return view('pages.ui-components.spinners'); });
    Route::get('tabs', function () { return view('pages.ui-components.tabs'); });
    Route::get('tooltips', function () { return view('pages.ui-components.tooltips'); });
});

Route::group(['prefix' => 'advanced-ui'], function(){
    Route::get('cropper', function () { return view('pages.advanced-ui.cropper'); });
    Route::get('owl-carousel', function () { return view('pages.advanced-ui.owl-carousel'); });
    Route::get('sweet-alert', function () { return view('pages.advanced-ui.sweet-alert'); });
});

Route::group(['prefix' => 'forms'], function(){
    Route::get('basic-elements', function () { return view('pages.forms.basic-elements'); });
    Route::get('advanced-elements', function () { return view('pages.forms.advanced-elements'); });
    Route::get('editors', function () { return view('pages.forms.editors'); });
    Route::get('wizard', function () { return view('pages.forms.wizard'); });
});

Route::group(['prefix' => 'charts'], function(){
    Route::get('apex', function () { return view('pages.charts.apex'); });
    Route::get('chartjs', function () { return view('pages.charts.chartjs'); });
    Route::get('flot', function () { return view('pages.charts.flot'); });
    Route::get('morrisjs', function () { return view('pages.charts.morrisjs'); });
    Route::get('peity', function () { return view('pages.charts.peity'); });
    Route::get('sparkline', function () { return view('pages.charts.sparkline'); });
});

Route::group(['prefix' => 'tables'], function(){
    Route::get('basic-tables', function () { return view('pages.tables.basic-tables'); });
    Route::get('data-table', function () { return view('pages.tables.data-table'); });
});

Route::group(['prefix' => 'icons'], function(){
    Route::get('feather-icons', function () { return view('pages.icons.feather-icons'); });
    Route::get('flag-icons', function () { return view('pages.icons.flag-icons'); });
    Route::get('mdi-icons', function () { return view('pages.icons.mdi-icons'); });
});

Route::group(['prefix' => 'general'], function(){
    Route::get('blank-page', function () { return view('pages.general.blank-page'); });
    Route::get('faq', function () { return view('pages.general.faq'); });
    Route::get('invoice', function () { return view('pages.general.invoice'); });
    Route::get('profile', function () { return view('pages.general.profile'); });
    Route::get('pricing', function () { return view('pages.general.pricing'); });
    Route::get('timeline', function () { return view('pages.general.timeline'); });
});

Route::group(['prefix' => 'auth'], function(){
    Route::get('login', function () { return view('pages.auth.login'); });
    Route::get('register', function () { return view('pages.auth.register'); });
});

Route::group(['prefix' => 'error'], function(){
    Route::get('404', function () { return view('pages.error.404'); });
    Route::get('500', function () { return view('pages.error.500'); });
});

Route::get('clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});
Route::get('generate-reference', function(){
    generateReferences(474);
});

// 404 for undefined routes
Route::any('/{page?}',function(){
    return View::make('pages.error.404');
})->where('page','.*');
