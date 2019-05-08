<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use App\Category;
use DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $this->authorize('index', Category::class);
      
        try {
                $categories = DB::table('vehicle_category')
                ->select('id','name as vehicle_type','person_capacity as vehicle_person_capacity','is_active' ,'per_km_charges')
                ->whereNull('deleted_at')
                ->orderby('id','DESC')
                ->paginate('10');

                return view('admin.categories.index',compact('categories'))->with('i', ($categories->currentpage()-1)*$categories->perpage()+1);

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
    }

     public function search(Request $request){
        
         $this->authorize('index', Category::class);

        try {
            $p  = $request->p;      // field name 
            $q  = $request->q;      // string that will be searched
            
            $categories = DB::table('vehicle_category')
            ->select('id','name as vehicle_type','basefare as vehicle_basefare','person_capacity as vehicle_person_capacity','is_active' , 'per_km_charges')
            ->whereNull('deleted_at')
                ->where(function($query) use ($p,$q) {
                    if (empty($p)) {
                        $query -> whereRaw('LOWER(vehicle_category.name) like ?', '%'.strtolower($q).'%');
                        $query -> orWhereRaw('LOWER(vehicle_category.per_km_charges) like ?', '%'.strtolower($q).'%');
                        $query -> orWhereRaw('LOWER(vehicle_category.person_capacity) like ?', '%'.strtolower($q).'%') ;
                    }elseif($p == 'type'){
                        $query -> whereRaw('LOWER(vehicle_category.name) like ?', '%'.strtolower($q).'%');
                    }elseif ($p == 'capacity') {
                        $query -> whereRaw('LOWER(vehicle_category.person_capacity) like ?', '%'.strtolower($q).'%');
                    }elseif ($p == 'per_km_charge') {
                        $query -> whereRaw('LOWER(vehicle_category.per_km_charges) like ?', '%'.strtolower($q).'%');
                    }
                })
               ->orderby('id','DESC')
               ->paginate('10');

            return view('admin.categories.index',compact('categories','p','q'))->with('i', ($categories->currentpage()-1)*$categories->perpage()+1);

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
            $this->authorize('create', Category::class);

        try {
            return view('admin.categories.add'); 
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
           $this->authorize('create', Category::class);

        $request->validate([
            'vehicle_name' =>  'required|unique:vehicle_category,name,NULL,id,deleted_at,NULL',  
            'vehicle_person_capacity' => 'required|numeric',
            // 'vehicle_basefare' => 'required|numeric',
            'per_km_charges' => 'required',
            'category_status' => 'required|numeric',
            'created_at' =>  \Carbon\Carbon::now(), 
            'updated_at' => \Carbon\Carbon::now()
        ]);

        try {

             $vehicle_data = array(
                'name' => $request->vehicle_name,   
                'person_capacity' => $request->vehicle_person_capacity,
                // 'basefare' => $request->vehicle_basefare,
                'per_km_charges' => $request->per_km_charges,
                'is_active' => $request->category_status
                );

                if ($request->hasFile('category_image')) {
                            $imageName = str_random(10).time().'.'.$request->category_image->getClientOriginalExtension(); 
                            $request->category_image->move('Admin/categoryImage', $imageName);
                            $vehicle_data['image'] = $imageName;
                }
                
                $status = DB::table('vehicle_category')->insertGetId($vehicle_data);

                if($status){
                    return redirect('categories')->with('msg','Vehicle category added successfully')->with('color' , 'success');
                }
                else{
                if ($request->hasFile('category_image') && file_exists('Admin/categoryImage/'.$imageName)) {
                    unlink('Admin/categoryImage', $imageName);
                }
                    return Redirect::back()->with('msg','Failed to add vehicle category')->with('color' , 'danger');
                }

        } catch ( \Exception $e) {
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
          $this->authorize('update', Category::class);

        try {
           $category = DB::table('vehicle_category')
            ->select('id','name as vehicle_name', 'image' ,'basefare as vehicle_basefare','person_capacity as vehicle_person_capacity','is_active as category_status' , 'per_km_charges')
            ->where('id' , '=' , decrypt($request->id))
            ->first();
            return view('admin.categories.edit',compact('category'));  
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
           $this->authorize('update', Category::class);

         $request->validate([
            'vehicle_name' => 'required|unique:vehicle_category,name,'.decrypt($request->id).',id,deleted_at,NULL',    
            'vehicle_person_capacity' => 'required|numeric',
            // 'vehicle_basefare' => 'required|numeric',
            'per_km_charges' => 'required',
            'category_status' => 'required|numeric',
            'updated_at' => \Carbon\Carbon::now()
        ]);
         
        try {

             $vehicle_data = array(
                'name' => $request->vehicle_name,   
                'person_capacity' => $request->vehicle_person_capacity,
                // 'basefare' => $request->vehicle_basefare,
                'per_km_charges' => $request->per_km_charges,
                'is_active' => $request->category_status
                );
             
             if ($request->hasFile('category_image')) {
                            $imageName = str_random(10).time().'.'.$request->category_image->getClientOriginalExtension(); 
                            $request->category_image->move('Admin/categoryImage', $imageName);
                            $vehicle_data['image'] = $imageName;
            }

            $old_category_image = DB::table('vehicle_category')
                                    ->select('image')
                                    ->where('id' , '=' , decrypt($request->id))
                                    ->first();

            $status = DB::table('vehicle_category')->where('id' , '=' , decrypt($request->id) )->update($vehicle_data);


            $redirects_to = $request->redirects_to; // for redirect to perticular previes page
              
            $status = true;

            if($status){

                if($request->hasFile('category_image') && file_exists('Admin/categoryImage/'.$old_category_image->image)){
                      @unlink('Admin/categoryImage/'.$old_category_image->image);
                }

                return redirect($redirects_to)->with('msg','Successfully vehicle category updated ')->with('color' , 'success');
            }
            else{

                if ($request->hasFile('category_image') && file_exists('Admin/categoryImage/'.$imageName)) {
                    @unlink('Admin/categoryImage', $imageName);
                }

                return Redirect::back($redirects_to)->with('msg','Failed to update vehicle category')->with('color' , 'danger');
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
             $this->authorize('delete', Category::class);

        try {
          $id = decrypt($request->id);
          $category = Category::find($id);
          if($category->delete()){
                return redirect('categories')->with('msg','Successfully deleted category!')->with('color' , 'success');
            }
            else{
                return Redirect::back()->with('msg','Failed to delete category!')->with('color' , 'danger');
            }

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
    }

      public function setStatus(Request $request){
         
         $this->authorize('setStatus', Category::class);

        try{
           $preStatus = DB::table('vehicle_category')->where('id' , '=' , decrypt($request->id))->first();
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

                DB::table('vehicle_category')->where('id' , '=' , decrypt($request->id))->update($arr);

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
