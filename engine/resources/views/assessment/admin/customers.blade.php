@extends('layouts.master')
@section('content')

    <div style="font-size:12px;border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="fbbluebox" id="users">

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
                        Search customer
                    </b>
                </td>
                <td>
                    <input type="text" class="form-control" name="customerName" onkeyup="search_customer()" id="customerName" placeholder="Search customer by name">
                </td>
                <td>
                    <input type="text" class="form-control" name="customerNumber" onkeyup="search_customer()" id="customerNumber" placeholder="Search by number">
                </td>
                <td>
                    <a href="{{ url('customers/add') }}" class="btn btn-primary"> <i class="glyphicon glyphicon-plus-sign"></i> Add customer</a>
                </td>
            </tr>
        </table>


        <div id="customers">
            @include('assessment.admin.search_customer_result')
        </div>

    </div>

    <script>
        function search_customer(){
            var customerName = document.getElementById('customerName').value;
            var customerNumber = document.getElementById('customerNumber').value;

            if(window.XMLHttpRequest) {
                myObject = new XMLHttpRequest();
            }else if(window.ActiveXObject){
                myObject = new ActiveXObject('Micrsoft.XMLHTTP');
                myObject.overrideMimeType('text/xml');
            }

            myObject.onreadystatechange = function (){
                data = myObject.responseText;
                if (myObject.readyState == 4) {
                    document.getElementById('customers').innerHTML = data;
                }
            }; //specify name of function that will handle server response........
            myObject.open('GET','{{ URL::route("search-customer") }}?customerNumber='+customerNumber+'&customerName='+customerName,true);
            myObject.send();
        }
    </script>




@endsection
