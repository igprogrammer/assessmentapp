@if(Session::has('success-message'))

    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {!! Session::get('success-message') !!}
    </div>
@endif

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
        <th>
            Delete
        </th>
    </tr>


    <?php

    if( count($employees) > 0 ){


    $i=1;
    foreach($employees as $employee){?>
    <tr>
        <td>
            {!! $i !!}
        </td>
        <td>
            {!! $employee->name !!}
        </td>
        <td>
            {!! $employee->account_status !!}
        </td>
        <td>
            {!! $employee->email !!}
        </td>
        <td>
            <?php
            if($employee->role == '0'){
                echo "Admin";
            }else if($employee->role == '1'){
                echo "Assessor";
            }else{
                echo "Undefined";
            }

            ?>
        </td>
        <td>
            <a href='employee/{{ $employee->id }}/edit' class="btn btn-success"><i class='glyphicon glyphicon-edit'>View</i></a>
        </td>
        <td>
            <button class="btn btn-danger" onclick="remove_user('{{ $employee->id }}')"><i class='glyphicon glyphicon-trash'></i> Delete</button>
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
    {{--<tr>--}}
        {{--<td colspan="9">--}}
            {{--<div class="pagination">--}}
                {{--{!! str_replace('/?', '?', $employees->render() )!!}--}}
            {{--</div>--}}
        {{--</td>--}}
    {{--</tr>--}}

</table>