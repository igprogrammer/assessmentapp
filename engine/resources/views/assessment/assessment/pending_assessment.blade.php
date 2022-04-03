@extends('layouts.master')

@section('content')

    <div style="border-bottom:none;padding-top:10px; padding-bottom:2px;background-color: white;margin-top: 10px;margin-left:3px;margin-right: 3px;margin-bottom: 0px" class="ask fbbluebox">

        <a class="btn btn-primary" href="{{ url('assessments/new-assessment') }}"><i class="glyphicon glyphicon-plus-sign"></i> New assessment<br></a>

        <br><br>

        <div class="panel panel-primary">
            @include('includes.flushMessage')
            <!-- Default panel contents -->

            <div class="panel-heading">{{ $title ?? 'List'}}</div>
            <div class="panel-body">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>
                            SNO
                        </th>
                        <th>
                            Company number
                        </th>
                        <th>
                            Company name
                        </th>

                        <th>
                            Phone number
                        </th>
                        <th>
                            Status
                        </th>
                        <th>
                            Action
                        </th>

                    </tr>

                    @if(count($tempAssessments) > 0)

                        <?php $sn = 1; ?>
                        @foreach($tempAssessments as $tempAssessment)
                            <tr>
                                <td>{{ $sn }}</td>
                                <td>{{ $tempAssessment->company_number }}</td>
                                <td>{{ $tempAssessment->company_name }}</td>
                                <td>{{ $tempAssessment->phone_number }}</td>
                                <td>
                                    @if($tempAssessment->status == 0)
                                        <a class="btn btn-info">Pending</a>
                                    @elseif($tempAssessment->status == 2)
                                        <a class="btn btn-info">Waiting approval</a>
                                    @else

                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-warning" href="{{ url('assessments/temp-assessment-details') }}/{{ encrypt($tempAssessment->id) }}"><i class="glyphicon glyphicon-edit"></i> Continue</a>

                                    @if(in_array(\Illuminate\Support\Facades\Auth::user()->isSuperVisor, array(1,2)))
                                        <a @if($tempAssessment->user_id != \Illuminate\Support\Facades\Auth::user()->id) disabled="disabled" @endif class="btn btn-danger" onclick="removeTempAssessment{{ $tempAssessment->id }}('{{ $tempAssessment->id }}')"><i class="glyphicon glyphicon-trash"></i> Delete</a>
                                        <script>
                                            function removeTempAssessment{{ $tempAssessment->id }}(id){
                                                bootbox.dialog({
                                                    closeButton: false,
                                                    message: "Are you sure you want to delete this incomplete assessment? ",
                                                    title: "Confirm delete assessment",
                                                    buttons: {
                                                        danger: {
                                                            label: "&nbsp;&nbsp;&nbsp;&nbsp; Yes &nbsp;&nbsp;&nbsp;&nbsp;",
                                                            className: "btn-danger",
                                                            callback: function() {

                                                                if(window.XMLHttpRequest) {
                                                                    myObject = new XMLHttpRequest();
                                                                }else if(window.ActiveXObject){
                                                                    myObject = new ActiveXObject('Micrsoft.XMLHTTP');
                                                                    myObject.overrideMimeType('text/xml');
                                                                }

                                                                myObject.onreadystatechange = function (){
                                                                    data = myObject.responseText;
                                                                    var res = JSON.parse(data);
                                                                    if (myObject.readyState == 4) {

                                                                        if (res.success == 1){
                                                                            bootbox.alert({
                                                                                message: res.message,
                                                                                callback: function () {

                                                                                    window.location.reload();
                                                                                    $('.loading').fadeOut(2000, function (){ $('a[href]').unbind("click"); });
                                                                                    filterButton.prop('disabled',false);


                                                                                }
                                                                            })
                                                                        }
                                                                    }
                                                                }; //specify name of function that will handle server response........
                                                                myObject.open('GET','{{ url('assessments/delete-assessment') }}?id='+id,true);
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
                                    @endif

                                </td>
                            </tr>
                            <?php $sn++; ?>
                        @endforeach

                        <tr><td colspan="13"><div class="pagination">{!! str_replace('/?', '?', $tempAssessments->render() )!!}</div></td></tr>
                    @else
                        <tr>
                            <td colspan="6"><b>No record found.</b></td>
                        </tr>
                    @endif



                </table>
            </div>
        </div>

    </div>



@stop
