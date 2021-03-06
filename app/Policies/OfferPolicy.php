<?php

namespace App\Policies;

use App\User;
use DB;
use Illuminate\Auth\Access\HandlesAuthorization;

class OfferPolicy
{
    use HandlesAuthorization;

    protected $var_add      =  53;
    protected $var_update   =  54;
    protected $var_index    =  55;

    public function add(User $user)
    {
       return $this->getPermission($user,$this->var_add);  // update the setting
    }


    /**
     * Determine whether the user can update the setting.
     *
     * @param  \App\User  $user
     * @param  \App\setting  $setting
     * @return mixed
     */
    public function update(User $user)
    {
       return $this->getPermission($user,$this->var_update);  // update the setting
    }


    /**
     * Determine whether the user can view the setting.
     *
     * @param  \App\User  $user
     * @param  \App\setting  $setting
     * @return mixed
     */
    public function index(User $user)
    {
       return $this->getPermission($user,$this->var_index);  // view the settings page
    }


    protected function getPermission($user,$p_id)
    {   
      
        $permissions = DB::table('permission_role as p_role')
                             ->select('p_role.role_id' , 'p_role.permission_id')
                             ->where('role_id' , '=' , $user->is_role)
                             ->get();

         foreach ($permissions as  $permission) {
                if($permission->permission_id == $p_id){
                     return true;
                }          
         }

        return false;
    }
}
