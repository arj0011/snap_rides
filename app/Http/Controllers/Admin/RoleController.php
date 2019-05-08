<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use DB;
use App\Role;
use App\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
         $this->authorize('index', Role::class);

          try {
                $roles = DB::table('roles')
                ->select('id','name as role_name' , 'is_active as role_status')
                ->whereNull('deleted_at')
              //  ->where('id' , '!=' , 1 )
                ->orderby('id','DESC')
                ->paginate('10');

                return view('admin.roles.index',compact('roles'))->with('i', ($roles->currentpage()-1)*$roles->perpage()+1);

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
    }

       public function search(Request $request){
          
            $this->authorize('index', Role::class);

        try {

            $q  = $request->q; // string that will be searched 

                  $roles = DB::table('roles')
                ->select('id','name as role_name' , 'is_active as role_status')
                ->whereNull('deleted_at')
              //  ->where('id' , '!=' , 1 )
                ->orderby('id','DESC')
                ->where(function($query) use ($q) {
                    if (!empty($q)) {
                        $query -> whereRaw('LOWER(roles.name) like ?', '%'.strtolower($q).'%');
                    }
                })
               ->paginate('10');

            return view('admin.roles.index',compact('roles','q'))->with('i', ($roles->currentpage()-1)*$roles->perpage()+1);

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
           $this->authorize('create', Role::class);

       try {
           
            $permissions = DB::table('permissions as per')->select('per.id' , 'per.name' ,'modules.name as permission_for' )
                         ->whereNull('per.deleted_at')
                         ->leftJoin('modules' , 'modules.id' , '=' , 'per.permission_for')
                         ->get();

            $modules = DB::table('modules')->select('id','name')->whereNull('deleted_at')->get();
     
            return view('admin.roles.add' , compact('permissions','modules')); 

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'warning');
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
           $this->authorize('create', Role::class);

            $request->validate([
                'role_name' => 'required|max:50|unique:roles,name',   
                'role_status' => 'required'
            ]);

         try {

                $role = new Role;
                $role->name = $request->role_name;
                $role->is_active = $request->role_status;
                $role->save();

                $role->permissions()->sync($request->permission);

            if($role){
                return redirect('roles')->with('msg','Successfully added role')->with('color' , 'success');
            }else{
                return Redirect::back()->with('msg','Failed to add role')->with('color' , 'danger');
            }

        }catch ( \Exception $e) {
            return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'warning');
        }
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    { 
          $this->authorize('update', Role::class);

          try {
               
            $permissions = DB::table('permissions as per')->select('per.id' , 'per.name' ,'modules.name as permission_for' )
                         ->whereNull('per.deleted_at')
                         ->leftJoin('modules' , 'modules.id' , '=' , 'per.permission_for')
                         ->get();

            $modules = DB::table('modules')->select('id','name')->whereNull('deleted_at')->get();

            $role = Role::find(decrypt($request->id));
/*                $permissions = Permission::all();

                $modules = DB::table('modules')->select('id','name')->whereNull('deleted_at')->get();*/

                return view('admin.roles.edit',compact('role' ,'permissions' , 'modules'));

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'warning');
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
          $this->authorize('update', Role::class);

            $request->validate([
                'role_name' => 'required|unique:roles,name,'.decrypt($request->id),
                'role_status' => 'required|numeric'
            ]);

          try {
                 
                $role = Role::find(decrypt($request->id));
                $role->name = $request->role_name;
                $role->is_active = $request->role_status;
                $role->save();

                $role->permissions()->sync($request->permission);

            if(true){
                return redirect('roles')->with('msg','Successfully updated role ')->with('color' , 'success');
            }
            else{
                return Redirect::back()->with('msg','Failed to update role')->with('color' , 'danger');
            }
        } catch ( \Exception $e) {
            return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'warning');
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
         $this->authorize('delete', Role::class);

        try {
          $id = decrypt($request->id);
         $status = DB::table('roles')->where('id' , '=' , decrypt($request->id) )->delete();
          if($status){
                return redirect('roles')->with('msg','Successfully deleted role!')->with('color' , 'success');
            }
            else{
                return Redirect::back()->with('msg','Failed to delete role!')->with('color' , 'danger');
            }

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
    }

    public function setStatus(Request $request){
         
         $this->authorize('setStatus', Role::class);

        try{
           $preStatus = DB::table('roles')->where('id' , '=' , decrypt($request->id))->first();
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

                DB::table('roles')->where('id' , '=' , decrypt($request->id))->update($arr);

                if($status == '1'){
                          $arr = array('status' => true);
                } else{
                          $arr = array('status' => false);
                }
                echo json_encode($arr);

        }catch(\Exception $e){
             return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'warning');
        }
    }
}
