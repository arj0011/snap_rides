<?php

namespace App\Policies;

use App\User;
use DB;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    protected $var_index      = 33;
    protected $var_create     = 34;
    protected $var_update     = 35;
    protected $var_view       = 37;
    protected $var_delete     = 36;
    protected $var_set_status = 38;


      /**
     * Determine whether the user can view the category list.
     *
     * @param  \App\User  $user
     * @param  \App\Category  $category
     * @return mixed
     */
    public function index(User $user)
    {
        return $this->getPermission($user,$this->var_index); // vehicle categories list
    }

    /**
     * Determine whether the user can view the category.
     *
     * @param  \App\User  $user
     * @param  \App\Category  $category
     * @return mixed
     */
    public function view(User $user)
    {
        return $this->getPermission($user,$this->var_view); // veiw vehicle categories 
    }

    /**
     * Determine whether the user can create categories.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
         return $this->getPermission($user,$this->var_create); // create vehicle categories
    }

    /**
     * Determine whether the user can update the category.
     *
     * @param  \App\User  $user
     * @param  \App\Category  $category
     * @return mixed
     */
    public function update(User $user)
    {
         return $this->getPermission($user,$this->var_update); // update vehicle categories
    }

    /**
     * Determine whether the user can delete the category.
     *
     * @param  \App\User  $user
     * @param  \App\Category  $category
     * @return mixed
     */
    public function delete(User $user)
    {
        return $this->getPermission($user,$this->var_delete); // delete vehicle categories
    }

    public function setStatus(User $user)
    {
        return $this->getPermission($user,$this->var_set_status); // set  vehicle categories state
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
