<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use DB;

class NotificationPolicy
{
    use HandlesAuthorization;

        protected $var_index      = '';
        protected $var_create     = 47;
        protected $var_update     = '';
        protected $var_delete     = '';
        protected $var_view       = '';
        protected $var_set_status = '';

    /**
     * Determine whether the user can view the notification list.
     *
     * @param  \App\User  $user
     * @param  \App\notification  $notification
     * @return mixed
     */
    public function index(User $user)
    {
        return $this->getPermission($user,$this->var_index); // notifications list
    }

    /**
     * Determine whether the user can view the notification.
     *
     * @param  \App\User  $user
     * @param  \App\notification  $notification
     * @return mixed
     */
    public function view(User $user)
    {
        return $this->getPermission($user,$this->var_view); // view notifications
    }

    /**
     * Determine whether the user can create notifications.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {  
        return $this->getPermission($user,$this->var_create); // create notifications
    }

    /**
     * Determine whether the user can update the notification.
     *
     * @param  \App\User  $user
     * @param  \App\notification  $notification
     * @return mixed
     */
    public function update(User $user)
    {
       return $this->getPermission($user,$this->var_update);  // update notifications
    }

    /**
     * Determine whether the user can delete the notification.
     *
     * @param  \App\User  $user
     * @param  \App\notification  $notification
     * @return mixed
     */
    public function delete(User $user)
    {
       return $this->getPermission($user,$this->var_delete);  // delete notifications
    }

    public function setStatus(User $user)
    {
        return $this->getPermission($user,$this->var_set_status); // set  notifications status
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
