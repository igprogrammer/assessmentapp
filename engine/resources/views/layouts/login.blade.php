<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Assessment and Receipt system | {!! $title !!}</title>

    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/full-slider.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/shoole.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/bootstrap-grid-h.css') }}" rel="stylesheet">
    <!-- Fonts -->
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
    {!! Html::style('css/jQueryUI/jquery-ui-1.10.3.custom.css') !!}
    {!! Html::style('css/datepicker/datepicker3.css') !!}

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
</head>
<body>

<div class="container-fluid" style="background-color: #55acee;">
    <div class="row">
        <div class="col-md-3" style="padding-top: 1em">
            {!! Html::image('img/logo.png') !!}
        </div>
        <div class="col-md-9 hospital">
                <span  style="font-size: 2em" >
                    Business Registrations And Licensing Agency(BRELA)
                </span>
        </div>
        <div class="col-md-6 hospital" style="text-align: center;font-size: 20px;">
            <i>
                Assessment and Receipt System
            </i>
        </div>
    </div>
</div>



<div class="container-fluid">
    <div class="row square">
        <div class="col-md-12">
            @yield('content')
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{asset('/js/jquery.min.js')}}"></script>

<script src="{{asset('/js/jquery.js')}}"></script>
<script src="{{asset('/js/jquery.flip.min.js')}}"></script>
<script src="{{asset('/js/jquery.fittext.js')}}"></script>
@yield('script')
<script src="{{asset('/js/shoole.js')}}"></script>

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
{!! Html::script('js/jquery.min.js')!!}

{!! Html::script('js/plugins/datepicker/bootstrap-datepicker.js')!!}
<script>
    $(function(){
        $('.datepicker').datepicker({ format: 'yyyy-mm-dd'})
    });
</script>
<!-- jQuery UI 1.10.3 -->
{!! Html::script('js/jquery-ui-1.10.3.min.js') !!}
<!-- Bootstrap -->
{!! Html::script('js/bootstrap.min.js') !!}

<!--end special date picker scripts -->

<nav class="navbar navbar-inverse navbar-fixed-bottom hidden-xs hidden-md hidden-sm" style="border-radius: 0 !important; padding-top: 15px;margin-bottom: 0px;margin-top: 10px">
    <center><p style="color: white">Assessment System &copy; BRELA  <?= date('Y') ?></p></center>
</nav>
<nav class="navbar navbar-inverse navbar-static-bottom visible-xs visible-md visible-sm" style="border-radius: 0 !important; padding-top: 15px;margin-bottom: 0px;margin-top: 10px">
    <center><p style="color: white">Assessment System &copy; BRELA  <?= date('Y') ?></p></center>
</nav>
</body>
</html>
<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 10/16/15
 * Time: 1:58 PM
 */
