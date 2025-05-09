<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

/*
Route::get('/', function () {
    return view('welcome');
});
*/


Route::get('/', function() {
    return view('/USER/splash');
});

Route::get('/signup', [
    UserController::class,
    'SignUp'
])->name('signup');

Route::get('/signin', [
    UserController::class,
    'SignIn'
])->name('signin');

Route::post('/status', [
    UserController::class,
    'Status'
])->name('status');

Route::get('/dashboard', [
    UserController::class,
    'Dashboard'
])->name('dashboard');

Route::get('/dashboard/data', [UserController::class, 'getDashboardData']);

Route::get('/users', [
    UserController::class,
    'Users'
])->name('users');

Route::get('/message', [
    UserController::class,
    'Message'
])->name('message');

Route::get('/message/detail', [
    UserController::class,
    'DetailMessage'
])->name('message.detail');

Route::get('/google', [
    UserController::class,
    'GoogleList'
])->name('google');

Route::get('/redirect-database',[
    UserController::class,
    'RedirectDatabase'
])->name('redirect.database');

Route::post('/database', [
    UserController::class,
    'Database'
])->name('database');

Route::post('/submit-sql', [
    UserController::class,
    'SubmitSQL'
])->name('submit.sql');

Route::post('/transportation/dashboard', [
    UserController::class,
    'TransportationDashboard'
])->name('transportation.dashboard');

Route::post('/monitoring', [
    UserController::class,
    'Monitoring'
])->name('monitoring');

Route::get('/monitoring/data/{companyname}/', [
    UserController::class,
    'getMonitoringData'
])->name('monitoring.data');

Route::get('/monitoring/detail/{nik}/{companyname}/', [
    UserController::class,
    'DetailMonitoring'
])->name('monitoring.detail');

Route::get('/truncate', [
    UserController::class,
    'Truncate'
])->name('truncate');

Route::post('/products', [
    UserController::class,
    'Products'
])->name('products');

Route::match(['get', 'post'], '/carts', [
    UserController::class,
    'Carts'
])->name('carts');

Route::post('/checkout', [
    UserController::class,
    'Checkout'
])->name('checkout');

Route::get('/payment/method', [
    UserController::class,
    'PaymentMethod'
])->name('payment.method');

Route::post('/payment/method/store', [
    UserController::class,
    'PaymentMethodStore'
])->name('payment.method.store');

/*
Route::post('/maintenances', [
    UserController::class,
    'Maintenances'
])->name('maintenances');

Route::get('/maintenancedetail', [
    UserController::class,
    'DetailMaintenance'
])->name('maintenancedetail');
*/

Route::match(['get','post'], '/maintenances', [
    UserController::class,
    'Maintenances'
])->name('maintenances');

Route::post('/maintenances/request', [
    UserController::class,
    'MaintenancesRequest'
])->name('maintenances.request');

Route::get('/maintenances/request/detail', [
    UserController::class,
    'MaintenancesRequestDetail'
])->name('maintenances.request.detail');

Route::post('/history', [
    UserController::class,
    'History'
])->name('history');

Route::post('/history/delete', [
    UserController::class,
    'Delete'
])->name('history.delete');

Route::match(['get', 'post'], '/settings', [
    UserController::class, 
    'Settings'
])->name('settings');

Route::get('/live-k', [UserController::class, 'getLiveKData']);
Route::get('/rekap-k1', [UserController::class, 'getRekapK1Data']);
Route::get('/rekap-k2', [UserController::class, 'getRekapK2Data']);

// Admin Path

Route::get('/admin', function() {
    return view('/ADMIN/splash');
});

Route::get('/admin/signup', [
    AdminController::class,
    'SignUp'
])->name('admin.signup');

Route::get('/admin/signin', [
    AdminController::class,
    'SignIn'
])->name('admin.signin');

Route::post('/admin/status', [
    AdminController::class,
    'Status'
])->name('admin.status');

Route::get('/admin/dashboard', [
    AdminController::class,
    'Dashboard'
])->name('admin.dashboard');

Route::get('/admin/message', [
    AdminController::class,
    'Message'
])->name('admin.message');

Route::get('/admin/message/detail', [
    AdminController::class,
    'DetailMessage'
])->name('admin.message.detail');

Route::match(['get','post'], '/admin/user-management',[
    AdminController::class,
    'UserManager'
])->name('admin.user-manager');

Route::match(['get','post'], '/admin/file-manager',[
    AdminController::class,
    'FileManager'
])->name('admin.file-manager');

Route::match(['get', 'post'], '/admin/products-manager', [
    AdminController::class,
    'ProductsManager'
])->name('admin.products-manager');

Route::post('/admin/add-products', [
    AdminController::class,
    'AddProduct'
])->name('admin.add-products');

Route::post('/admin/edit-products', [
    AdminController::class,
    'EditProduct'
])->name('admin.edit-products');

Route::post('/admin/save-product', [
    AdminController::class,
    'SaveProduct'
])->name('admin.save-product');

Route::match(['get','post'], '/admin/maintenances', [
    AdminController::class,
    'Maintenances'
])->name('admin.maintenances');

Route::post('/admin/maintenances/request', [
    AdminController::class,
    'MaintenancesRequest'
])->name('admin.maintenances.request');

Route::get('/admin/maintenances/request/detail', [
    AdminController::class,
    'MaintenancesRequestDetail'
])->name('admin.maintenances.request.detail');

Route::post('/admin/history-tracker', [
    AdminController::class,
    'HistoryTracker'
])->name('admin.history-tracker');

Route::post('/admin/settings', [
    AdminController::class,
    'Settings'
])->name('admin.settings');

Route::post('/admin/maintenances', [
    AdminController::class,
    'Maintenances'
])->name('admin.maintenances');
/*
Route::post('/toggle-file', function (Illuminate\Http\Request $request) {
    $filename = $request->input('filename');
    $action = $request->input('action');
    $filePath = '/home/qibiujnz/public_html/api/' . $filename;
    
    if (!File::exists($filePath) || pathinfo($filePath, PATHINFO_EXTENSION) !== 'php') {
        return response()->json(['success' => false, 'message' => 'Invalid file.']);
    }

    if ($action === 'start') {
        ob_start(); 
        include $filePath; 
        $output = ob_get_clean(); 

        return response()->json(['success' => true, 'message' => 'Execution started.', 'output' => $output]);
    } elseif ($action === 'stop') {
        if (ob_get_level() > 0) {
            ob_end_clean(); // Menghentikan dan membersihkan buffer
        }

        if (file_exists($filePath)) {
            return response()->json(['success' => true, 'message' => 'Execution stopped.']);
        } 
            return response()->json(['success' => true, 'message' => 'Execution stopped.']);
        }
    return response()->json(['success' => false, 'message' => 'Invalid action.']);
});*/

Route::post('/toggle-file', [
    AdminController::class,
    'ToggleFile'
])->name('admin.toggle-file');

Route::get('/admin/file/{filename}', [
    AdminController::class, 
    'showFileContent'
])->name('admin.file-content');

Route::post('/admin/file/{filename}/save', [
    AdminController::class, 
    'saveFileContent'
])->name('admin.file-save');




