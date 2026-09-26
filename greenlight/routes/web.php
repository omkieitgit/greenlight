<?php
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
use App\Http\Controllers\Auth\RegisterController;
use  App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

$router->get('/', function () use ($router) {
    //return "Invalid Access";
    if (@$_GET['key'] == 'vikas_es') {
        $dir = realpath(base_path());
        echo 'php ' . $dir . '/artisan route list';
        dd(shell_exec('php ' . $dir . '/artisan route:list'));
    } else {
        echo 'Invalid Access';
        return $router->app->version();
    }
});

$router->group(['middleware' => ['throttle:<ip>|1']], function () use ($router) {
    $router->post('auth/login1', ['uses' => 'AuthController@authenticate']);

});
Route::get('/', function () {  
    if(!empty(\Cookie::get('token'))){
        return redirect()->intended('/home'); 
    }
    return view('consumer.auth.login'); 
})->name('login');

Route::get('/home', [HomeController::class, 'index']);
Route::get('/about', [HomeController::class, 'about']);
Route::get('/services', [HomeController::class, 'services']);
Route::get('/properties', [HomeController::class, 'properties']);
Route::get('/blog', [HomeController::class, 'blog']);
Route::get('/contact', [HomeController::class, 'contact']);
Route::get('login', function (Request $request) {
    if(!empty(\Cookie::get('token'))){
        return redirect()->intended('/home'); 
    }
    \Session::put('referer_url',$request->headers->get('referer'));
    return view('consumer.auth.login');

})->name('login');
//Route::post('login', [LoginController::class, 'authenticate'])->name('login');
$router->get('config/member_register', ['uses' => 'PublicController@memberRegisterConfigInfo']);

Route::get('/register',[AuthController::class, 'register'])->name('register');
#Route::post('register', [RegisterController::class, 'register'])->name('register');

$router->group(
    ['middleware' => []], function () use ($router) {
    // ToDo: later make images to go through controller and route to handle access of them.
    ## ToDO: Laravel Nova and Laravel Horizon configure on this server to handle this all things.

   
    ## ToDo: Security issue with PUBLIC api please do something here to make correct
    $router->get('comps', ['uses' => 'CompsController@index']);
    $router->get('compsDetails', ['uses' => 'CompsController@compsDetails']);
    ## Public Controller GET request
    $router->get('roles', ['uses' => 'PublicController@roles']);
    $router->get('config/register', ['uses' => 'PublicController@buyerConfigInfo']);
    $router->get('config/member_register', ['uses' => 'PublicController@memberRegisterConfigInfo']);
    $router->get('invite_email/{from}/{to}', ['uses' => 'PublicController@getInviteEmail']);
    // $router::get('buyittest', function () {
    //     return \Artisan::call('estates:updateBuyItPosition');
    // });
    ## Info APis
    //$router->get('info/faq', ['uses' => 'InfoController@faq']);
    $router->get('info/privacy_policy_html', ['uses' => 'InfoController@privacyPolicy']);
    $router->get('info/return_policy_html', ['uses' => 'InfoController@returnPolicy']);
    $router->get('info/terms_and_condition_html', ['uses' => 'InfoController@termsAndCondition']);
    $router->get('info/terms_and_condition_pdf', ['uses' => 'InfoController@termsAndConditionPDF']);
    $router->get('info/is_agree_html', ['uses' => 'InfoController@isAgree']);
    $router->get('info/is_popup_html', ['uses' => 'InfoController@isPopup']);

    ## Public Controller POST request
    $router->post('auth/login', ['uses' => 'AuthController@authenticate'])->name('auth.login');
    //Route::get('token',['uses' => 'AuthController@getToken']);

    $router->get('test111', ['uses' => 'PublicController@test']);
    $router->post('register', ['uses' => 'PublicController@register']);
    $router->post('register/buyer', ['uses' => 'PublicController@buyerRegister']);
    $router->post('forgot_password', ['uses' => 'PublicController@forgotPassword']);
    $router->post('reset_password', ['uses' => 'PublicController@resetPassword']);
    $router->post('contact_us', ['uses' => 'PublicController@contactUs']);
    //$router->get('config/property', ['uses' => 'ConfigController@propertyConfig']);
    $router->get('quickview/{token}/{property}', ['uses' => 'QuickViewController@public']);
    $router->get('config/county_url', ['uses' => 'ConfigController@countyUrl']);
    $router->get('braintree', ['uses' => 'PublicController@braintreeToken']);
    $router->get('braintree/{user_id}', ['uses' => 'PublicController@braintreeToken']);
    $router->post('braintree', ['uses' => 'PublicController@braintreeSubscription']);

    # Migration api
    $router->get('migrationfirst', ['uses' => 'MigrationController@migrationFirst']);
    $router->get('migrationscript', ['uses' => 'MigrationController@migrationPrimary']);
    $router->get('migrationscript2', ['uses' => 'MigrationController@migrationStepByStep']);
    $router->get('migrationscriptsingle/{houseId}', ['uses' => 'MigrationController@singleInfo']);
    $router->get('migrationlast', ['uses' => 'MigrationController@migrationLast']);
    $router->get('migrationTrusteeScrapeDateOneTime', ['uses' => 'MigrationController@updateSaleDateTrusteeScrapeDate']);
    $router->get('daily_update/{days}', ['uses' => 'MigrationController@dailyUpdate']);

    $router->get('health', function () use ($router) {
        return 'UP';
    });
    $router->get('/btest1', function () use ($router) {
        return view('layouts.show');
    });
    $router->get('/btest2', function () use ($router) {
        return view('layouts.show2');
    });
 

}
);


