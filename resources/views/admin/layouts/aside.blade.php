 <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          @if (!empty(Auth::user()->profile_image) && file_exists('Admin/profileImage/'.Auth::user()->profile_image))
          <img style="background: white;" src="{{ asset('Admin/profileImage/'.Auth::user()->profile_image) }}" class="img-circle" alt="User Image">
          @else
           <img style="background: white;" src="{{ asset('Admin/profileImage/unknown.png') }}" class="img-circle" alt="User Image not available ">
          @endif
        </div>
        <div class="pull-left info">
          <p>{{Auth::user()->username }}</p>
          <a href="{{ route('home') }}"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">

        <li >
          <a href="{{ route('dashboard') }}" class="{{ Request::is('admin/dashboard') ? 'inactive' : '' }}">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
          </a>
        </li>

        @can( 'index' , App\Role::class)
        <li>
          <a href="{{ route('role/roles') }}" class="{{ Request::is('roles') || Request::is('search-role') ? 'inactive' : '' }}">
            <i class="fa fa-users" aria-hidden="true"></i></i><span>&nbsp;Roles</span>
          </a>
        </li>
        @endcan
        @can( 'index' , App\Permission::class)
        <li>
          <a href="{{ route('permission/permissions') }}" >
            <i class="fa fa-users" aria-hidden="true"></i></i><span>&nbsp;Permissions</span>
          </a>
        </li>
         @endcan
        @can( 'index' , App\User::class)
          <li>
            <a href="{{ route('user/users') }}" class="{{ Request::is('users') || Request::is('search-user') ? 'inactive' : '' }}">
              <i class="fa fa-users" aria-hidden="true"></i></i><span>&nbsp;Dispatcher</span>
            </a>
          </li>
        @endcan
        @can( 'index' , App\Customer::class)
        <li>
          <a href="{{ route('rider/riders') }}" class="{{ Request::is('riders') || Request::is('search-rider') ? 'inactive' : '' }}">
            <i class="fa fa-users" aria-hidden="true"></i></i><span>&nbsp;Riders</span>
          </a>
        </li>
        @endcan
        @can( 'index' , App\Driver::class)
          <li>
          <a href="{{ route('driver/drivers') }}" class="{{ Request::is('drivers') || Request::is('search-driver') ? 'inactive' : '' }}">
            <i><img src="{{ asset('Admin/icon/driver.svg') }}" width="20" height="15"></i><span>&nbsp;Drivers</span>
          </a>
        </li>
        <li>
          <a href="{{ route('driver/tracking') }}" class="{{ Request::is('tracking') ? 'inactive' : '' }}">
            <i><img src="{{ asset('Admin/icon/driver.svg') }}" width="20" height="15"></i><span>&nbsp;Realtime Tracking</span>
          </a>
        </li>
        @endcan
        @can( 'index' , App\Category::class)
         <li>
          <a href="{{ route('category/categories') }}" class="{{ Request::is('categories') || Request::is('search-category') ? 'inactive' : '' }}">  
            <i><img src="{{ asset('Admin/icon/vehicle-type-icon.png') }}" width="20" height="15"></i><span>&nbsp;Vehicle Type</span>
          </a>
        </li>
        @endcan

         @can( 'index' , App\Booking::class)
        <li>
          <a href="{{ route('booking/bookings') }}" class="{{ Request::is('bookings') || Request::is('search-booking') ? 'inactive' : '' }}">
            <i><img src="{{ asset('Admin/icon/booking.png') }}" width="20" height="15"></i><span>&nbsp;Bookings</span>
          </a>
        </li>
        @endcan 
       {{--  @can( 'index' , App\Plan::class)
        <li>
          <a href="{{ route('plan/plans') }}">
            <i class="fa fa-book" aria-hidden="true"></i><span>&nbsp;Plans</span>
          </a>
        </li>
         @endcan --}}
        @can( 'index' , App\Setting::class)
        <li>
          <a href="{{ route('setting/settings') }}" class="{{ Request::is('settings') ? 'inactive' : '' }}">
            <i class="fa fa-gears"></i><span>&nbsp;Settings</span>
          </a>
        </li>
        @endcan
         @can( 'create' , App\Notification::class)
        <li>
          <a href="{{ route('notification/create') }}" class="{{ Request::is('send-notification') ? 'inactive' : '' }}">
            <i class="fa fa-book" aria-hidden="true"></i><span>&nbsp;Notification</span>
          </a>
        </li>
         @endcan
         {{-- @can( 'create' , App\Sms::class)
        <li>
          <a href="{{ route('sms/create') }}">
            <i class="fa fa-book" aria-hidden="true"></i><span>&nbsp;Sms</span>
          </a>
        </li>
         @endcan --}}
        {{--  @can( 'index' , App\Faq::class)
        <li>
          <a href="{{ route('support/supports') }}">
            <i class="fa fa-book" aria-hidden="true"></i><span>&nbsp;FAQ</span>
          </a>
        </li>
         @endcan --}}
      
        @can( 'index' , App\OfferCode::class)
         <li>
          <a href="<?php echo url('/') ?>/offers/promo" class="{{ Request::is('offers/promo') ? 'inactive' : '' }}">
            <i class="fa fa-folder" aria-hidden="true"></i><span>&nbsp;Offers</span>
          </a>
        </li>
         @endcan

        @can( 'index' , App\Booking::class)
        <li>
          <a href="{{ route('report/reports') }}" class="{{ Request::is('reports') || Request::is('search-report') ? 'inactive' : '' }}">
            <i class="fa fa-pie-chart"></i><span>&nbsp;Reports</span>
          </a>
        </li>
        @endcan

        @can( 'index' , App\Payment::class)
        <li><a href="{{ route('payment/payments') }}" class="{{ Request::is('payments') || Request::is('search-payment') ? 'inactive' : '' }}">
            <i class="fa fa-credit-card"></i><span>&nbsp;Payment</span>
          </a>
        </li>
        @endcan
      
      </ul>
    </section>
    <!-- /.sidebar -->

<style type="text/css">
  .inactive{color: #fff!important;background: #1e282c;}
</style>
