
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Assessment System | {!! $title !!}</title>

    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/full-slider.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/shoole.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/bootstrap-grid-h.css') }}" rel="stylesheet">

    <!--FOR MULTISELECT-->
    <link rel="stylesheet" href="css/bootstrap-multiselect.css" type="text/css"/>
    <!-- Fonts -->
    <!--<link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>-->
{!! HTML::style('css/jQueryUI/jquery-ui-1.10.3.custom.css') !!}
{!! HTML::style('css/datepicker/datepicker3.css') !!}

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.0/js/bootstrapValidator.min.js"> </script>
    <![endif]-->
    <style>
        #search_form > .input-group > .form-group {
            display: inline;
        }
    </style>

    <!--Script to timeout users-->
    <script>
        /*var timer = 5*60*1000;
        setTimeout(function(){
            window.location.reload();
        }, timer);*/
    </script>

    <style>

        .loading {
            position: fixed;
            display: none;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0,0,0,0.5);
            z-index: 2;
            cursor: pointer;
        }

        .loading img{

            width: 120px;
            height: 120px;
            /*margin-left: 50%;*/
            margin-top: 30%;
            float: left;
        }

        .loading span{

            width: 100px;
            height: 50px;
            display: block;
            /*margin-left: 20%;*/
            margin-top: 20%;
            /*line-height: 40%;*/
            color: white;
            float: left;

        }

        .inner-load{

            margin-left: 45%;

        }

        a{
            pointer-events: visible;
        }
    </style>


</head>
<body style="background-color: #999999">

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="loading">

                <div class="inner-load">
                    <span> </span> <img src="{{url(asset('assets/img/loading.gif'))}}">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid" style="background-color: #55acee;">
    <div class="row">
        <div class="col-md-3" style="padding-top: 1em">
            {!! HTML::image('img/logo.png') !!}
        </div>
        <div class="col-md-9 hospital">
                <span  style="font-size: 2em" >
                    Business Registrations And Licensing Agency
                </span>
        </div>
        <div class="col-md-6 hospital" style="text-align: center;font-size: 20px;">
            <i>
                Assessment System
            </i>
        </div>
    </div>
</div>
<nav class="navbar navbar-inverse navbar-static-top" style="margin: 0%">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#searchbar">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ url('/') }}"></a>
        </div>
        <div class="collapse navbar-collapse" id="searchbar">
            <ul class="nav navbar-nav navbar-left" style="margin-right: 5%; font-family: arial" >
                <li>
                    <a href ="" style="font-size: 12px">
                        <span class="glyphicon glyphicon-dashboard"></span> Assessment Dashboard
                    </a>
                </li>

            </ul>
            <ul class="nav navbar-nav tarehe" style="margin-right: 5%; font-family: arial;" >

                <li>
                    <a href ="" style="font-size: 12px">
                        <?php $date = date('Y-m-d H:i:s'); ?>
                        <span class="glyphicon glyphicon-calendar"></span> <?php
                        echo $date = date('F j, Y', strtotime($date) );

                        ?>
                    </a>
                </li>

            </ul>
            <ul class="nav navbar-nav navbar-right" style="margin-right: 5%; font-family: arial" >
                <li class="dropdown">
                    <a style="font-size: 12px" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo \Illuminate\Support\Facades\Auth::user()->name;?> <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="{{ url('change-password')  }}"><span class="glyphicon glyphicon-cog"></span> Change password</a></li>
                        <li><a href="{{ url('logout')  }}"><span class="glyphicon glyphicon-off"></span> Logout</a></li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</nav>
