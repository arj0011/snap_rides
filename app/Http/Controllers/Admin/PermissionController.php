<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use DB;
use App\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
      // $this->authorize('index', Permission::class);

        try {  
               
                $filter = '';
                $filter = $request->filter;

                $permissions = DB::table('permissions as per')
                ->select('per.id','per.name as permission_name' , 'modules.name as permission_for' , 'per.is_active as permission_status')
                ->whereNull('per.deleted_at')
                ->where(function($query) use ($filter) {
                    if (!empty($filter)) {
                      $query -> where('per.permission_for' , '=' , $filter );
                    }
                })
                ->leftJoin('modules' , 'modules.id' , '=' , 'per.permission_for')
                ->paginate('10');

                $modules = DB::table('modules')->select('id' , 'name')->get();

                return view('admin.permissions.index',compact('permissions' , 'modules' , 'filter'))->with('i', ($permissions->currentpage()-1)*$permissions->perpage()+1);

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'warning');
        }
    }

    public function search(Request $request){
       
      //   $this->authorize('index', Permission::class);

        try {
            $filter = '';
            $filter = $request->filter;
            $action  = $request->p;      
            $string  = $request->q;
                $permissions = DB::table('permissions as per')
                ->select('per.id','per.name as permission_name' , 'modules.name as permission_for' , 'per.is_active as permission_status')
                // ->orderby('per.id','DESC')
                ->whereNull('per.deleted_at')
                ->where(function($query) use ($action,$string) {
                    if (empty($action)) {
                        $query -> whereRaw('LOWER(per.name) like ?', '%'.strtolower($string).'%');
                    }
                })
                ->where(function($query) use ($filter) {
                    if (!empty($filter)) {
                      $query -> where('per.permission_for' , '=' , $filter );
                    }
                })
               ->leftJoin('modules' , 'modules.id' , '=' , 'per.permission_for')
               ->paginate('10');

                $modules = DB::table('modules')->select('id' , 'name')->get();

            return view('admin.permissions.index',compact('permissions','string','action','modules' ,'filter'))->with('i', ($permissions->currentpage()-1)*$permissions->perpage()+1);

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg',$e->getMessage())->with('color' , 'warning');
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //  $this->authorize('create', Permission::class);

        try {
            $modules = DB::table('modules')->select('id','name')->get();

            return view('admin.permissions.add',compact('modules')); 
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
        //  $this->authorize('create', Permission::class);

             $request->validate([
                'permission_name' => 'required',
                'permission_for' => 'required',   
                'permission_status' => 'required'
            ]);

        try {

             $permission_data = array(
                'name' => strtolower($request->permission_name),   
                'permission_for' => $request->permission_for, 
                'is_active' => $request->permission_status,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
                );

            $status = DB::table('permissions')->insertGetId($permission_data);

            if($status){
                return redirect('permissions')->with('msg','Successfully added permission')->with('color' , 'success');
            }else{
                return Redirect::back()->with('msg','Failed to add permission')->with('color' , 'danger');
            }

        }catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
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
       //   $this->authorize('update', Permission::class);

        try {
                $permission = DB::table('permissions')
                ->select('id','name as permission_name' , 'is_active as permission_status' ,'permission_for')
                ->where('id' , '=' , decrypt($request->id))
                ->first();

                $modules = DB::table('modules')->select('id','name')->get();

                return view('admin.permissions.edit',compact( 'permission', 'modules' ));

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
       //   $this->authorize('update', Permission::class);

         $request->validate([
            'permission_name' => 'required',
            'permission_for' => 'required',
            'permission_status' => 'required'
        ]);
         
       try {

             $permission_data = array(
                'name' => strtolower($request->permission_name),   
                'permission_for' => $request->permission_for, 
                'is_active' => $request->permission_status,
                'updated_at' => \Carbon\Carbon::now()
                );

            $status = DB::table('permissions')->where('id' , '=' , decrypt($request->id) )->update($permission_data);

            $redirects_to = $request->redirects_to; // for redirect to perticular page previes page
              
            $status = true;
            if($status){
                return redirect($redirects_to)->with('msg','Successfully updated permission ')->with('color' , 'success');
            }
            else{
                return Redirect::back($redirects_to)->with('msg','Failed to update permission')->with('color' , 'danger');
            }
        } catch ( \Exception $e) {
            return Redirect::back($redirects_to)->with('msg',$e->getMessage())->with('color' , 'warning');
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
        //  $this->authorize('delete', Permission::class); 

        try {
          $id = decrypt($request->id);
         $status = DB::table('permissions')->where('id' , '=' , decrypt($request->id) )->delete();
          if($status){
                return redirect('permissions')->with('msg','Successfully deleted permissions!')->with('color' , 'success');
            }
            else{
                return Redirect::back()->with('msg','Failed to delete role!')->with('color' , 'danger');
            }

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
    }

    public function setStatus(Request $request){
          
       //   $this->authorize('setStatus', Permission::class);

        try{
           $preStatus = DB::table('permissions')->where('id' , '=' , decrypt($request->id))->first();
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

                DB::table('permissions')->where('id' , '=' , decrypt($request->id))->update($arr);

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
