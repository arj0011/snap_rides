<?php

namespace App\Policies;

use App\User;
use DB;
use Illuminate\Auth\Access\HandlesAuthorization;

class RiderPolicy
{
    use HandlesAuthorization;

        protected $var_index      = 15;
        protected $var_view       = 17;
        protected $var_set_status = 16;
        protected $var_destroy = 56;
    

    /**
     * Determine whether the user can view the rider list.
     *
     * @param  \App\User  $user
     * @param  \App\Rider  $rider
     * @return mixed
     */
    public function index(User $user)
    {
       return $this->getPermission($user,$this->var_index); // riders list
    }

    /**
     * Determine whether the user can view the rider.
     *
     * @param  \App\User  $user
     * @param  \App\Rider  $rider
     * @return mixed
     */
    public function view(User $user)
    {
       return $this->getPermission($user,$this->var_view);  // view riders
    }

    public function setStatus(User $user)
    {
        return $this->getPermission($user,$this->var_set_status); // set  riders status
    }

    public function destroy(User $user)
    {
        return $this->getPermission($user,$this->var_destroy); // destroy  riders
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
