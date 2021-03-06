@extends('layouts.master')
@section('content')

    <div style="font-size:12px;border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="fbbluebox">

        @if(Session::has('success-message'))

            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {!! Session::get('success-message') !!}
            </div>
        @endif

        <table class="table" style="width: 100%">
            <tr>
                <td>
                    <b>
                        Search user
                    </b>
                </td>
                <td>
                    <input type="text" class="form-control" name="name" onkeyup="search_user()" id="name" placeholder="Search user">
                </td>
                <td>
                    <a href="{{ url('register') }}" class="btn btn-primary"> <i class="glyphicon glyphicon-plus-sign"></i> Add user</a>
                </td>
            </tr>
        </table>


        <div id="users">
            @include('assessment.admin.search_user_result')
        </div>

    </div>

    <script>
        function search_user(){
            var name = document.getElementById('name').value;

            if(window.XMLHttpRequest) {
                myObject = new XMLHttpRequest();
            }else if(window.ActiveXObject){
                myObject = new ActiveXObject('Micrsoft.XMLHTTP');
                myObject.overrideMimeType('text/xml');
            }

            myObject.onreadystatechange = function (){
                data = myObject.responseText;
                if (myObject.readyState == 4) {
                    document.getElementById('users').innerHTML = data;
                }
            }; //specify name of function that will handle server response........
            myObject.open('GET','{{ URL::route("search-user") }}?name='+name,true);
            myObject.send();
        }
    </script>




@endsection
