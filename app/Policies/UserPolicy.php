<?php

namespace App\Policies;

use App\User;
use DB;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    protected $var_index      = 1;
    protected $var_create     = 2;
    protected $var_update     = 3;
    protected $var_view       = 5;
    protected $var_delete     = 5;
    protected $var_set_status = 6;
     
     /**
     * Determine whether the user can list the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function index(User $user)
    {
       return $this->getPermission($user,$this->var_index); // users list
    }
    


    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function view(User $user)
    {
       return $this->getPermission($user,$this->var_view); // users list
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    { 
      return $this->getPermission($user,$this->var_create); // create users 
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function update(User $user)
    {
        return $this->getPermission($user,$this->var_update); // edit users
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $user)
    {
        return $this->getPermission($user,$this->var_delete); // delete users
    }

    public function setStatus(User $user)
    {
        return $this->getPermission($user,$this->var_set_status); // delete users
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
