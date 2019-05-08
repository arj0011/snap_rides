<?php

namespace App\Policies;

use App\User;
use DB;
use Illuminate\Auth\Access\HandlesAuthorization;

class SmsPolicy
{
    use HandlesAuthorization;

     protected $var_create     = 52;

      /**
     * Determine whether the user can create plans.
     *
     * @param  \App\User  $user
     * @return mixed
     */
      
    public function create(User $user)
    {
        return $this->getPermission($user,$this->var_create); // create plans
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
