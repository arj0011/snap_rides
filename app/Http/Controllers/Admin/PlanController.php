<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use App\Plan;
use DB;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
         $this->authorize('index', Plan::class);

        try {
               $plans = DB::table('subcription_plans as plan')
                ->select('plan.id' ,'plan.plan_name' , 'plan.plan_cost' ,'plan.is_active')
                ->selectRaw("CASE WHEN plan.plan_type = 1 THEN 'Weekly' WHEN plan.plan_type = 2 THEN 'Monthly' WHEN plan.plan_type = 3 THEN 'Yearly' END as plan_type
                    ")
                ->selectRaw("SUBSTRING(plan.plan_description, 1, 50) as plan_description ")
                ->whereNull('deleted_at')
                ->orderby('plan.id','DESC')
                ->paginate('10');

                return view('admin.plans.index',compact('plans'))->with('i', ($plans->currentpage()-1)*$plans->perpage()+1); 
               
           } catch ( \Exception $e) {
               return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
           }   
    }

    public function search(Request $request){

        $this->authorize('index', Plan::class);

         try {
                $q  = $request->q;   // stirng that will be searched

               $plans = DB::table('subcription_plans as plan')
               ->select('plan.id' ,'plan.plan_name' , 'plan.plan_cost' ,'plan.is_active')
                ->selectRaw("SUBSTRING(plan.plan_description, 1, 50) as plan_description ")
                ->selectRaw("CASE WHEN plan.plan_type = 1 THEN 'Weekly' WHEN plan.plan_type = 2 THEN 'Monthly' WHEN plan.plan_type = 3 THEN 'Yearly' END as plan_type
                    ")
                ->whereNull('deleted_at')
                ->whereRaw('LOWER(plan.plan_name) like ?', '%'.strtolower($q).'%')
                ->paginate('10');
                return view('admin.plans.index',compact('plans','q'))->with('i', ($plans->currentpage()-1)*$plans->perpage()+1);
               
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
         $this->authorize('create', Plan::class);

         try {
                return view('admin.plans.add');
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
        $this->authorize('create', Plan::class);

        $request->validate([
            'plan_name' => 'required|unique:subcription_plans,plan_name,NULL,id,deleted_at,NULL',
            'plan_description' => 'required',
            'plan_type' => 'required||numeric',
            'plan_cost' => 'required|numeric',
            'plan_status' => 'required|numeric'
        ]);
        
         try {

                 $plan_data = array(
                    'plan_name' => $request->plan_name,   
                    'plan_description' => $request->plan_description,
                    'plan_type' => $request->plan_type,
                    'plan_cost' => $request->plan_cost,
                    'is_active' => $request->plan_status,
                    'created_at' =>  \Carbon\Carbon::now(), 
                    'updated_at' => \Carbon\Carbon::now()
                    );

                $status = DB::table('subcription_plans')->insertGetId($plan_data);
                if($status){
                    return redirect('plans')->with('msg','Successfully added plan')->with('color' , 'success');
                }
                else{
                    return Redirect::back()->with('msg','Failed to add plan')->with('color' , 'danger');
                }
               
           } catch ( \Exception $e) {
               return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
           }  
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {  
        $this->authorize('view', Plan::class);

          try {
                $plan = DB::table('subcription_plans as plan')
                 ->select('plan.id' ,'plan.plan_name' , 'plan.plan_description' , 'plan.plan_type', 'plan.plan_cost' ,'plan.is_active as plan_status' , 'plan.created_at as plan_created_at' , 'plan.updated_at as plan_updated_at')
                 ->whereNull('deleted_at')
                 ->where('plan.id' , '=' , decrypt($request->id))
                 ->first();

                 return view('admin.plans.plan',compact('plan'));
               
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
        $this->authorize('update', Plan::class);

          try {
        	$plan = DB::table('subcription_plans as plan')
             ->select('plan.id' ,'plan.plan_name' , 'plan.plan_description' , 'plan.plan_type', 'plan.plan_cost' ,'plan.is_active as plan_status')
             ->whereNull('deleted_at')
             ->where('plan.id' , '=' , decrypt($request->id))
             ->first();

             return view('admin.plans.edit',compact('plan'));
               
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
        $this->authorize('update', Plan::class);

                 $request->validate([
                     'plan_name' => 'required|unique:subcription_plans,plan_name,'.decrypt($request->id).',id,deleted_at,NULL',
                     'plan_description' => 'required',
                     'plan_type' => 'required||numeric',
                     'plan_cost' => 'required|numeric',
                     'plan_status' => 'required|numeric'
                ]);

           try {

                 $plan_data = array(
                    'plan_name' => strtolower($request->plan_name),   
                    'plan_description' => $request->plan_description,
                    'plan_type' => $request->plan_type,
                    'plan_cost' => $request->plan_cost,
                    'is_active' => $request->plan_status,
                    'updated_at' => \Carbon\Carbon::now()
                    );

                 $redirects_to = $request->redirects_to; // for redirect to perticular previews page

                $status = DB::table('subcription_plans')->where('id','=',decrypt($request->id))->update($plan_data);
                if($status){
                    return redirect($redirects_to)->with('msg','Successfully updated plan')->with('color' , 'success');
                }
                else{
                    return Redirect::back($redirects_to)->with('msg','Failed to update plan')->with('color' , 'danger');
                }  
               
           } catch ( \Exception $e) {
               return Redirect::back($redirects_to)->with('msg','Something went wrong')->with('color' , 'warning');
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
        $this->authorize('delete', Plan::class);

        try {
              $id = decrypt($request->id);
              $driver = Plan::find($id);
              if($driver->delete()){
                    return redirect('plans')->with('msg','Successfully deleted plan!')->with('color' , 'success');
                }
                else{
                    return Redirect::back()->with('msg','Failed to delete plan')->with('color' , 'danger');
                }
               
           } catch ( \Exception $e) {
               return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
           }  
    }

      public function setStatus(Request $request){
         
         $this->authorize('setStatus', Plan::class);

        try{
           $preStatus = DB::table('subcription_plans')->where('id' , '=' , decrypt($request->id))->first();
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

                DB::table('subcription_plans')->where('id' , '=' , decrypt($request->id))->update($arr);

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
