<div class="horizontal-menu">
  <nav class="navbar top-navbar">
    <div class="container">
      <div class="navbar-content">
        <a href="" class="navbar-brand" onclick="return false">
          Billing<span>System</span>
        </a>
        <form class="search-form">
          <div class="input-group">
            <div class="input-group-text">
              <i data-feather="search"></i>
            </div>
            <input type="text" class="form-control" id="navbarForm" placeholder="Search here...">
          </div>
        </form>
        <ul class="navbar-nav">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img class="wd-30 ht-30 rounded-circle" src="{{ url('https://via.placeholder.com/30x30') }}" alt="profile">
            </a>
            <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
              <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
                <div class="mb-3">
                  <img class="wd-80 ht-80 rounded-circle" src="{{ url('https://via.placeholder.com/80x80') }}" alt="">
                </div>
                <div class="text-center">
                  <p class="tx-16 fw-bolder">Admin</p>
                  <p class="tx-12 text-muted">pranav.bhardwaj@codingcafe.website</p>
                </div>
              </div>
              <ul class="list-unstyled p-1">
                <li class="dropdown-item py-2">
                  <a href="{{ url('/general/profile') }}" class="text-body ms-0">
                    <i class="me-2 icon-md" data-feather="user"></i>
                    <span>Profile</span>
                  </a>
                </li>
                <li class="dropdown-item py-2">
                  <a href="javascript:;" class="text-body ms-0">
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
          <a href="#" class="nav-link">
            <i class="link-icon" data-feather="inbox"></i>
            <span class="menu-title">Users</span>
            <i class="link-arrow"></i>
          </a>
          <div class="submenu">
            <ul class="submenu-item">
              <li class="nav-item"><a href="{{ url('/all-users') }}" class="nav-link {{ active_class(['all-users']) }}">All Users</a></li>
              <li class="nav-item"><a href="{{ url('/add-user') }}" class="nav-link {{ active_class(['add-user']) }}">Add user</a></li>
            </ul>
          </div>
        </li>
      </ul>
    </div>
  </nav>
</div>