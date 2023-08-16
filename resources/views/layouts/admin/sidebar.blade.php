<div class="app-sidebar">
    <div class="logo">
        <a href="{{route('home')}}" class="logo-icon"><span class="logo-text">Neptune</span></a>
        <div class="sidebar-user-switcher user-activity-online">
            <a href="#">
                <img src="{{asset('assets/admin/images/avatars/avatar.png')}}">
                <span class="activity-indicator"></span>
                <span class="user-info-text">Chloe<br><span class="user-state-info">On a call</span></span>
            </a>
        </div>
    </div>
    <div class="app-menu">
        <ul class="accordion-menu">
            <li class="sidebar-title">
                Apps
            </li>
            <li class="{{Route::is('admin.index') ? 'open' : ''}}">
                <a href="{{route('admin.index')}}" class="{{Route::is('admin.index') ? 'active' : ''}}">
                    <i class="material-icons-two-tone">dashboard</i>
                    Dashboard
                </a>
            </li>

            <li class="{{ Route::is('article.index') || Route::is('article.create') || Route::is('article.pending-approval') || Route::is('article.comment.list') ? 'open' : '' }}">
                <a href="#">
                    <i class="material-icons-two-tone">article</i>
                    Məqalə Proseslər
                    <i class="material-icons has-sub-menu">keyboard_arrow_right</i>
                </a>
                <ul class="sub-menu" style="">
                    <li>
                        <a href="{{route('article.create')}}" class="{{ Route::is('article.create') ? 'active' : '' }}">Məqalə Əlavə Et</a>
                    </li>
                    <li>
                        <a href="{{route('article.index')}}" class="{{ Route::is('article.index') ? 'active' : '' }}">Məqalə Siyahısı</a>
                    </li>
                    <li>
                        <a href="{{route('article.comment.list')}}" class="{{ Route::is('article.comment.list') ? 'active' : '' }}">Kommentlər</a>
                    </li>
                    <li>
                        <a href="{{route('article.pending-approval')}}" class="{{ Route::is('article.pending-approval') ? 'active' : '' }}">Təsdiq Olunacaq Kommentlər</a>
                    </li>
                </ul>
            </li>

            <li class="{{ Route::is('category.index') || Route::is('category.create') ? 'open' : '' }}">
                <a href="#">
                    <i class="material-icons-two-tone">category</i>
                    Kateqoriya Proseslər
                    <i class="material-icons has-sub-menu">keyboard_arrow_right</i>
                </a>
                <ul class="sub-menu" style="">
                    <li>
                        <a href="{{route('category.create')}}" class="{{ Route::is('category.create') ? 'active' : '' }}">Kateqoriya Əlavə Et</a>
                    </li>
                    <li>
                        <a href="{{route('category.index')}}" class="{{ Route::is('category.index') ? 'active' : '' }}">Kateqoriya Siyahısı</a>
                    </li>
                </ul>
            </li>

            <li class="{{ Route::is('users.index') || Route::is('users.create') ? 'open' : '' }}">
                <a href="#">
                    <i class="material-icons-two-tone">person</i>
                    İstifadəçi Proseslər
                    <i class="material-icons has-sub-menu">keyboard_arrow_right</i>
                </a>
                <ul class="sub-menu" style="">
                    <li>
                        <a href="{{route('users.create')}}" class="{{ Route::is('users.create') ? 'active' : '' }}">İstifadəçi Əlavə Et</a>
                    </li>
                    <li>
                        <a href="{{route('users.index')}}" class="{{ Route::is('user.index') ? 'active' : '' }}">İstifadəçi Siyahısı</a>
                    </li>
                </ul>
            </li>

            <li class="{{ Route::is('settings') ? 'open' : '' }}">
                <a href="{{route('settings')}}">
                    <i class="material-icons-two-tone">settings</i>
                    Parametrlər
                </a>
            </li>

            <li class="{{ Route::is('dbLogs') ? 'open' : '' }}">
                <a href="{{route('dbLogs')}}">
                    <i class="material-icons-two-tone">settings</i>
                    Loglar
                </a>
            </li>

        </ul>
    </div>
</div>
