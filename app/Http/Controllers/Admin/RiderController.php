<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use App\Customer;
use DB;

class RiderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
         $this->authorize('index', Customer::class);

        try {
            $riders = DB::table('customers')
            ->select( 'customers.id' , 'customers.name' ,'customers.mobile' , 'customers.status as is_active')
           // ->leftJoin('cities', 'customers.city', '=', 'cities.id')
           ->whereNull('deleted_at')
           ->orderBy('id','desc')         
            ->paginate('10');
            return view('admin.riders.index',compact('riders'))->with('i', ($riders->currentpage()-1)*$riders->perpage()+1);
            
        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'danger');
        }
    }

    public function search(Request $request){
        
         $this->authorize('index', Customer::class);

        try {

            $p  = $request->p;      // filed name
            $q  = $request->q;      // string that will be searched

             $riders = DB::table('customers')
            ->select( 'customers.id' , 'customers.name' , 'customers.email' ,'customers.mobile' , 'customers.status as is_active')
            ->whereNull('deleted_at')
           // ->leftJoin('cities', 'customers.city', '=', 'cities.id')
            ->where(function($query) use ($p,$q) {
                    if (empty($p)) {
                        $query -> whereRaw('LOWER(customers.name) like ?', '%'.strtolower($q).'%');
                        $query -> orWhereRaw('LOWER(customers.mobile) like ?', '%'.strtolower($q).'%');
                        // $query -> orWhereRaw('LOWER(customers.email) like ?', '%'.strtolower($q).'%') ;
                        // $query -> orWhereRaw('LOWER(cities.name) like ?', '%'.strtolower($q).'%');
                    }elseif($p == 'name'){
                        $query -> whereRaw('LOWER(customers.name) like ?', '%'.strtolower($q).'%');
                    }elseif ($p == 'mobile') {
                        $query -> whereRaw('LOWER(customers.mobile) like ?', '%'.strtolower($q).'%');
                    }
                    // elseif ($p == 'email') {
                    //     $query -> whereRaw('LOWER(customers.email) like ?', '%'.strtolower($q).'%');
                    // }
                    // elseif ($p == 'city') {
                    //    $query -> orWhereRaw('LOWER(cities.name) like ?', '%'.strtolower($q).'%');
                    // }
                })
               ->paginate('10');
            return view('admin.riders.index',compact('riders','p','q'))->with('i', ($riders->currentpage()-1)*$riders->perpage()+1);

        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'danger');
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
        $this->authorize('view', Customer::class);

        try {
        
           $rider_id= decrypt($request->id); 
           $rider = DB::table('customers')
              ->where("customers.id","=",$rider_id)
             ->select('customers.id', 'customers.name', 'customers.mobile','image as profile_image')
             // ->leftJoin('cities', 'customers.city', '=', 'cities.id')
            ->first();
            
           return view('admin.riders.rider',compact('rider')); 
            
        } catch ( \Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'danger');
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

      $this->authorize('destroy', Customer::class);

      try {
        $id = decrypt($request->id);

        $status = DB::table('customers')->where('id',$id)->update([ 'status' => '0' ,'deleted_at' => \Carbon\Carbon::now() ]);
        if($status){
          return redirect('riders')->with('msg','Rider Successfully deleted')->with('color' , 'success');
        }else{
          return Redirect::back()->with('msg','Failed to delete rider')->with('color' , 'danger');
        }
            
      } catch ( \Exception $e) {
        return Redirect::back()->with('msg','Something went wrong')->with('color' , 'danger');
      }

    }

    public function setStatus(Request $request){
         
         $this->authorize('setStatus', Customer::class);

        try{
           $preStatus = DB::table('customers')->where('id' , '=' , decrypt($request->id))->first();
           switch ($preStatus->status) {
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

                $arr =  array('status' => $status);

                DB::table('customers')->where('id' , '=' , decrypt($request->id))->update($arr);

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
