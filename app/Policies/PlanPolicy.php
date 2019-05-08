<?php

namespace App\Policies;

use App\User;
use DB;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlanPolicy
{
    use HandlesAuthorization;

        protected $var_index      = 39;
        protected $var_create     = 40;
        protected $var_update     = 41;
        protected $var_delete     = 42;
        protected $var_view       = 43;
        protected $var_set_status = 44;

    /**
     * Determine whether the user can view the plan list.
     *
     * @param  \App\User  $user
     * @param  \App\Plan  $plan
     * @return mixed
     */
    public function index(User $user)
    {
        return $this->getPermission($user,$this->var_index); // plans list
    }

    /**
     * Determine whether the user can view the plan.
     *
     * @param  \App\User  $user
     * @param  \App\Plan  $plan
     * @return mixed
     */
    public function view(User $user)
    {
        return $this->getPermission($user,$this->var_view); // view plans
    }

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

    /**
     * Determine whether the user can update the plan.
     *
     * @param  \App\User  $user
     * @param  \App\Plan  $plan
     * @return mixed
     */
    public function update(User $user)
    {
       return $this->getPermission($user,$this->var_update);  // update plans
    }

    /**
     * Determine whether the user can delete the plan.
     *
     * @param  \App\User  $user
     * @param  \App\Plan  $plan
     * @return mixed
     */
    public function delete(User $user)
    {
       return $this->getPermission($user,$this->var_delete);  // delete plans
    }

    public function setStatus(User $user)
    {
        return $this->getPermission($user,$this->var_set_status); // set  plans status
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
