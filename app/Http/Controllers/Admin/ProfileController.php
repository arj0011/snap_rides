<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\User;
use DB;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    { 
        
        try {
                $data = DB::table('users as u')->select('u.*','roles.name as role_name')->leftJoin('roles' , 'roles.id' , '=' , 'u.is_role')->where('u.id' , '=' , Auth::id())->first();
 
                if(empty($data->profile_image) || !file_exists('Admin/profileImage/'.$data->profile_image)){
                       $data->profile_image = 'unknown.png';
                     }
                     return view('admin.profile.profile',compact('data'));
            
        } catch ( \Exception $e) {
                return Redirect::back()->with('msg','Something went wrong!')->with('color' , 'warning');
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
        try {
                $data = DB::table('users')->where('id' , '=' , Auth::id())->first();
                return view('admin.profile.edit',compact('data'));
        } catch ( \Exception $e) {
                return Redirect::back()->with('msg','Something went wrong!')->with('color' , 'warning');
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
            $validatedData = $request->validate([
            'username' => 'required|max:50|min:4|unique:users,username,'.Auth::id(), 
            'first_name'   => 'required|min:2|max:50',
            'last_name'    => 'required|min:2|max:50',
            'mobile'       => 'required|digits_between:7,19|unique:users,mobile,'.Auth::id(),
            'email'        => 'required|unique:users,email,'.Auth::id(),
            'profile_image' =>  'image|mimes:jpeg,jpg,png'
            ]);
            
        try {

                $user_data = array(
                    'username' => $request->username,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'mobile'   => $request->mobile
                    );

                if ($request->hasFile('profile_image')) {
                     $imageName = str_random(10).'-'.time().'.'.request()->profile_image->getClientOriginalExtension(); 
                    request()->profile_image->move('Admin/profileImage', $imageName);
                    $user_data['profile_image'] = $imageName;
                }
                
                $update_status = DB::table('users')
                    ->where('id', '=', Auth::id())
                    ->update($user_data);

                if($update_status){
                    if( $request->hasFile('profile_image') && file_exists('Admin/profileImage/'.$request->old_profile_image)){
                        unlink('Admin/profileImage/'.$request->old_profile_image);
                    }
                    return redirect('profile')->with('msg','Successfully updated profile!')->with('color' , 'success');
                }
                else{
                  if ($request->hasFile('profile_image') && file_exists('Admin/profileImage/'.$imageName) ) {
                    if(!empty($images));
                       unlink('Admin/profileImage/'.$imageName);
                    }
                    return Redirect::back()->with('msg','Failed to update profile!')->with('color' , 'danger');
                }
            
        } catch ( \Exception $e) {
                    return Redirect::back()->with('msg','Something went wrong!')->with('color' , 'warning');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
