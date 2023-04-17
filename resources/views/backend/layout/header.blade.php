<div class="horizontal-menu">
  <nav class="navbar top-navbar">
    <div class="container">
      <div class="navbar-content">
        <a href="" class="navbar-brand" onclick="return false">
         <img src="{{ asset('assets/images/logo-sm.png')}}" width="50px">
        </a>
        {{-- <form class="search-form">
          <div class="input-group">
            <div class="input-group-text">
              <i data-feather="search"></i>
            </div>
            <input type="text" class="form-control" id="navbarForm" placeholder="Search here...">
          </div>
        </form> --}}
        <ul class="navbar-nav">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img class="wd-30 ht-30 rounded-circle" src="{{asset(\Auth::user()->image)}}" alt="profile">
            </a>
            <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
              <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
                <div class="mb-3">
                  <img class="wd-80 ht-80 rounded-circle" src="{{asset(\Auth::user()->image)}}" alt="">
                </div>
                <div class="text-center">
                  <p class="tx-16 fw-bolder">Admin</p>
                  <p class="tx-12 text-muted">{{\Auth::user()->email}}</p>
                </div>
              </div>
              <ul class="list-unstyled p-1">
                <li class="dropdown-item py-2">
                  <a href="{{ url('profile') }}" class="text-body ms-0">
                    <i class="me-2 icon-md" data-feather="user"></i>
                    <span>Profile</span>
                  </a>
                </li>
                <li class="dropdown-item py-2">
                  <a href="{{url('/logout')}}" class="text-body ms-0">
                    <i class="me-2 icon-md" data-feather="log-out"></i>
                    <span>Log Out</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="horizontal-menu-toggle">
          <i data-feather="menu"></i>					
        </button>
      </div>
    </div>
  </nav>
  <nav class="bottom-navbar">
    <div class="container"> 
      <ul class="nav page-navigation">
        <li class="nav-item {{ active_class(['/']) }}">
          <a class="nav-link" href="{{ url('/') }}">
            <i class="link-icon" data-feather="box"></i>
            <span class="menu-title">Dashboard</span>
          </a>
        </li>
        <li class="nav-item {{ active_class(['forms/*']) }}">
          <a href="#" class="nav-link" onclick="return false">
            <i class="link-icon" data-feather="users"></i>
            <span class="menu-title">Users</span>
            <i class="link-arrow"></i>
          </a>
          <div class="submenu">
            <ul class="submenu-item">
              <li class="nav-item"><a href="{{ url('/users') }}" class="nav-link {{ active_class(['users']) }}">Users</a></li>
              <li class="nav-item"><a href="{{ url('/users/create') }}" class="nav-link {{ active_class(['users/create']) }}">Add user</a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item {{ active_class(['forms/*']) }}">
          <a href="#" class="nav-link" onclick="return false">
            <i class="link-icon" data-feather="subscriptions"></i>
            <span class="menu-title">Subscriptions</span>
            <i class="link-arrow"></i>
          </a>
          <div class="submenu">
            <ul class="submenu-item">
              <li class="nav-item"><a href="{{ url('/subscriptions') }}" class="nav-link {{ active_class(['subscriptions']) }}">Subscriptions</a></li>
              <li class="nav-item"><a href="{{ url('/subscriptions/create') }}" class="nav-link {{ active_class(['subscriptions/create']) }}">Add subscription</a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item {{ active_class(['forms/*']) }}">
          <a href="#" class="nav-link" onclick="return false">
            <i class="link-icon" data-feather="termsconditions"></i>
            <span class="menu-title">Terms & Conditions</span>
            <i class="link-arrow"></i>
          </a>
          <div class="submenu">
            <ul class="submenu-item">
              <li class="nav-item"><a href="{{ url('/termsconditions') }}" class="nav-link {{ active_class(['termsconditions']) }}">Terms & Conditions</a></li>
              <li class="nav-item"><a href="{{ url('/termsconditions/create') }}" class="nav-link {{ active_class(['termsconditions/create']) }}">Add New Page</a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item {{ active_class(['forms/*']) }}">
          <a href="#" class="nav-link" onclick="return false">
            <i class="link-icon" data-feather="subscriptions"></i>
            <span class="menu-title">Activity Types</span>
            <i class="link-arrow"></i>
          </a>
          <div class="submenu">
            <ul class="submenu-item">
              <li class="nav-item"><a href="{{ url('/activity-type') }}" class="nav-link {{ active_class(['activity-type']) }}">Activity Types</a></li>
              <li class="nav-item"><a href="{{ url('/activity-type/create') }}" class="nav-link {{ active_class(['activity-type/create']) }}">Add Activity Type</a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item {{ active_class(['companies/*']) }}">
          <a href="#" class="nav-link" onclick="return false">
            <i class="link-icon" data-feather="subscriptions"></i>
            <span class="menu-title">Companies</span>
            <i class="link-arrow"></i>
          </a>
          <div class="submenu">
            <ul class="submenu-item">
              <li class="nav-item"><a href="{{ url('/companies') }}" class="nav-link {{ active_class(['companies']) }}">Companies</a></li>
              <li class="nav-item"><a href="{{ url('/companies/create') }}" class="nav-link {{ active_class(['companies/create']) }}">Add Company</a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item {{ active_class(['forms/*']) }}">
          <a href="#" class="nav-link" onclick="return false">
            <i class="link-icon" data-feather="subscriptions"></i>
            <span class="menu-title">Default Roles</span>
            <i class="link-arrow"></i>
          </a>
          <div class="submenu">
            <ul class="submenu-item">
              <li class="nav-item"><a href="{{ url('/roles') }}" class="nav-link {{ active_class(['roles']) }}">Roles</a></li>
              <li class="nav-item"><a href="{{ url('/roles/create') }}" class="nav-link {{ active_class(['roles/create']) }}">Add Role</a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item {{ active_class(['settings/*']) }}">
          <a href="#" class="nav-link" onclick="return false">
            <i class="link-icon" data-feather="subscriptions"></i>
            <span class="menu-title">Settings</span>
            <i class="link-arrow"></i>
          </a>
          <div class="submenu">
            <ul class="submenu-item">
              <li class="nav-item"><a href="{{ url('/settings/smtp') }}" class="nav-link {{ active_class(['settings/smtp']) }}">SMTP</a></li>
              <li class="nav-item"><a href="{{ url('/settings/subscription-trial') }}" class="nav-link {{ active_class(['settings/subscription-trial']) }}">Subscription Trial Days</a></li>
            </ul>
          </div>
        </li>
      </ul>
    </div>
  </nav>
</div>
