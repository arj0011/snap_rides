<?php

namespace App\Policies;

use App\User;
use DB;
use Illuminate\Auth\Access\HandlesAuthorization;

class DriverPolicy
{
    use HandlesAuthorization;

    protected $var_index      =  7;
    protected $var_create     =  8;
    protected $var_update     =  9;
    protected $var_view       = 11;
    protected $var_delete     = 10;
    protected $var_set_status = 12;
    protected $var_approved   = 13;
    protected $var_declined   = 14;

    /**
     * Determine whether the user can view the driver list.
     *
     * @param  \App\User  $user
     * @param  \App\Driver  $driver
     * @return mixed
     */
    public function index(User $user)
    {
       return $this->getPermission($user,$this->var_index); // drivers list
    }

    /**
     * Determine whether the user can view the driver.
     *
     * @param  \App\User  $user
     * @param  \App\Driver  $driver
     * @return mixed
     */
    public function view(User $user)
    {
       return $this->getPermission($user,$this->var_view); // view drivers
    }

    /**
     * Determine whether the user can create drivers.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
       return $this->getPermission($user,$this->var_create); // create drivers
    }

    /**
     * Determine whether the user can update the driver.
     *
     * @param  \App\User  $user
     * @param  \App\Driver  $driver
     * @return mixed
     */
    public function update(User $user)
    {
      return $this->getPermission($user,$this->var_update);  // update drivers
    }

    /**
     * Determine whether the user can delete the driver.
     *
     * @param  \App\User  $user
     * @param  \App\Driver  $driver
     * @return mixed
     */
    public function delete(User $user)
    {
      return $this->getPermission($user,$this->var_delete);  // delete drivers
    }

    public function setStatus(User $user)
    {
        return $this->getPermission($user,$this->var_set_status); // set  drivers state
    }

    public function approved(User $user){
        return $this->getPermission($user,$this->var_approved);   // drivers approved
    }

    public function declined(User $user){
        return $this->getPermission($user,$this->var_declined); // drivers declined
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
