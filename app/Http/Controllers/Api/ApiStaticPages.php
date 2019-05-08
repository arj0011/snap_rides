<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
 
class ApiStaticPages extends Controller {
     
      function faq(request $request){
       
        $faq_for = $request->faq_for;
        if($faq_for==null || $faq_for==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Faq for can not be blank");
            print_r(json_encode($data));
            exit;
        }
        
        $language_type = $request->language_type;
        if($language_type==null || $language_type==''){
            $status = false;
            $data = array("success"=>false, "message"=>"Language type can not be blank");
            print_r(json_encode($data));
            exit;
        }
       
       $data=array();
      /* $data['data']= DB::table('support_categories')
        ->join('support_details','support_categories.id','=','support_details.cat_id')
        ->select('support_details.*')
        ->get();
       */
       $data['data']= DB::table('support_details')
        ->where("faq_for","=",$faq_for)
        ->where("lang_id","=",$language_type)       
        ->get();
       
       return view('admin.faq', $data);
       
    }
    function terms(){
        $data=array();
        return view('static_pages.term_n_conditions', $data);
    }

    function private_policy(){
        $data=array();
       return view('static_pages.private_policy', $data);  
    }

      function contact(){
       $data=array();
       $data['data']= DB::table('support_categories')
        ->join('support_details','support_categories.id','=','support_details.cat_id')
        ->select('support_details.*')
        ->get();
        return view('admin.contact', $data);
    }

    function pages(Request $request){

        $page = $request->input('page');
        if($page==null || $page==''){
            $page=1;
        }

        $lang_id = $request->input('lang_id');
        if($lang_id==null || $lang_id==''){
            $lang_id=3;
        }
        $faq_for = $request->input('faq_for');
        if($faq_for==null || $faq_for==''){
            $faq_for=1;
        }
        
        $data = array();
        if($page=='1'){
                $data=array();
                $data['data']= DB::table('support_categories')
                ->join('support_details','support_categories.id','=','support_details.cat_id')
                ->select('support_details.*')
                ->where('faq_for',$faq_for)
                ->where('lang_id',$lang_id)
                ->get();
                return view('admin.faq', $data);
        }else if($page=='2'){
            return view('static_pages.term_n_conditions', $data);
        }else if($page=='3'){
             return view('static_pages.private_policy', $data);  
        }else if($page=='4'){
             return view('static_pages.contact', $data);    
        }


    }


  
}
