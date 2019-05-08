  <!-- Logo -->
    <a href="../../index2.html" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>SnapRides</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>SnapRides</b> App</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            @if (!empty(Auth::user()->profile_image) && file_exists('Admin/profileImage/'.Auth::user()->profile_image))
              <img src="{{ asset('Admin/profileImage/'.Auth::user()->profile_image) }}" class="user-image" alt="User Image">
            @else
              <img src="{{ asset('Admin/profileImage/unknown.png') }}" class="user-image" alt="User Image not available">
            @endif
              <span class="hidden-xs"> {{ Auth::user()->username }}</span>
            </a>
            <ul class="dropdown-menu"  style="width: 80px;">
              <li>
                  <a href="{{route('profile/show')}}" style="color:#323944">Profile</a>
              </li>
              <li>
                  <a href="{{ route('change-password') }}" style="color:#323944">Change Password</a>
              </li>
              <li>
                  <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();" style="color:#323944">
                                        {{ __('Logout') }}
                  </a>
                  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                      @csrf
                  </form>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>

