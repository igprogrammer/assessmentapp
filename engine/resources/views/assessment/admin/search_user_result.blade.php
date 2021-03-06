<table class="table">
    <tr>
        <th>
            SNO
        </th>
        <th>
            Employee Name
        </th>
        <th>
            Account status
        </th>
        <th>
            Email address
        </th>
        <th>
            Employee type
        </th>
        <th>
            Edit
        </th>
        <th colspan="2">
            Actions
        </th>
    </tr>


    <?php

    if( count($users) > 0 ){


    $i=1;
    foreach($users as $user){?>
    <tr>
        <td>{{ $i }}</td>
        <td>{{ $user->name }}</td>
        <td>
            @if($user->account_status == 1)
                Active
            @else
                Disabled
            @endif
        </td>
        <td>
            {{ $user->email }}
        </td>
        <td>
            @if($user->role == 0)
                Admin
            @elseif($user->role == 1)
                Assessor
            @else
                Invalid
            @endif
        </td>
        <td>
            <a href='user/{{ encrypt($user->id) }}/edit' class="btn btn-success"><i class='glyphicon glyphicon-edit'>View</i></a>
        </td>
        <td>
            <a href="{{ url('change-password') }}/{{ encrypt($user->id) }}" class="btn btn-primary"> <i class="glyphicon glyphicon-plus-sign"></i> Change user password</a>
        </td>
        <td>
            @if($user->account_status == 1)
                <a class="btn btn-danger" onclick="disable_user{{ $user->id }}('{{ $user->id }}')"><i class='glyphicon glyphicon-trash'></i> Disable user</a>
                <script>
                    function disable_user{{ $user->id }}(id){
                        bootbox.dialog({
                            closeButton: false,
                            message: "Are you sure you want to disable this user? ",
                            title: "Disable user confirm",
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
                                            res = JSON.parse(data);
                                            if (myObject.readyState == 4) {

                                                if(res.success == 1){

                                                    bootbox.alert(res.message, function(){
                                                        window.location.reload();
                                                    });

                                                }

                                            }
                                        }; //specify name of function that will handle server response........
                                        myObject.open('GET','{{ URL::route("disable-user") }}?id='+id,true);
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
            @else
                <a class="btn btn-success" onclick="enable_user{{ $user->id }}('{{ $user->id }}')"><i class='glyphicon glyphicon-signal'></i> Enable user</a>
                <script>
                    function enable_user{{ $user->id }}(id){
                        bootbox.dialog({
                            closeButton: false,
                            message: "Are you sure you want to enable this user? ",
                            title: "Enable user confirm",
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
                                            res = JSON.parse(data);
                                            if (myObject.readyState == 4) {

                                                if(res.success == 1){

                                                    bootbox.alert(res.message, function(){
                                                        window.location.reload();
                                                    });

                                                }

                                            }
                                        }; //specify name of function that will handle server response........
                                        myObject.open('GET','{{ URL::route("enable-user") }}?id='+id,true);
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
    <?php $i++; }
    }else{ ?>
    <tr>
        <td colspan="8">
            <b style="font-size: 14px;color: deeppink;">
                No new employee found.
            </b>
        </td>
    </tr>

    <?php }

    ?>
    <tr>
        <td colspan="9">
            <div class="pagination">
                {!! str_replace('/?', '?', $users->render() )!!}
            </div>
        </td>
    </tr>

</table>
