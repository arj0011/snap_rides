<?php

namespace App\Policies;

use App\User;
use DB;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

        protected $var_index      = 27;
        protected $var_create     = 28;
        protected $var_update     = 29;
        protected $var_view       = 30;
        protected $var_delete     = 31;
        protected $var_set_status = 32;

   
    /**
     * Determine whether the user can view the role list.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function index(User $user)
    {
       return $this->getPermission($user,$this->var_index);  // roles list
    }

    /**
     * Determine whether the user can view the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function view(User $user)
    {
      return $this->getPermission($user,$this->var_view);  // view roles
    }

    /**
     * Determine whether the user can create roles.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
      return $this->getPermission($user,$this->var_create);  // create roles
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function update(User $user)
    {
      return $this->getPermission($user,$this->var_update);  // update roles
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function delete(User $user)
    {
       return $this->getPermission($user,$this->var_delete); // delete roles
    }
    
    public function setStatus(User $user)
    {
        return $this->getPermission($user,$this->var_set_status); // set  role  status
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
