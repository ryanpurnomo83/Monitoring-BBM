<?php

namespace App\Http\Controllers;
use App\Models\Monitoring;
use App\Models\User;
use App\Models\Records;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Snap;
use Google\Cloud\FirestoreClient;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use GuzzleHttp\Client;
require_once app_path('Libraries/midtrans/Midtrans.php');

class UserController extends Controller
{
    public function Handle()
    {
        $currentUrl = url()->current();
        $pageHistory = Session::get('page_history', []);
        $pageHistory[] = $currentUrl;
        if (count($pageHistory) > 10) {
            $pageHistory = array_slice($pageHistory, -10);
        }
        Session::put('page_history', $pageHistory);
    }
    
    public function SignUp()
    {   
        $data = 0;
        return view('USER/check-status', ['data' => $data]);
    }

    public function SignIn()
    {
        $data = 1;
        return view('USER/check-status', ['data' => $data]);
    }

    public function SignUpSave(Request $request)
    {   
        try{
            $user = new User;
            $user->companyname = $request->input('companyname');
            $user->name = $request->input('username');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('psw'));
            $user->last_active = now();
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            if ($latitude && $longitude) {
                $user->location = json_encode(['latitude' => $latitude, 'longitude' => $longitude]);
            }
            $user->save();
            $request->session()->flash('status', 'success');
            $request->session()->flash('message', 'Sign Up Successful! Welcome.');
            return redirect()->route('signup');
        }catch(\Exception $e){
            $request->session()->flash('status', 'error');
            $request->session()->flash('message', 'Sign Up Failed! Please try again.');
            session(['user_id' => $user->id]);
            return redirect()->route('signup');
        }
        /*
        $request->session()->put('user_id', $user->id);
        return redirect()->route('dashboard');
        */
    }

    public function SignInChecker(Request $request)
    {
        $user = new User;
        $email = $request->input('email');
        $password = $request->input('psw');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        
        $user = User::where('email', $email)->first();
        if($user && Hash::check($password, $user->password)){
            $request->session()->flash('status', 'success');
            $request->session()->flash('message', 'Sign In Successful!');
            session(['user_id' => $user->id]);
            $user->last_active = now();
            if ($latitude && $longitude) {
                $user->location = json_encode(['latitude' => $latitude, 'longitude' => $longitude]);
            }
            $user->save();
            return redirect()->route('signin');
            /*
            session(['user_id' => $user->id]);
            return redirect()->route('dashboard');
            */
        }else{
            $request->session()->flash('status', 'error');
            $request->session()->flash('message', 'Invalid Email or Password!');
            return redirect()->route('signin');
            //return back()->withErrors(['login' => "Invalid Email or password"]);
        }
    }

    public function Status(Request $request)
    {
        $status = $request->input('status');
        if($status == '0'){
            return $this->SignUpSave($request);
        }else{
            return $this->SignInChecker($request);
        }
    }
    
    public function AllUsersList()
    {
        $allUsersList = User::get();
        return $allUsersList;
    }
    
    public function Dashboard()
    {
        $userId = session('user_id');
        $user = User::find($userId);
        $this->Handle();
        $username = $user->name;
        $usercompanynameS = $user->companyname;
        $usercompanyname = $user->companyname;
        $usercompanyname = str_replace(' ', '_', $usercompanyname);
        $allUsersList = $this->AllUsersList();
        $locations = [];
        
        $dashboard = DB::table('upload_'.$usercompanyname)->get();
        try {
            $totalMessages = DB::table('replies')->where('name_destined', $username)
                                                 ->where('companyname_destined', $usercompanynameS)
                                                 ->count();
        } catch (\Exception $e) {
            $totalMessages = 0;
        }
        
        
        foreach ($allUsersList as $aULT) {
            $location = json_decode($aULT->location, true);
            if (isset($location['latitude']) && isset($location['longitude'])) {
                $latitude = $location['latitude'];
                $longitude = $location['longitude'];
    
                //$city = $this->AllUsersLocation($location['latitude'], $location['longitude']);
                //$cities[] = $city;
                $locations[] = "$latitude, $longitude";
            }
        }
        return view('USER/dashboard', ['user' => $user, 'dashboard' => $dashboard, 'totalMessages' => $totalMessages, 'locations' => $locations]);
    }
    
    public function getDashboardData()
    {
        $userId = session('user_id');
        $user = User::find($userId);
        $usercompanyname = str_replace(' ', '_', $user->companyname);
        $dashboard = DB::table('upload_'.$usercompanyname)->get();
        return response()->json($dashboard);
    }
    
    public function Users()
    {
        $this->Handle();
        return view('USER/users');
    }

    public function Message()
    {
        $userId = session('user_id');
        $user = User::find($userId);
        $this->Handle();
        $username = $user->name;
        $usercompanynameS = $user->companyname;
        $usercompanyname = $user->companyname;
        $usercompanyname = str_replace(' ', '_', $usercompanyname);
        $dashboard = DB::table('upload_'.$usercompanyname)->get();
        try {
            $messages = DB::table('replies')->where('name_destined', $username)
                                                 ->where('companyname_destined', $usercompanynameS)
                                                 ->get();
        } catch (\Exception $e) {
            $totalMessages = 0;
        }
        return view('USER/message', ['user' => $user, 'messages' => $messages]);
    }

    public function DetailMessage(Request $request)
    {
        $messageId = $request->input('msg_id');
        $messages = DB::table('replies')->where('id', $messageId)->get();
        if ($messages) {
            return view('USER/detail-message', ['messages' => $messages]);
        } else {
            return response()->json(['error' => 'Failed to save data.']);
        }
        $this->Handle();
    }
    
    public function GoogleList()
    {   
        $this->Handle();
        return view('USER/googles');
    }
    
    public function Database()
    {
        $userId = session('user_id');
        $user = User::find($userId);
        $this->Handle();
        
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        if (strpos($userAgent, 'Linux x86_64') == true || strpos($userAgent, 'aarch64') == true) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            echo $userAgent;
            header('HTTP/1.0 403 Forbidden');
            echo "\nTidak bisa mengakses selain menggunakan Raspberry Pi 5.";
            exit;
        }else{
            return view('USER/database', ['user' => $user]);
        }
    }
    
    public function SubmitSQL(Request $request)
    {
        $sqlQuery = $request->input('sqlQuery');
        $SQLQry = '';
        $userId = $request->input('user_id');
        $user = User::find($userId);
        $companyname = $user->companyname;
        $companyname = str_replace(' ', '_', $companyname);
        //$data = DB::table($companyname)->get();
        //$this->Handle();
        
        if($request->input('table_id')){
            $tableid = $request->input('table_id');    
        }
        
        $tableReference = ' ';
        
        try{
            if(stripos($sqlQuery, 'show') !== false){
                if(stripos($sqlQuery, 'databases') !== false){
                    $SQLQry = "SHOW TABLES LIKE `upload_$companyname`";
                    $pdo = DB::getPdo();
                    $result = $pdo->query($SQLQry);
                    return response()->json([
                        'message' => 'Query executed successfully!',
                        'data' => $result
                    ]);
                }else if(stripos($sqlQuery, 'tables') !== false){
                    $SQLQry = "SELECT DISTINCT nik FROM `upload_$companyname`";
                    $result = DB::select($SQLQry);
                    return response()->json([
                        'message' => 'Query executed successfully!',
                        'data' => $result
                    ]);
                }else if(stripos($sqlQuery, 'columns')){
                    
                }else if(stripos($sqlQuery, 'status')){
                    $SQLQry = "SHOW STATUS";
                    $result = DB::select($SQLQry);
                    return response()->json([
                        'message' => 'Query executed successfully!',
                        'data' => $result
                    ]);
                }else if(stripos($sqlQuery, 'variables')){
                    $SQLQry = "SHOW VARIABLES";
                    $result = DB::select($SQLQry);
                    return response()->json([
                        'message' => 'Query executed successfully!',
                        'data' => $result
                    ]);
                }
            }else if(stripos($sqlQuery, 'use') !== false){
                if(stripos($sqlQuery, $companyname) === false){
                    $result = "No Data Available";
                    return response()->json([
                        'message' => 'Query executed successfully!',
                        'data' => $result
                    ]);
                }else{
                    $result = DB::select($sqlQuery);
                    return response()->json([
                        'message' => 'Query executed successfully!',
                        'data' => $result
                    ]);
                }
            }else if(stripos($sqlQuery, 'select') !== false){
                if(stripos($sqlQuery, $companyname) !== false){
                    $result = "No Data Available";
                    return response()->json([
                        'message' => 'Query executed successfully!',
                        'data' => $result
                    ]);
                }else{
                    $result = DB::select($sqlQuery);
                    return response()->json([
                        'message' => 'Query executed successfully!',
                        'data' => $result
                    ]);
                }
            }
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Error executing query: ' . $e->getMessage()
            ], 500);
        }
    }

    public function TransportationDashboard(Request $request){
            if(!$request->has('nik')){
                $userId = $request->input('user_id');
                if($userId == null){
                    $userId = session('user');
                }
                $user = User::find($userId);
                return view('USER/transportation-data-dashboard', ['user' => $user]);
            }else{
                $userId = $request->input('userId');
                if($userId == null){
                    $userId = session('user');
                }
                $user = User::find($userId);
                $serviceAPath = base_path('monitoring-bbm-my-id-4231a6208e71.json');
                $factory = (new Factory)->withServiceAccount($serviceAPath);
                $firestore = $factory->createFirestore();
                $database = $firestore->database();
    
                $nikArray = json_decode($request->input('nik'), true);
                
                $nikArray = json_decode($request->input('nik'), true);
                $collectionReference = $database->collection($user->companyname);
                
                foreach ($nikArray as $nik) {
                    $documentReference = $collectionReference->document($nik);
                    
                    if (!$documentReference->snapshot()->exists()) {
                        $documentReference->create([]);
                    }
                }
                $condition = 'monitoring';
                return view('USER.redirect', ['condition' => $condition, 'user' => $user]);
            }
    }

    public function Monitoring(Request $request)
    {   
            $this->Handle();
            $userId = $request->input('user_id');
            $user = User::find($userId);
            $usercompanyname = $user->companyname;
            $usercompanyname = str_replace(' ', '_', $usercompanyname);
            $tableName = 'upload_' . $usercompanyname;
            if (Schema::hasTable($tableName)) {
                
                $data = DB::table($tableName)->get();
                $groupedData = $data->groupBy('nik');

                $monitoringT = [];
                $monitoring = [];
        
                foreach ($groupedData as $nik => $records) {
                    $sortedRecords = $records->sortByDesc('timestamp');
                    $latestRecord = $sortedRecords->first();
                    $monitoringT[$nik] = $latestRecord;
                }
        
                foreach ($monitoringT as $nik => $record) {
                    $monitoring[] = [
                        "nik" => $nik,
                        "id" => $record->id,
                        "companyname" => $record->companyname,
                        "level" => $record->level,
                        "timestamp" => $record->timestamp
                    ];
                }
            } else {
                $monitoring = null;
            }
            
            return view('USER/monitoring', ['monitoring' => $monitoring, 'user' => $user]);
    }
    
        
    public function getMonitoringData($companyname)
    {
        $tableName = 'upload_' . $companyname;
    
        if (!Schema::hasTable($tableName)) {
            return response()->json(['error' => 'Tabel tidak ditemukan'], 404);
        }
    
        try {
            $data = DB::table($tableName)
                ->orderBy('timestamp', 'desc')
                ->get()
                ->groupBy('nik');
    
            $monitoringData = [];
    
            foreach ($data as $nik => $records) {
                $sortedRecords = $records->sortByDesc('timestamp');
                $latestRecord = $sortedRecords->first();
                $monitoringData[$nik] = $latestRecord;
            }
    
            return response()->json($monitoringData);
    
        } catch (\Exception $e) {
            return response()->json([
                'tableName' => $tableName,
                'error' => 'Error fetching data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function showMonitoringPage()
    {
        $companyname = Auth::users()->companyname;
        return view('monitoring', compact('companyname'));
    }
    
    public function DetailMonitoring($nik, $companyname)
    {   
        $usercompanyname = $companyname;
        $usercompanyname = str_replace(' ', '_', $usercompanyname);
        $tableName = 'upload_' . $usercompanyname;

        $dataForChart = [];
        if (Schema::hasTable($tableName)) {
                
                $data = DB::table($tableName)->where('nik', $nik)->get();
                $groupedData = $data->groupBy($nik);

                $monitoringT = [];
                $monitoring = [];
        
                foreach ($groupedData as $groupNik => $records) {
                    foreach ($records as $record) {
                        $dataForChart[] = [
                            "nik" => $record->nik,
                            "id" => $record->id,
                            "companyname" => $record->companyname,
                            "level" => $record->level,
                            "timestamp" => $record->timestamp
                        ];
                    }
                }
        } else {
            $monitoring = null;
        }
        return view('USER/detail-monitoring', [
            'dataForChart' => $dataForChart,
        ]);
    } 
    
    public function Truncate()
    {
        $truncate = Monitoring::truncate();
        return redirect()->route('monitoring');
    }

    public function Products(Request $request)
    {
        $userId = $request->input('user_id');
        $user = User::find($userId);
        $this->Handle();
        $data = DB::connection('qibiujnz_bbm-tbb-marketplace')->table('products')->get();
        return view('USER/products', ['user' => $user, 'data' => $data]);
    }
    
    public function PaymentMethod(Request $request)
    {
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $orderId = uniqid('order_'); 
        $grossAmount = 100000; // Pastikan ini tipe integer, bukan string
    
        if (!is_numeric($grossAmount)) {
            return response()->json(['error' => 'Invalid amount']);
        }
    
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'johndoe@example.com',
                'phone' => '081234567890',
            ],
        ];
    
        \Log::info('Transaction Details: ', $params);
    
        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $redirectUrl = Snap::createTransaction($params)->redirect_url;
            //$paymentOptions = \Midtrans\CoreApi::getPaymentOptions();
             $paymentOptions = [
                [
                    'name' => 'E-wallet',
                    'code' => 'e_wallet',
                    'sub_options' => [
                        ['name' => 'ShopeePay', 'code' => 'shopeepay'],
                        ['name' => 'GoPay', 'code' => 'gopay'],
                        ['name' => 'OVO', 'code' => 'ovo'],
                    ],
                ],
                [
                    'name' => 'Card Payments',
                    'code' => 'card_payments',
                    'sub_options' => [
                        ['name' => 'VISA', 'code' => 'visa'],
                        ['name' => 'MasterCard', 'code' => 'gopay'],
                        ['name' => 'OVO', 'code' => 'ovo'],
                    ],
                ],
                [
                    'name' => 'Bank Transfer',
                    'code' => 'bank_transfer',
                    'sub_options' => [
                        ['name' => 'BCA', 'code' => 'bca'],
                        ['name' => 'BNI', 'code' => 'bni'],
                        ['name' => 'BRIVIA', 'code' => 'brivia'],
                        ['name' => 'Mandiri', 'code' => 'mandiri'],
                        ['name' => 'Permata Bank', 'code' => 'permata'],
                        ['name' => 'CIMB Niaga', 'code' => 'brivia'],
                    ],
                ],
                [
                    'name' => 'Kartu Debit',
                    'code' => 'debit_card',
                ],
                [
                    'name' => 'Over The Counter',
                    'code' => 'over_the_counter',
                    'sub_options' => [
                        ['name' => 'Indomaret', 'code' => 'indomaret'],
                        ['name' => 'Alfamart', 'code' => 'alfamart'],
                        ['name' => 'Alfamidi', 'code' => 'alfamidi'],
                    ],
                ],
                [
                    'name' => 'Kartu Kredit',
                    'code' => 'credit_card',
                ],
            ];
            return view('USER/payment-method', compact('snapToken', 'redirectUrl'));
        } catch (\Exception $e) {
            \Log::error('Midtrans API error: ' . $e->getMessage());
            return response()->json(['error' => 'Midtrans API error: ' . $e->getMessage()]);
        }
    }

    public function Carts(Request $request)
    {   
        //$product = json_decode($request->input('product'));
        $product = json_decode($request->getContent(), true);
        //$deleteproduct = $request->input('product');
        if(!$product){
            return view('USER/carts');
        }else{
            $productData = $product['product'];
        }
        
        if (!$product || !isset($productData['id'])) {
            return response()->json(['message' => $product], 400);
        }/*else if($deleteproduct == 1){
            session()->forget('cart', $cart);
        }*/else{
            $cart = session()->get('cart', []);
            $cart[] = $productData;
            session()->put('cart', $cart);
            return response()->json(['message' => 'Barang berhasil ditambahkan ke keranjang']);
        }
    }
    
    public function Checkout(Request $request)
    {   
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $orderId = uniqid('order_'); 
        
        $priceTotal = $request->input('price_total');
        $productId = $request->input('product_id');
        
        $grossAmount = $priceTotal;
        
        //var_dump($priceTotal);
        //var_dump($productId);
    
        if (!is_numeric($grossAmount)) {
            return response()->json(['error' => 'Invalid amount']);
        }
    
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'johndoe@example.com',
                'phone' => '081234567890',
            ],
        ];
    
        \Log::info('Transaction Details: ', $params);
    
        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $redirectUrl = Snap::createTransaction($params)->redirect_url;
             $paymentOptions = [
                [
                    'name' => 'E-wallet',
                    'code' => 'e_wallet',
                    'sub_options' => [
                        ['name' => 'ShopeePay', 'code' => 'shopeepay'],
                        ['name' => 'GoPay', 'code' => 'gopay'],
                        ['name' => 'OVO', 'code' => 'ovo'],
                    ],
                ],
                [
                    'name' => 'Card Payments',
                    'code' => 'card_payments',
                ],
                [
                    'name' => 'Bank Transfer',
                    'code' => 'bank_transfer',
                ],
                [
                    'name' => 'Kartu Debit',
                    'code' => 'debit_card',
                ],
                [
                    'name' => 'Over The Counter',
                    'code' => 'over_the_counter',
                ],
                [
                    'name' => 'Kartu Kredit',
                    'code' => 'credit_card',
                ],
            ];
            return view('USER/checkout', compact('snapToken', 'redirectUrl'));
        } catch (\Exception $e) {
            \Log::error('Midtrans API error: ' . $e->getMessage());
            return response()->json(['error' => 'Midtrans API error: ' . $e->getMessage()]);
        }
    }
    
    public function Maintenances(Request $request)
    {  
        $userId = $request->input('user_id');
        if($userId == null){
            $userId = $request->session()->get('user_id');
        }
        $user = User::find($userId);
        $messages = DB::table('messages')->get();
        $this->Handle();
        return view('USER/request-maintenance', ['user' => $user, 'messages' => $messages]);
    }
    
    public function MaintenancesRequest(Request $request)
    {
        $userId = $request->input('user_id');
        $user = User::find($userId);
        
        $request->validate([
            'picture' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        $url = null;
        
        $messages = $request->input('title') . "<br>" . $request->input('description') . "<br>" . $request->input('note');
        $timesent = now();
        $status = 'read';
        
        if ($request->hasFile('picture')) {
            $picture = $request->file('picture');
            $directoryPath = '/home/qibiujnz/public_html/storage/post/messages-picture/';
            $fileName = uniqid() . '.' . $picture->getClientOriginalExtension();
            $picture->move($directoryPath, $fileName);
            $url = '/storage/post/messages-picture/' . $fileName;
            $data = DB::table('messages')->insert([
                'name_sender' => $user->name,
                'companyname_sender' => $user->companyname,
                'messages' => $messages,
                'picture' => $url,
                'time_sent' => $timesent,
                'status' => $status
            ]);
        }
        
        $request->session()->flash('user_id', $user->id);
        return redirect()->route('maintenances');
    }
    
    public function MaintenancesRequestDetail(Request $request)
    {
        $messageId = $request->input('msg_id');
        $messages = DB::table('messages')->where('id', $messageId)->get();
        if ($messages) {
            return view('USER/detail-request-maintenance', ['messages' => $messages]);
        } else {
            return response()->json(['error' => 'Failed to save data.']);
        }
    }
    
    public function History(Request $request)
    {
        $userId = $request->input('user_id');
        $user = User::find($userId);
        $this->Handle();
        return view('USER/history', ['user' => $user]);
    }

    public function Delete(Request $request)
    {   
        $userId = $request->input('user_id');
        $user = User::find($userId);
        $this->Handle();
        Session::forget('page_history');
        return view('USER/history', ['user' => $user]);
    }

    public function Settings(Request $request)
    {   
        $userId = $request->input('user_id') ?? session('user');
        
        if($userId == null){
            $userId = session('user');
        }
        $user = User::find($userId);
        $this->Handle();
        
        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), [
                'new-password' => 'nullable|min:8', 
                'current-password' => 'required_with:new_password'
            ]);
    
            if ($validator->fails()) {
                //return redirect()->back()->withErrors($validator)->withInput();
            }
    
            if ($request->filled('new-password')) {
                if (!Hash::check($request->input('current-password'), $user->password)) {
                    return redirect()->back()->with('error', 'Current password is incorrect.');
                }

                $user->password = Hash::make($request->input('new-password'));
                $user->save();
    
                //return redirect()->back()->with('success', 'Password updated successfully.');
                return view('USER/settings', ['user' => $user]);
            }
            return view('USER/settings', ['user' => $user]);
        }
    }
}
?>