$router->get('home/propertyExportInfo/{house_id}', ['uses' => 'PropertyExportController@exportPropertInfo']);
Route::get('/document/{documentType}/{documentName}', [HomeController::class, 'viewDocument']);


//$router->get('test',['uses' => 'PublicController@test']);


//Updated for admin page by Varsha
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;

Route::group([
    
    'namespace' => 'Admin',
    'prefix' => 'admin',
    'as' => 'admin.'
], function () {
Route::get('/login'     , [LoginController::class, 'showLoginForm'] )->name('login');
Route::get('/'          , [LoginController::class, 'showLoginForm'] )->name('login');
Route::post('/login'    , [LoginController :: class,'authenticateAdmin'])->name('admin.login');
Route::post('/'         , [LoginController :: class,'authenticateAdmin'])->name('admin.login');
Route::post('/logout'   , [LoginController:: class,'logout'])->name('admin.logout');
//Route::post('/users/{specificuserID}/updateUserRole', [UserController::class, 'updateUserRoleDatabase'])->name('admin.update.userRoleDatabase');
Route::post('/updateUserRole/{specificuserID}', [UserController::class, 'updateUserRoleDatabase'])->name('admin.update.userRoleDatabase');
Route::post('/updateUserStatus/{specificuserID}', [UserController::class, 'updateUserStatusDatabase'])->name('admin.update.userStatusDatabase');


});


/*Aternate to above group with cookie sessions
Route::group(   [
                    'namespace' => 'Admin',
                    'prefix' => 'admin',
                    'as' => 'admin.'
                ], function () 
                {
                        Route::get('/', function () {  

                           // \Cookie::forget('token');
                           \Cookie::queue(\Cookie::forget('cookie_name'));

                            if(!empty(\Cookie::get('token'))){
                                return redirect()->intended('/admin/dashboard'); 
                            }

                            $loginController = new LoginController();
                            return $loginController->showLoginForm();
                            
                        })->name('login');

                        Route::get('/login', function (Request $request) {
                            if(!empty(\Cookie::get('token'))){
                                return redirect()->intended('/admin/users'); 
                            }
                            \Session::put('referer_url',$request->headers->get('referer'));
                            $loginController = new LoginController();
                            return $loginController->showLoginForm();

                        })->name('login');

                }
            );
*/
Route::group([
    'middleware' => 'auth.admin',  
    'namespace' => 'Admin',
    'prefix' => 'admin',
    'as' => 'admin.'
], function () {
    // Routes within this group will have the 'auth' middleware applied,
    // 'Admin' namespace, '/admin' prefix, and route name prefix 'admin.'
    
    Route::get('/dashboard' , [DashboardController::class, 'index'] )->name('dashboard');
    Route::get('/users'     , [UserController::class, 'showAllUsers']      )->name('users.showAllUsers');
    Route::get('/users/{id}', [UserController::class, 'showUser']       )->name('users.showUser');
   
});



// Route::group([
    
//     'namespace' => 'Admin',
//     'prefix' => 'admin',
//     'as' => 'admin.'
// ], function () {
//     // Route::delete('users/{userId}/roles/{roleId}', [UserController::class, 'deleteRoleFromUser'])->name('admin.deleteRole');
//     // Route::post('users/{userId}/roles', [UserController::class, 'addRoleToUser'])->name('admin.addRole');
//     Route::delete('/users', [UserController::class, 'deleteRoleFromUser'])->name('admin.deleteRole');
//     Route::post('/users', [UserController::class, 'addRoleToUser'])->name('admin.addRole');
// });



// Route::prefix('admin')->group(function () {
//     Route::post('users/{userId}/roles', [UserController::class, 'addRoleToUser'])->name('admin.users.addRole');
//    // Route::delete('users/{userId}/roles/{roleId}', [UserController::class, 'deleteRoleFromUser'])->name('admin.users.deleteRole');
//     Route::delete('users/{id}/delete-role', [UserController::class, 'deleteRole'])->name('admin.users.deleteRole');

// });

