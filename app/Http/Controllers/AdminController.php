<?php

namespace App\Http\Controllers;
use App\Models\Monitoring;
use App\Models\Admin;
use App\Models\User;
use App\Models\Records;
use Google\Cloud\FirestoreClient;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Geocoder\Provider\Nominatim\Nominatim;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;
use Http\Client\Curl\Client as HttpClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;
use GuzzleHttp\Client;

class AdminController extends Controller
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
        return view('ADMIN/check-status', ['data' => $data]);
    }

    public function SignIn()
    {
        $data = 1;
        return view('ADMIN/check-status', ['data' => $data]);
    }
    
    public function SignUpSave(Request $request){
        $admin = new Admin;
        $admin->name = $request->input('username');
        $admin->email = $request->input('email');
        $admin->password = Hash::make($request->input('psw'));
        $admin->save();
        $request->session()->put('admin_id', $admin->id);
        return redirect()->route('admin.dashboard');
    }
    
    public function SignInChecker(Request $request)
    {
        $admin = new Admin;
        $email = $request->input('email');
        $password = $request->input('psw');
        
        $admin = Admin::where('email', $email)->first();
        
        if($admin && Hash::check($password, $admin->password)){
            session(['admin_id' => $admin->id]);
            return redirect()->route('admin.dashboard');
        }else{
            return back()->withErrors(['login' => "Invalid Email or password"]);
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
    
    public function ActiveUsers()
    {
        $activeUsers = User::where('last_active', '>=', now()->subMinutes(30))->count();
        return $activeUsers;
    }
    
    public function TotalUsers()
    {
        $allUsers = User::count();
        return $allUsers;
    }
    
    public function AllUsersList()
    {
        $allUsersList = User::get();
        return $allUsersList;
    }
    
    public function Dashboard()
    {
        $this->Handle();
        $adminId = session('admin_id');
        $admin = Admin::find($adminId);
        $activeUsers = $this->ActiveUsers();
        $totalUsers = $this->TotalUsers();
        $allUsersList = $this->AllUsersList();
        $latitude = '';
        $longitude = '';
        //$cities = [];  
        $locations = [];
        
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
    
        $totalMessages = DB::table('messages')->count();
    
        return view('ADMIN/dashboard', [
            'admin' => $admin, 
            'activeUsers' => $activeUsers, 
            'totalUsers' => $totalUsers, 
            'allUsersList' => $allUsersList, 
            'totalMessages' => $totalMessages,
            'locations' => $locations
        ]);
    }
    
    public function Message()
    {
        $this->Handle();
        $adminId = session('admin_id');
        $admin = Admin::find($adminId);
        $messages = DB::table('messages')->get();
        return view('ADMIN/message', ['admin' => $admin, 'messages' => $messages]);
    }

    public function DetailMessage(Request $request)
    {
        $this->Handle();
        $messageId = $request->input('msg_id');
        $messages = DB::table('messages')->where('id', $messageId)->get();
        if ($messages) {
            return view('ADMIN/detail-message', ['messages' => $messages]);
        } else {
            return response()->json(['error' => 'Failed to save data.']);
        }
    }
    
    public function UserManager(Request $request)
    {
        $this->Handle();
        $adminId = $request->input('admin_id');
        if(empty($adminId)){
            $adminId = session('admin_id');
        }
        $admin = Admin::find($adminId);
        $allUsersList = $this->AllUsersList();
        return view('ADMIN/user-manager', ['admin' => $admin, 'allUsersList' => $allUsersList]);
    }
    
    public function FileManager(Request $request)
    {   
        $this->Handle();
        $adminId = $request->input('admin_id');
        $admin = Admin::find($adminId);
        $directoryPath = '/home/qibiujnz/public_html/api/';

        if (is_dir($directoryPath)) {
            $files = scandir($directoryPath);
            $files = array_diff($files, ['.', '..']);
            $files = preg_grep('/^upload_/', $files);
        } else {
            $files = [];
            session()->flash('error', 'Directory path is invalid or inaccessible.');
        }
        //var_dump($admin);
        
        $data = DB::table('process')->get();
        
        return view('ADMIN/file-manager', ['files' => $files, 'admin' => $admin, 'data' => $data]);
    }
    
    public function ToggleFile(Request $request)
    {
        $this->Handle();
        $filename = $request->input('filename');
        $action = $request->input('action');
        if($action === 'START') {
            DB::table('process')->updateOrInsert(
                ['filename' => $filename],
                ['status' => 'STOP']
            );
            return response()->json(['success' => true, 'message' => 'Execution started.']);
        }else if($action === 'STOP'){
            DB::table('process')->updateOrInsert(
                ['filename' => $filename],
                ['status' => 'START']
            );
            return response()->json(['success' => true, 'message' => 'Execution stopped.']);
        }
    }
    
    public function showFileContent($filename)
    {
        $this->Handle();
        $directoryPath = '/home/qibiujnz/public_html/api/';
        $filePath = $directoryPath . '/' . $filename;
        if (file_exists($filePath) && is_readable($filePath)) {
            $content = file_get_contents($filePath);
            //print_r($content);
            return view('ADMIN/file-content', ['filename' => $filename, 'content' => $content]);
        } else {
            abort(404, "File not found or inaccessible.");
        }
    }
    
    public function saveFileContent(Request $request, $filename)
    {
        $this->Handle();
        $directoryPath = '/home/qibiujnz/public_html/api';
        $filePath = $directoryPath . '/' . $filename;
    
        if (file_exists($filePath) && is_writable($filePath)) {
            file_put_contents($filePath, $request->input('content')); // Simpan konten file
            return redirect()->route('admin.file-content', ['filename' => $filename])
                             ->with('success', 'File updated successfully!');
        } else {
            return back()->withErrors(['error' => 'File not found or is not writable.']);
        }
    }
    
    public function ProductsManager(Request $request)
    {
        $this->Handle();
        $adminId = $request->input('admin_id');
        if(empty($adminId)){
            $adminId = session('admin_id');
        }
        $admin = Admin::find($adminId);
        $data = DB::connection('qibiujnz_bbm-tbb-marketplace')->table('products')->get();
        return view('ADMIN/products-manager', ['admin' => $admin, 'data' => $data]);
    }
    
    public function AddProduct(Request $request)
    {   
        $this->Handle();
        $adminId = $request->input('admin_id');
        $admin = Admin::find($adminId);
        return view('/ADMIN/add-products', ['admin' => $admin]);
    }
    
    public function EditProduct(Request $request)
    {   
        $this->Handle();
        $adminId = $request->input('admin_id');
        $productsId = $request->input('product_id');
        $admin = Admin::find($adminId);
        $data = DB::connection('qibiujnz_bbm-tbb-marketplace')->table('products')->where('id', $productsId)->get();
        return view('/ADMIN/edit-products', ['admin' => $admin, 'data' => $data]);
    }

    public function SaveProduct(Request $request)
    {
        $this->Handle();
        $adminId = $request->input('admin_id');
        $admin = Admin::find($adminId);
        $request->validate([
            'productName' => 'required|string|max:255',
            'productPrice'  => 'required|int',
            'productQuantity'  => 'required|int',
            'productDescription' => 'required|string',
            'productImages.*' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        $url = null;
        
        if ($request->hasFile('productImages')) {
            $productImage = $request->file('productImages');
            $directoryPath = '/home/qibiujnz/public_html/storage/post/products-picture/';
            $fileName = uniqid() . '.' . $productImage->getClientOriginalExtension();
            $productImage->move($directoryPath, $fileName);
            $url = '/storage/post/products-picture/' . $fileName;
            DB::connection('qibiujnz_bbm-tbb-marketplace')->table('products')->insert([
                'product_name' => $request->input('productName'),
                'price' => $request->input('productPrice'),
                'quantity' => $request->input('productQuantity'),
                'description' => $request->input('productDescription'),
                'product_image' => $url,
            ]);
        } else {
            $products->gambar = null;
        }
        session(['admin_id' => $adminId]);
        return redirect()->route('admin.products-manager');
    }

    public function UpdateProduct(Request $request)
    {
        $adminId = $request->input('admin_id');
        $admin = Admin::find($adminId);
    
        $request->validate([
            'productName' => 'required|string|max:255',
            'productPrice' => 'required|integer',
            'productQuantity' => 'required|integer',
            'productDescription' => 'required|string',
            'productImages' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
    
        $productId = $request->input('product_id');
        $product = DB::connection('qibiujnz_bbm-tbb-marketplace')
                     ->table('products')
                     ->where('id', $productId)
                     ->first();
    
        if (!$product) {
            return back()->withErrors(['message' => 'Produk tidak ditemukan.']);
        }
    
        $url = $product->product_image; // Tetap gunakan gambar lama jika tidak ada gambar baru
        if ($request->hasFile('productImages')) {
            $productImage = $request->file('productImages');
            $fileName = uniqid() . '.' . $productImage->getClientOriginalExtension();
            $directoryPath = public_path('storage/post/products-picture/');
            $productImage->move($directoryPath, $fileName);
            $url = '/storage/post/products-picture/' . $fileName;
    
            if (!empty($product->product_image) && file_exists(public_path($product->product_image))) {
                unlink(public_path($product->product_image));
            }
        }
    
        DB::connection('qibiujnz_bbm-tbb-marketplace')
            ->table('products')
            ->where('id', $productId)
            ->update([
                'product_name' => $request->input('productName'),
                'price' => $request->input('productPrice'),
                'quantity' => $request->input('productQuantity'),
                'description' => $request->input('productDescription'),
                'product_image' => $url,
            ]);
    
        session(['admin_id' => $adminId]);
    
        return redirect()->route('admin.products-manager')->with('success', 'Produk berhasil diperbarui.');
    }

    public function Update(Request $request, $id)
    {   
        $this->Handle();
        $request->validate([
            'productName' => 'required|string|max:255',
            'productDescription' => 'required|string',
            'productImage' => 'nullable|file|image|max:2048',
        ]);

        $product = Products::find($id);
        $product->nama = $request->input('productName');
        $product->deskripsi = $request->input('productDescription');

        if ($request->hasFile('productImage')) {
            $path = $request->file('productImage')->store('post');
            $product->gambar = $path;
        }
        $product->save();
        return redirect()->route('admin.produk');
    }

    public function Delete(Request $request, $id)
    {
        $product = Products::find($id);
        $product->delete();
        return redirect()->route('admin.produk');
    }
    
    public function Maintenances(Request $request)
    {
        $this->Handle();
        $adminId = $request->input('admin_id');
        $admin = Admin::find($adminId);
        if($adminId == null){
            $adminId = $request->session()->get('admin_id');
        }
        $admin = Admin::find($adminId);
        $messages = DB::table('replies')->get();
        return view('ADMIN/request-maintenance', ['admin' => $admin, 'messages' => $messages]);
    }
    
    public function MaintenancesRequest(Request $request)
    {
        $this->Handle();
        $adminId = $request->input('admin_id');
        $admin = Admin::find($adminId);
        
        $request->validate([
            'picture' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        $url = null;
        
        $destinedusername = $request->input('destinedusername');
        $destinedusercompanyname = $request->input('destinedusercompanyname');
        $messages = $request->input('title') . "<br>" . $request->input('description') . "<br>" . $request->input('note');
        $timesent = now();
        $status = 'read';
        
        if ($request->hasFile('picture')) {
            $picture = $request->file('picture');
            $directoryPath = '/home/qibiujnz/public_html/storage/post/replies-picture/';
            $fileName = uniqid() . '.' . $picture->getClientOriginalExtension();
            $picture->move($directoryPath, $fileName);
            $url = '/storage/post/replies-picture/' . $fileName;
            $data = DB::table('replies')->insert([
                'name_sender' => $admin->name,
                'name_destined' => $destinedusername,
                'companyname_destined' => $destinedusercompanyname,
                'messages' => $messages,
                'picture' => $url,
                'time_sent' => $timesent,
                'status' => $status
            ]);
        }
        
        $request->session()->flash('admin_id', $admin->id);
        return redirect()->route('maintenances');
    }
    
    public function MaintenancesRequestDetail(Request $request)
    {
        $this->Handle();
        $messageId = $request->input('msg_id');
        $messages = DB::table('replies')->where('id', $messageId)->get();
        if ($messages) {
            return view('ADMIN/detail-request-maintenance', ['messages' => $messages]);
        } else {
            return response()->json(['error' => 'Failed to save data.']);
        }
    }
    
    public function HistoryTracker(Request $request)
    {
        $adminId = $request->input('admin_id');
        $admin = Admin::find($adminId);
        $this->Handle();
        return view('ADMIN/history-tracker', ['admin' => $admin]);
    }
    
    public function Settings(Request $request)
    {   
        $this->Handle();
        $adminId = $request->input('admin_id') ?? session('admin');
        
        if($adminId == null){
            $adminId = session('admin');
        }
        $admin = Admin::find($adminId);
        
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
                return view('ADMIN/settings', ['admin' => $admin]);
            }
            return view('ADMIN/settings', ['admin' => $admin]);
        }
    }
}

?>