@extends('layouts.master')

@section('content')

    <div style="border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="ask fbbluebox">


        <div class="panel panel-primary">

            <div class="panel-heading">Assessment list</div>
            <div class="panel-body">

                {!! Form::open(['url'=>'assessments/filter','method'=>'post','class'=>'form','files'=>true])!!}
                <table class="table table-striped table-bordered">
                    <td>From</td>
                    <td>
                        <input type="text" name="from_date" value="<?php echo date('Y-m-d'); ?>" class="form-control datepicker">
                    </td>
                    <td>To</td>
                    <td>
                        <input type="text" name="to_date" value="<?php echo date('Y-m-d'); ?>" class="form-control datepicker">
                    </td>
                    <td></td>
                    <td>
                        {!! Form::hidden('flag',$flag) !!}
                        {!! Form::submit('Filter',['class'=>'btn btn-success']) !!}
                    </td>
                </table>
                {!!Form::close()!!}


                <table class="table">
                    <tr>
                        <td>
                            <b>
                                Search item
                            </b>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="controlNumber" onkeyup="search_entity()" id="controlNumber" placeholder="Search by control number">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="entityNumber" onkeyup="search_entity()" id="entityNumber" placeholder="Search by number">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="entityName" onkeyup="search_entity()" id="entityName" placeholder="Search by name">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="reference" onkeyup="search_entity()" id="reference" placeholder="Search by reference number">
                        </td>
                    </tr>
                </table>

                @if(Session::has('success-message'))

                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {!! Session::get('success-message') !!}
                    </div>
                @endif

                <div  id="assessment-details">
                    @include('assessment.assessment.assessment_list')
                </div>


            </div>
        </div>

        </div>


    </div>



    <script>
        function print_assessment(payment_id,type)
        {
            testprintout=window.open("{{ URL::route('print-bill-payment') }}?type="+type+"&payment_id="+payment_id+"","t","width=1000,height=700,menubar=yes,resizable=yes,scrollbars=yes,toolbar=yes,location=no").print();
        }



        function search_entity(){
            var controlNumber = document.getElementById('controlNumber').value;
            var entityName = document.getElementById('entityName').value;
            var entityNumber = document.getElementById('entityNumber').value;
            var reference = document.getElementById('reference').value;
            var flag = '<?php echo $flag; ?>'

            if(window.XMLHttpRequest) {
                myObject = new XMLHttpRequest();
            }else if(window.ActiveXObject){
                myObject = new ActiveXObject('Micrsoft.XMLHTTP');
                myObject.overrideMimeType('text/xml');
            }

            myObject.onreadystatechange = function (){
                data = myObject.responseText;
                if (myObject.readyState == 4) {
                    document.getElementById('assessment-details').innerHTML = data;
                }
            }; //specify name of function that will handle server response........
            myObject.open('GET','{{ URL::route("search-assessment") }}?reference='+reference+'&controlNumber='+controlNumber+'&flag='+flag+'&entityName='+entityName+'&entityNumber='+entityNumber,true);
            myObject.send();
        }


        function reRequestControlNumber(payment_id){


            bootbox.dialog({
                closeButton: false,
                message: "Are you sure you want re-generate control number for payment?",
                title: "Confirm request Control number",
                buttons: {
                    danger: {
                        label: "&nbsp;&nbsp;&nbsp;&nbsp; Yes &nbsp;&nbsp;&nbsp;&nbsp;",
                        className: "btn-danger",
                        callback: function() {


                            $('.loading').css('display','block');
                            $('a[href]').on('click', function(event) { event.preventDefault(); });


                            if(window.XMLHttpRequest) {
                                myObject = new XMLHttpRequest();
                            }else if(window.ActiveXObject){
                                myObject = new ActiveXObject('Micrsoft.XMLHTTP');
                                myObject.overrideMimeType('text/xml');
                            }

                            myObject.onreadystatechange = function (){
                                data = myObject.responseText;
                                var response = JSON.parse(data);
                                if (myObject.readyState == 4) {
                                    $('.loading').css('display','none');

                                    bootbox.alert({
                                        message: response.message,
                                        callback: function () {
                                            window.location.reload(true)
                                        }
                                    })

                                }
                            };

                            myObject.open('GET','{{ url('assessments/re-request-control-number') }}?paymentId='+payment_id,true);
                            myObject.send();


                        }
                    },
                    main: {
                        label: "&nbsp;&nbsp;&nbsp;&nbsp; No &nbsp;&nbsp;&nbsp;&nbsp;",
                        className: "btn-primary",
                        callback: function() {
                            return true;
                        }
                    }
                }
            });



        }



    </script>



@stop
