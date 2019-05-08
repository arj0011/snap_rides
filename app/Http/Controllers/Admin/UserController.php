<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\User;
use DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {    
           $this->authorize('index', User::class);

            try {
              $users = DB::table('users')
              ->select('users.id' , 'users.username' , 'users.mobile' , 'users.email' , 'users.name' , 'users.is_role' , 'roles.id as role_id' , 'roles.name as role_name' ,'users.is_active as user_status')
              ->where('users.id' , '!=' , 1 )
              ->whereNull('users.deleted_at')
               ->selectRaw("CASE WHEN users.is_role = 1 THEN 'Super Admin' WHEN users.is_role = 2 THEN 'Manager' END as role
                    ")
              ->orderby('id','DESC')
              ->leftJoin('roles' , 'roles.id' , '=' , 'users.is_role')
              ->paginate('10');

              return view('admin.users.index',compact('users'))->with('i', ($users->currentpage()-1)*$users->perpage()+1);
              
            } catch ( \Exception $e) {
                return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
            }
    }


    public function search(Request $request){
        
         $this->authorize('index', User::class);

        try {
            $p  = $request->p;      // field name 
            $q  = $request->q;      // string that will be searched

             $users = DB::table('users')
              ->select('users.id' , 'users.username' , 'users.mobile' , 'users.email' , 'users.name' , 'users.is_role' , 'roles.id as role_id' , 'roles.name as role_name' ,'users.is_active as user_status')
              ->where('users.id' , '!=' , 1 )
              ->whereNull('users.deleted_at')
               ->selectRaw("CASE WHEN users.is_role = 1 THEN 'Super Admin' WHEN users.is_role = 2 THEN 'Manager' END as role
                    ")
              ->orderby('id','DESC')
              ->leftJoin('roles' , 'roles.id' , '=' , 'users.is_role')
            ->where(function($query) use ($p,$q) {
                    if (empty($p)) {
                        $query -> whereRaw('LOWER(users.name) like ?', '%'.strtolower($q).'%');
                        $query -> orWhereRaw('LOWER(users.mobile) like ?', '%'.strtolower($q).'%');
                        $query -> orWhereRaw('LOWER(users.email) like ?', '%'.strtolower($q).'%') ;
                    }elseif($p == 'name'){
                        $query -> whereRaw('LOWER(users.name) like ?', '%'.strtolower($q).'%');
                    }elseif ($p == 'mobile') {
                        $query -> whereRaw('LOWER(users.mobile) like ?', '%'.strtolower($q).'%');
                    }elseif ($p == 'email') {
                        $query -> whereRaw('LOWER(users.email) like ?', '%'.strtolower($q).'%');
                    }
                })
               ->paginate('10');

             return view('admin.users.index',compact('users','p','q'))->with('i', ($users->currentpage()-1)*$users->perpage()+1);

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }

    }
  

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    { 
        $this->authorize('create', User::class);
 
        try {
              $roles = DB::table('roles')->select('id' ,'name')->where('is_active' , '=' , '1')->whereNull('deleted_at')->where('id' , '!=' , 1 )->get();

             return view('admin.users.add',compact('roles'));
        } catch ( \Exception $e) {
           return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
    }


      /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
         $this->authorize('create', User::class);

          $request->validate([
              'user_username' => 'required|min:4|unique:users,username,NULL,id,deleted_at,NULL',
              'user_name' => 'required',   
              'user_mobile' => 'required',
              'user_email' => 'required',
              'user_role' => 'required',
              'user_status' => 'required',
              'user_password' => 'required',
          ]);

          try {

           $user_data = array(
            'username' => strtolower($request->user_username),
              'name' => strtolower($request->user_name),   
              'mobile' => $request->user_mobile,
              'email' => $request->user_email,
              'password' => Hash::make($request->user_password),
              'is_role' => decrypt($request->user_role),
              'is_active' => $request->user_status,
              'created_at' =>  \Carbon\Carbon::now(), 
              'updated_at' => \Carbon\Carbon::now()
              );
              
              $insertID = DB::table('users')->insertGetId($user_data);

              if($insertID){

                  return redirect('users')->with('msg','Successfully added dispatcher')->with('color' , 'success');
              }else{

                  return Redirect::back()->with('msg','Failed to add dispatcher')->with('color' , 'danger');  
              }
            } catch ( \Exception $e) {
              
                return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
            }
    }

 /*   function randomString() {
      $str = "";
      $characters = array_merge(range('A','Z'),range('0','9'));
      $max = count($characters) - 1;
      for ($i = 0; $i < 8; $i++) {
        $rand = mt_rand(0, $max);
        $str .= $characters[$rand];
      }
      return $str;
    }
*/
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {   
        $this->authorize('view' , User::class);

        try {
             $user = DB::table('users')
                ->select('users.id' , 'users.username as user_username' , 'users.name as user_name' , 'users.email as user_email' , 'users.mobile as user_mobile' , 'users.created_at' , 'roles.name as user_role')
                ->where('users.id' , '=' , decrypt($request->id))
                ->leftJoin('roles' , 'roles.id' , '=' , 'users.is_role')
                ->first();

                return view('admin.users.user', compact('user')); 

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
    }

    public function edit(Request $request) 
    {   
        $this->authorize('view', User::class);

        try {

             $roles = DB::table('roles')->select('id' ,'name' , 'is_active as user_status' )->whereNull('deleted_at')->where('id' , '!=' , 1 )->get();
                                 
             $user = DB::table('users')
                ->select('users.id' , 'users.username as user_username' , 'users.name as user_name' , 'users.email as user_email' , 'users.mobile as user_mobile' ,  'users.is_active as user_status' ,'users.is_role as user_role')
                ->where('users.id' , '=' , decrypt($request->id))
                ->first();

                return view('admin.users.edit', compact('user','roles')); 
       

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
    }

      /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  public function update(Request $request)
    {     

       $this->authorize('update', User::class);


            $request->validate([
                'user_username' => 'required|min:4|unique:users,username,'.decrypt($request->id).',id,deleted_at,NULL', 
                'user_name' => 'required|max:25|min:2',   
                'user_mobile' => 'required',
                'user_email' => 'required',
                'user_role' => 'required',
                'user_status' => 'required'
            ]);

         try {

            $driver_data = array(
                'username' => $request->user_username,
                'name' => $request->user_name,   
                'mobile' => $request->user_mobile,
                'email' => $request->user_email,
                'is_active' => $request->user_status,
                'is_role' => decrypt($request->user_role),
                'updated_at' => \Carbon\Carbon::now()
            );

               $status =  DB::table('users')->where('id' , '=' , decrypt($request->id))->update($driver_data);

               $redirects_to = $request->redirects_to; // for redirect to perticular previews page

                $status = true;

                if($status){
                  
                  return redirect($redirects_to)->with('msg','Successfully updated dispatcher')->with('color' , 'success');
                }else{

                  return redirect($redirects_to)->with('msg','Failed to update dispatcher')->with('color' , 'danger');
                }   

                } catch (\Exception $e) {

                    return redirect($redirects_to)->with('msg','Something went wrong!')->with('color' , 'warning');
                }
    }


     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    { 
       $this->authorize('delete', User::class);

        try {
       
        $id = decrypt($request->id);

          $vehicle = DB::table('users')->where('id' , '=' , $id)->update(['deleted_at' => \Carbon\Carbon::now() ]);
          if($vehicle){
                return redirect('users')->with('msg','Successfully deleted dispatcher!')->with('color' , 'success');
            }
            else{
                return Redirect::back()->with('msg','Failed to delete dispatcher')->with('color' , 'danger');
            }

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }

    }

      public function setStatus(Request $request){
        
         $this->authorize('setStatus', User::class);

        try{
           $preStatus = DB::table('users')->where('id' , '=' , decrypt($request->id))->first();
           switch ($preStatus->is_active) {
               case '0':
                   $status = '1';
                   break;
               case '1':
                   $status = '0';
                   break;
               default:
                   throw new \Exception('Something went wrong');
                   break;
            }

                $arr =  array('is_active' => $status);

                DB::table('users')->where('id' , '=' , decrypt($request->id))->update($arr);

                if($status == '1'){
                          $arr = array('status' => true);
                } else{
                          $arr = array('status' => false);
                }
                echo json_encode($arr);

        }catch(\Exception $e){
             return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
    }
}