<div class="container-fluid">
    <div class="row square">
        <div class="col-md-2">

            <div id="leftnav" style="margin-top: 10px;font-size: 12px;margin-right: 10px" >
                <ol class="list-group">
                    <li class="list-group-item normal" >
                        <div class="visible-lg" >
                            <div style="margin-bottom: 5px">
                                <a href="">
                                    <b style="font-size: 14px;">
                                        Welcome
                                    </b>
                                </a>
                            </div>
                            <a href=""> <b><?php echo \Illuminate\Support\Facades\Auth::user()->name; ?></b></a>
                        </div>
                        <div class="visible-xs visible-sm visible-md">
                            <table>
                                <tr>
                                    <td>
                                        {!! HTML::image('img/sample.jpg', 'a picture',['width'=>'30px']) !!}
                                    </td>
                                    <td>
                                        &nbsp;<a href=""><b><?php echo \Illuminate\Support\Facades\Auth::user()->name; ?></b></a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </li>
                    <li class="list-group-item normal"><a href="{{ url('admin')  }}"><span class="glyphicon glyphicon-home"></span> Home</a> <span class="glyphicon pull-right"></span></li>

                    @if(\Illuminate\Support\Facades\Auth::user()->role == '0')

                        <li class="list-group-item normal"><a href="{{ url('new-assessment')  }}"><span class="glyphicon glyphicon-plus-sign"></span> New assessment</a> <span class="glyphicon pull-right"></span></li>
                        <li class="list-group-item normal"><a href="{{ url('divisions/list')  }}"><span class="glyphicon glyphicon-briefcase"></span> Divisions</a> <span class="glyphicon pull-right"></span></li>
                        <li class="list-group-item normal"><a href="{{ url('fees/fee-accounts')  }}"><span class="glyphicon glyphicon-briefcase"></span> Fee accounts</a> <span class="glyphicon pull-right"></span></li>
                        <li class="list-group-item normal"><a href="{{ url('fees/list')  }}"><span class="glyphicon glyphicon-briefcase"></span> Fees</a> <span class="glyphicon pull-right"></span></li>
                        <li class="list-group-item normal"><a href="{{ url('fees/items')  }}"><span class="glyphicon glyphicon-briefcase"></span> Fee items</a> <span class="glyphicon pull-right"></span></li>
                        <li  style="padding-left: 5px !important;padding-right: 5px !important;" class="list-group-item normal"><a style="margin-left: 10px" href=""><span class="glyphicon glyphicon-book"></span> Assessment reports</a> <span class="glyphicon  pull-right"></span>
                            <ol class="list-roup" style="margin-left: 0px !important;">

                                <li class="list-group-item normal"><a href="{{ url('my-assessments')  }}/individual"><span class="glyphicon glyphicon-folder-close"></span> My assessments</a> <span class="glyphicon pull-right"></span></li>
                                <li class="list-group-item normal"><a href="{{ url('my-assessments')  }}/all"><span class="glyphicon glyphicon-folder-close"></span> All assessments</a> <span class="glyphicon pull-right"></span></li>




                            </ol>
                        </li>
                        <li class="list-group-item normal"><a href="{{ url('users')  }}"><span class="glyphicon glyphicon-user"></span> Users</a> <span class="glyphicon pull-right"></span></li>
                    @endif

                    @if(\Illuminate\Support\Facades\Auth::user()->role == '1')

                        <li class="list-group-item normal"><a href="{{ url('new-assessment')  }}"><span class="glyphicon glyphicon-plus-sign"></span> New assessment</a> <span class="glyphicon pull-right"></span></li>
                        <li  style="padding-left: 5px !important;padding-right: 5px !important;" class="list-group-item normal"><a style="margin-left: 10px" href=""><span class="glyphicon glyphicon-book"></span> Assessment reports</a> <span class="glyphicon  pull-right"></span>
                            <ol class="list-roup" style="margin-left: 0px !important;">

                                <li class="list-group-item normal"><a href="{{ url('my-assessments')  }}/individual"><span class="glyphicon glyphicon-folder-close"></span> My assessments</a> <span class="glyphicon pull-right"></span></li>
                                <li class="list-group-item normal"><a href="{{ url('my-assessments')  }}/all"><span class="glyphicon glyphicon-folder-close"></span> All assessments</a> <span class="glyphicon pull-right"></span></li>




                            </ol>
                        </li>

                    @endif


                </ol>
            </div>
        </div>
        <div class="col-md-10">
            @yield('content')
        </div>
    </div>
</div>

<!-- Scripts -->

<script src="{{asset('/js/jquery.js')}}"></script>
<script src="{{asset('/js/jquery.flip.min.js')}}"></script>
<script src="{{asset('/js/jquery.fittext.js')}}"></script>
@yield('script')
<script src="{{asset('/js/shoole.js')}}"></script>
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>-->




<!-- jQuery 2.0.2 -->
<script src="js/plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
<script>
    $(function(){
        $('.datepicker').datepicker({ format: 'yyyy-mm-dd'})
    });
</script>


@yield('script')

<!-- special scripts for date picker-->

<!-- jQuery 2.0.2 -->
{!! HTML::script('js/jquery.min.js')!!}
{!! HTML::script('js/firems.js')!!}
{!! HTML::script('js/plugins/datepicker/bootstrap-datepicker.js')!!}
<script>
    $(function(){
        $('.datepicker').datepicker({ format: 'yyyy-mm-dd'})
    });
</script>


<!-- jQuery UI 1.10.3 -->
{!! HTML::script('js/jquery-ui-1.10.3.min.js') !!}
<!-- Bootstrap -->
{!! HTML::script('js/bootstrap.min.js') !!}
{!! HTML::script('assets/js/bootbox.min.js') !!}

<!--end special date picker scripts -->

<!-- Include the plugin's CSS and JS:FOR MULTISELECT -->


<div class="container-fluid">
    <div class="row-fluid">
        <nav class="navbar navbar-inverse navbar-static-bottom" style="border-radius: 0 !important; padding-top: 15px;margin-bottom: 0px;margin-top: 10px">
            <center><p style="color: white">Assessment System &copy; BRELA  <?= date('Y') ?></p></center>
        </nav>
    </div>
</div>


<!-- script to allow multiple select -->
@yield('multiselect')

@yield('multipleselect')

@yield('page_load')

@yield('lavacharts')
<script src="http://code.highcharts.com/highcharts.js"></script>

<script>

    $('.loading').css('display','block');
    $('a[href]').on('click', function(event) { event.preventDefault(); });

    $(document).ready(function (){
        //$("[data-mask]").inputmask();
        $('.loading')/*.css('display','none')*/.fadeOut(2000, function (){ $('a[href]').unbind("click"); });
    });


</script>

</body>
</html>