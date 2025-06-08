<!-- Sidebar -->
<div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-3 mb-3">
        @php
        $id_peran = session()->get('login.peran.id_peran');
        $id_pengguna = session()->get('login.peran.id_pengguna');
        $nm_peran = DB::table('man_akses.peran')->where('id_peran', $id_peran)->first();
        $nm_pengguna = DB::table('man_akses.pengguna')->where('id_pengguna', $id_pengguna)->first();
        @endphp
        <div class="user-panel d-flex text-light">
            <div class="image">
                <img src="https://akses.unila.ac.id/images/blank-profile.png" alt="User Image">
            </div>
            <div class="info">
                <span class="d-block font-weight-bold">
                    {{$nm_pengguna->nm_pengguna}}
                </span>
                <span class="text-sm">
                    {{$nm_peran->nm_peran }}
                </span>
            </div>

        </div>
    </nav>

    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ AktifMenu('dashboard') }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>

            @if(\Session::has('menu_user_manajemen'))
            @foreach(Session::get('menu_user_manajemen') AS $menu_name_1 => $menu_route_1)
            @if($menu_route_1[0]==true)
            <li class="nav-item has-treeview
                <?php
                $state = 0;
                foreach ($menu_route_1[1] as $menu_name_2 => $menu_route_2) {
                    echo ((AktifMenu($menu_route_2[1], 2) == 'active') ? 'menu-open' : '');
                    ($state == 0 ? ((AktifMenu($menu_route_2[1], 2) == 'active') ? $state = 1 : $state = 0) : $state = 1);
                }
                ?>
                            ">
                <a href="#" class="nav-link {{ $state==1?'active':'' }}">
                    <i class="nav-icon {{ $menu_route_1[2] }}"></i>
                    <p>{{ $menu_name_1 }}</p> <i class="right fas fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                    @foreach($menu_route_1[1] AS $menu_name_2 => $menu_route_2)
                    <li class="nav-item"><a href="{{ \Route::has($menu_route_2[1])?route($menu_route_2[1]):'' }}" class="nav-link {{ AktifMenu($menu_route_2[1],2) }}"><i class="nav-icon fa fa-circle-o"></i> <span>{{ $menu_name_2 }}</span></a></li>
                    @endforeach
                </ul>
            </li>
            @else
            <li class="nav-item">
                <a href="{{ \Route::has($menu_route_1[1])?route($menu_route_1[1]):'' }}" class="nav-link {{ AktifMenu($menu_route_1[1]) }}"><i class="nav-icon {{ $menu_route_1[2] }}"></i>
                    <p>{{ $menu_name_1 }}</p>
                </a>
            </li>
            @endif
            @endforeach
            @endif
            @if(env('APP_DEBUG')==true)
            <li class="nav-item">
                <a href="{{ route('ubah_peran.update',Crypt::encrypt(session('login.peran.id_peran'))) }}" class="nav-link"><i class="nav-icon fas fa-refresh"></i>
                    <p>Refresh Menu</p>
                </a>
            </li>
            @endif
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->