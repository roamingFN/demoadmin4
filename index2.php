<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    
    <title>Order2Easy</title>
    
    <link href="https://fonts.googleapis.com/css?family=Istok+Web" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.3.2/css/mdb.min.css" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
</head>
<body>
    <section id="wrapper">
        <header id="header">
            <!-- Start header left -->
            <div class="header-left">
                <!-- Start offcanvas left: This menu will take position at the top of template header (mobile only). Make sure that only #header have the `position: relative`, or it may cause unwanted behavior -->
                <div class="navbar-minimize-mobile left">
                    <div data-activates="slide-out" class="navbar-brand button-collapse waves-effect waves-light"><i
                            class="fa fa-bars"></i></div>
                </div>
                <!--/ End offcanvas left -->

                <!-- Start navbar header -->
                <div class="navbar-header">

                    <!-- Start brand -->
                    <a class="navbar-brand waves-effect waves-light" href="{{ action('Admin\DashboardController@index') }}">
                        <img class="logo" src="{{ asset('img/zeiss.logo.png') }}"
                             alt="{{ _('brand logo') }}"/>
                    </a><!-- /.navbar-brand -->
                    <!--/ End brand -->

                </div><!-- /.navbar-header -->
                <!--/ End navbar header -->

                <div class="clearfix"></div>
            </div><!-- /.header-left -->
            <!--/ End header left -->

            <!-- Start header right -->
            <div class="header-right">
                <!-- Start navbar toolbar -->
                <div class="navbar navbar-toolbar">

                    <!-- Start left navigation -->
                    <ul class="nav navbar-nav navbar-left">

                        <!-- Start sidebar shrink -->
                        <li class="navbar-minimize">
                            <div data-activates="slide-out" class="navbar-brand button-collapse waves-effect waves-ripple"><i
                                    class="fa fa-bars"></i></div>
                        </li>
                        <!--/ End sidebar shrink -->

                    </ul><!-- /.nav navbar-nav navbar-left -->
                    <!--/ End left navigation -->

                    <!-- Start right navigation -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Start profile -->
                        <li class="dropdown navbar-profile">
                            <a href="#" class="dropdown-toggle waves-effect" data-toggle="dropdown">
                                <span class="meta">
                                    <span class="text hidden-xs hidden-sm text-muted">{{ auth()->user()->name }}</span>
                                    <span class="caret"></span>
                                </span>
                            </a>
                            <!-- Start dropdown menu -->
                            <ul class="dropdown-menu animated flipInX">
                                <li class="dropdown-header"><a href="{{ route('admin.user.edit', ['id' => Auth::id()]) }}">{{ _('Account') }}</a></li>
                                <li><a href="{{ action('Auth\AuthController@logout') }}"><i
                                            class="fa fa-sign-out"></i>{{ _('Logout') }}</a></li>
                            </ul>
                            <!--/ End dropdown menu -->
                        </li><!-- /.dropdown navbar-profile -->
                        <!--/ End profile -->

                    </ul>
                    <!--/ End right navigation -->

                </div><!-- /.navbar-toolbar -->
                <!--/ End navbar toolbar -->
            </div><!-- /.header-right -->
            <!--/ End header left -->

        </header> <!-- /#header -->

        <aside id="sidebar-left">

    <!-- Sidebar navigation -->
    <div id="slide-out" class="side-nav admin-side-nav stylish-side-nav">
        <!-- Logo -->
        <div class="logo-wrapper">
            <span class="mini-stat-icon"><i class="fa fa-user fg-facebook"></i></span>
            <div class="rgba-stylish-strong"><p class="user white-text">{{ _('Hello') }}, {{ auth()->user()->name }}<br>
                    {{ _('Web Admin') }}</p></div>
        </div>
        <!--/. Logo -->

        <!-- Side navigation links -->
        <ul class="collapsible collapsible-accordion">
            <li>
                <a class="waves-effect waves-light" href="{{ url('/admin') }}"><i class="fa fa-btn fa-dashboard"></i>Dashboard</a>
            </li>
            @if (userCanAccess('/admin/user', auth()->user()->is_admin))
            <li>
                <a class="waves-effect waves-light" href="{{ url('/admin/user') }}"><i class="fa fa-btn fa-male"></i>Users</a>
            </li>
            @endif
            @if (userCanAccess('/admin/market', auth()->user()->is_admin))
            <li>
                <a class="waves-effect waves-light" href="{{ url('/admin/market') }}"><i class="fa fa-btn fa-flag"></i>Markets</a>
            </li>
            @endif
            @if (userCanAccess('/admin/lens', auth()->user()->is_admin))
            <li>
                <a class="waves-effect waves-light" href="{{ url('/admin/lens') }}"><i class="fa fa-btn fa-eye"></i>Products</a>
            </li>
            @endif
            @if (userCanAccess('/admin/survey', auth()->user()->is_admin))
            <li>
                <a class="waves-effect waves-light" href="{{ url('/admin/survey') }}"><i class="fa fa-btn fa-list"></i>Surveys</a>
            </li>
            @endif

            @if (userCanAccess('/admin/score', auth()->user()->is_admin))
            <li>
                <a class="waves-effect waves-light" href="{{ url('/admin/score') }}"><i class="fa fa-btn fa-sort-numeric-desc"></i>Scores</a>
            </li>
            @endif
            @if (userCanAccess('/admin/rule', auth()->user()->is_admin))
            <li>
                <a class="waves-effect waves-light" href="{{ url('/admin/rule') }}"><i class="fa fa-btn fa-arrows-v"></i>Rules</a>
            </li>
            @endif
            @if (userCanAccess('/admin/translation', auth()->user()->is_admin))
            <li>
                <a class="waves-effect waves-light" href="{{ url('/admin/translation') }}"><i class="fa fa-btn fa-language"></i>Translation</a>
            </li>
            @endif
            @if (userCanAccess('/admin/media', auth()->user()->is_admin))
                <li>
                    <a class="waves-effect waves-light" href="{{ url('/admin/media') }}"><i class="fa fa-btn fa-picture-o"></i>Media Library</a>
                </li>
            @endif
        </ul>
        <!--/. Side navigation links -->

    </div>
    <!--/. Sidebar navigation -->

</aside><!-- /#sidebar-left -->


        <div class="header-content">
            <h2><i class="fa fa-home"></i>Order2Easy<span></span></h2>
            <div class="breadcrumb-wrapper hidden-xs">
                <span class="label"></span>
                <i class="fa fa-home"></i>
                <ol class="breadcrumb">
                    <li>
                        <a></a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                </ol>
            </div>
        </div>

        <section id="page-content">
        <div class="body-content">
        <div class="body-content animated fadeIn">
            <div class="row">
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="mini-stat mini-box clearfix z-depth-1">
                        <span class="mini-stat-icon"><i class="fa fa-user"></i></span>
                        <div class="mini-stat-info">
                            <span class="counter">1</span>
                            Cash
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        </section>

    </section>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.3.2/css/mdb.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>