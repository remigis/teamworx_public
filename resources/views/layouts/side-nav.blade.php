<div class="modal right fade" id="sideNav" aria-hidden="true"
     tabindex="-1" role="dialog" aria-labelledby="myModal2g">
    <div class="modal-dialog side_nav" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @guest()
                @include('layouts.side-nav-login-form')
            @else
                <div class="side_nav_user_card text-center">
                    <img class="rounded mx-auto d-block mt-3" width="100px" height="100px"
                         src="/storage/{{Auth::user()->avatar->path}}" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title">{{Auth::user()->name}}</h5>
                    </div>
                </div>
                <div class="container side_nav_link_container">
                    <div class="row p-3">
                        <div class="col-4 p-1 menu_link_box"><a href="#"
                                                                class="btn btn-outline-primary side_menu_link disabled"
                                                                role="button" aria-pressed="true">
                                <div class="text">Warehouse</div>
                                <div class="icon"><i class="fa-solid fa-lg fa-warehouse"></i></div>
                            </a></div>
                        <div class="col-4 p-1 menu_link_box"><a href="{{route('calculatorView')}}"
                                                                class="btn btn-outline-primary side_menu_link"
                                                                role="button" aria-pressed="true">
                                <div class="text">Calculator</div>
                                <div class="icon"><i class="fa-solid fa-lg fa-calculator"></i></div>
                            </a></div>
                        <div class="col-4 p-1 menu_link_box"><a href="{{route('newItemScan')}}"
                                                                class="btn btn-outline-primary side_menu_link"
                                                                role="button" aria-pressed="true">
                                <div class="text">Scan</div>
                                <div class="icon"><i class="fas fa-lg fa-barcode"></i></div>
                            </a></div>
                        <div class="col-4 p-1 menu_link_box"><a href="{{route('boxBuildMenu')}}"
                                                                class="btn btn-outline-primary side_menu_link"
                                                                role="button" aria-pressed="true">
                                <div class="text">Box Build</div>
                                <div class="icon"><i class="fa-solid fa-lg fa-boxes-stacked"></i></div>
                            </a></div>
                        <div class="col-4 p-1 menu_link_box"><a href="{{route('gateOpener')}}"
                                                                class="btn btn-outline-primary side_menu_link"
                                                                role="button" aria-pressed="true">
                                <div class="text">Gates</div>
                                <div class="icon"><i class="fas fa-lg fa-torii-gate"></i></div>
                            </a></div>
                        <div class="col-4 p-1 menu_link_box"><a href="#"
                                                                class="btn btn-outline-primary side_menu_link disabled"
                                                                role="button" aria-pressed="true">
                                <div class="text">Statistics</div>
                                <div class="icon"><i class="fa-solid fa-lg fa-chart-area"></i></div>
                            </a></div>
                        <div class="col-4 p-1 menu_link_box"><a href="{{route('lists')}}"
                                                                class="btn btn-outline-primary side_menu_link"
                                                                role="button" aria-pressed="true">
                                <div class="text">Lists</div>
                                <div class="icon"><i class="fa-solid fa-clipboard-list"></i></div>
                            </a></div>
                        <div class="col-4 p-1 menu_link_box"><a href="{{route('itemTransfer')}}"
                                                                class="btn btn-outline-primary side_menu_link"
                                                                role="button" aria-pressed="true">
                                <div class="text">Transfer</div>
                                <div class="icon"><i class="fa-solid fa-right-left"></i></div>
                            </a></div>
                        <div class="col-4 p-1 menu_link_box"><a href="{{route('printer')}}"
                                                                class="btn btn-outline-primary side_menu_link"
                                                                role="button" aria-pressed="true">
                                <div class="text">Printer</div>
                                <div class="icon"><i class="fa-solid fa-print"></i></div>
                            </a></div>
                    </div>
                </div>
                <div class="container">
                    <div class="bottom-buttons-height">
                        <div class="row h-100">
                            <a class="col-4 btn btn-primary h-100"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                    class="fa-solid fa-right-from-bracket"></i> <span
                                    class="hide-text">Logout</span></a>
                            <a class="col-4 btn btn-primary h-100" href="{{route('home')}}"><i
                                    class="fa-solid fa-gear"></i> <span class="hide-text">Settings</span></a>
                            @privilege(\App\Utilities\PrivilegeUtilities::PRIVILEGE_TO_ACCESS_ADMIN_PANEL)
                            <a class="col-2 btn btn-primary h-100" href="{{ route('adminPanel') }}"><i
                                    class="fa-solid fa-user-tie"></i> <span
                                    class="hide-text"></span></a>
                            <a class="col-2 btn btn-primary h-100"><i class="fa-solid fa-wrench"></i> <span
                                    class="hide-text"></span></a>
                            @elseprivilege
                            <a class="col-4 btn btn-primary h-100"><i class="fa-solid fa-wrench"></i> <span
                                    class="hide-text">Change Log</span></a>
                            @endprivilege

                        </div>
                    </div>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>

                </div>
            @endguest
        </div>
    </div>
</div>
