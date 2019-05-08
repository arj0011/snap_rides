<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use DB;
use App\Faq;
use App\Banner;

class SupportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $this->authorize('index', Faq::class);

         try {

               $filter = '';
               $filter = $request->filter;
               $filter_faqfor = '';
               $filter_faqfor = $request->filter_faqfor;
               $filter_faqlang = '';
               $filter_faqlang = $request->filter_faqlang;

               $supports_catetories  = DB::table('support_categories')->select('id' , 'name')->get();
               $language_type  = DB::table('faq_languages')->get();
               $supports = DB::table('support_details as sd')
                ->select('sd.id' ,'sd.question' ,'sd.lang_id','sd.faq_for', 'support_categories.name as category')
                ->selectRaw("SUBSTRING(sd.answer, 1, 120) as answer")
                ->leftJoin('support_categories' , 'support_categories.id' , '=' , 'sd.cat_id')
                ->orderby('sd.id','DESC')
                ->where(function($query) use ($filter) {
                      if (!empty($filter) && $filter != 'all') {
                          $query -> where('sd.cat_id' , '=' , $filter);
                      }
                     })
                   ->where(function($query) use ($filter_faqfor) {
                      if (!empty($filter_faqfor)) {
                          $query -> where('sd.faq_for' , '=' , $filter_faqfor);
                      }
                     })  
                      ->where(function($query) use ($filter_faqlang) {
                      if (!empty($filter_faqlang)) {
                          $query -> where('sd.lang_id' , '=' , $filter_faqlang);
                      }
                     })  
                     
                ->paginate('10');

                return view('admin.supports.index',compact('supports' , 'supports_catetories', 'filter','filter_faqfor','filter_faqlang','language_type'))->with('i', ($supports->currentpage()-1)*$supports->perpage()+1); 
               
           } catch ( \Exception $e) {
               return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
           }   
    }

    public function search(Request $request){

        $this->authorize('index', Faq::class);

         try {

              $filter = '';
              $filter_faqfor = '';
              $filter_faqlang = '';
              $language_type  = DB::table('faq_languages')->get();

              $supports_catetories  = DB::table('support_categories')->select('id' , 'name')->get();

                $string  = $request->q;  
                $supports = DB::table('support_details as sd')
                    ->select('sd.id' ,'sd.question' ,'sd.lang_id','sd.faq_for', 'support_categories.name as category')
                    ->selectRaw("SUBSTRING(sd.answer, 1, 120) as answer")
                    ->leftJoin('support_categories' , 'support_categories.id' , '=' , 'sd.cat_id')
                    ->orderby('sd.id','DESC')
                    ->where(function($query) use ($string) {
                      if (empty($action)) {
                          $query -> whereRaw('LOWER(sd.question) like ?', '%'.strtolower($string).'%');
                          $query -> orWhereRaw('LOWER(sd.answer) like ?', '%'.strtolower($string).'%');
                      }
                     })
                    ->paginate('10');

                return view('admin.supports.index',compact('supports','string','action' , 'supports_catetories' , 'filter','filter_faqfor','filter_faqlang','language_type'))->with('i', ($supports->currentpage()-1)*$supports->perpage()+1);
               
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
         $this->authorize('create', Faq::class);

         try {
              
              $language_type  = DB::table('faq_languages')->get();
              $supports_catetories  = DB::table('support_categories')->select('id' , 'name')->get();
              return view('admin.supports.add',compact('supports_catetories','language_type'));
             
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
        $this->authorize('create', Faq::class);

        $request->validate([
            
            'question' => 'required|unique:support_details',
            'answer' => 'required',
            'lang_type' => 'required',
            'faq_for' => 'required',
        ]);
        
         try {

                 $support_data = array(
                    'cat_id' =>  "1",   
                    'question' => strtolower($request->question),
                    'answer' => $request->answer,
                    'lang_id' => $request->lang_type, 
                     'faq_for' => $request->faq_for,
                    'created_at' =>  \Carbon\Carbon::now(), 
                    'updated_at' => \Carbon\Carbon::now()
                    );

                $status = DB::table('support_details')->insertGetId($support_data);

                if($status){
                    return redirect('supports')->with('msg','Successfully added new FAQ')->with('color' , 'success');
                }
                else{
                    return Redirect::back()->with('msg','Failed to add FAQ')->with('color' , 'danger');
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
      $this->authorize('view', Faq::class);
        try {
              $support = DB::table('support_details as sd')->select('sd.id' , 'sd.cat_id as category_id' , 'sd.question' , 'sd.answer' , 'sc.name as category_name' , 'sd.created_at' , 'sd.updated_at' )
                             ->where('sd.id' , '=' , decrypt($request->id))
                             ->leftJoin('support_categories as sc' ,'sc.id' , '=' , 'sd.cat_id')
                             ->first();
                             
              return view('admin.supports.support',compact('support' ));
             
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
         $this->authorize('update', Faq::class);

         try {
              $support = DB::table('support_details as sd')->select('sd.id' , 'sd.cat_id as category_id' , 'sd.question' , 'sd.answer','sd.lang_id','sd.faq_for' , 'sc.name as category_name')
                             ->where('sd.id' , '=' , decrypt($request->id))
                             ->leftJoin('support_categories as sc' ,'sc.id' , '=' , 'sd.cat_id')
                             ->first();
              
              $language_type  = DB::table('faq_languages')->get();
                             
              $support_categories  = DB::table('support_categories')->where('id' , '!=' , $support->category_id)->select('id' , 'name')->get();

              return view('admin.supports.edit',compact('support_categories' ,'support','language_type' ));
             
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
           $this->authorize('update', Faq::class);

        $request->validate([
            
            'question' => 'required|unique:support_details,question,'.decrypt($request->id),
            'answer' => 'required',
            'updated_at' => \Carbon\Carbon::now(),
            'lang_type' => 'required',
            'faq_for' => 'required',
        ]);
        
         try {

                 $support_data = array(
                    'cat_id' =>  "1",   
                    'question' => strtolower($request->question),
                    'answer' => $request->answer,
                     'lang_id' => $request->lang_type, 
                     'faq_for' => $request->faq_for,
                    );

               DB::table('support_details')->where('id' , '=' , decrypt($request->id))->update($support_data);

               return redirect('supports')->with('msg','Successfully updated FAQ')->with('color' , 'success');
               
           } catch ( \Exception $e) {
               return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
           }  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request){  
        $this->authorize('delete', Faq::class);
        try {
            $status = DB::table('support_details')->where('id' , '=' , decrypt($request->id))->delete();
           
            if($status){
                    return redirect('supports')->with('msg','Successfully deleted')->with('color' , 'success');
                }
                else{
                    return Redirect::back()->with('msg','Failed to delete')->with('color' , 'danger');
                }

        }catch (Exception $e) {
            return Redirect::back()->with('msg','Something went wrong')->with('color' , 'warning');
        }
    }

    public function banners(){
        $data=Banner::limit(1)->get();
        if(isset($data[0])){
            $banner = $data[0];
        }else{
            $banner =array();
        }
        
        return view('admin.supports.banners',compact('banner'));
    }

    public function saveBanner(Request $request){
      $header_text = $request->header_text;
      $footer_text = $request->footer_text;
      $id = $request->id;
      $img = $request->img;
      $file = $request->file('userfile');

      if(!empty($file) && (count($file)>0)){
        if(file_exists('Admin/'.$img)){
          unlink('Admin/'.$img);
        }
        $image = time().$file->getClientOriginalName();
        $destinationPath="Admin";
        $file->move($destinationPath,$image);
      } 
  
      if($id!=''){
        $banner =  Banner::find($id);
      }else{
        $banner = New Banner;  
      }

      $banner->header_text = $header_text;
      $banner->footer_text = $footer_text;
      if(!empty($file) && (count($file)>0)){
        $banner->image = $image;
      }
      $banner->save();
      return redirect('banners')->with('msg','Banner Successfully Updated')->with('color' , 'success');
     
    }
}
