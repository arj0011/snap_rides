<?php

namespace App\Policies;

use App\User;
use DB;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingPolicy
{
    use HandlesAuthorization;

    protected $var_index      = 18;
    protected $var_view       = 20;
    protected $var_delete     = 19;
    protected $var_invoice    = 21;


    /**
     * Determine whether the user can view the booking list.
     *
     * @param  \App\User  $user
     * @param  \App\Booking  $booking
     * @return mixed
     */
    public function index(User $user)
    {
        return $this->getPermission($user,$this->var_index); // bookings list
    }

    /**
     * Determine whether the user can view the booking.
     *
     * @param  \App\User  $user
     * @param  \App\Booking  $booking
     * @return mixed
     */
    public function invoice(User $user)
    {
        return $this->getPermission($user,$this->var_invoice); // invoice bookings
    }

      /**
     * Determine whether the user can delete the booking.
     *
     * @param  \App\User  $user
     * @param  \App\Booking  $booking
     * @return mixed
     */
    public function delete(User $user)
    {
        return $this->getPermission($user,$this->var_delete); // delete bookings
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
