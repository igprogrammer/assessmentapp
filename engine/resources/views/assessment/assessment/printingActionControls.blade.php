@if((int)$payment->invoice >= 991350000000)

    <nav class="nav navbar-nav navbar-right" style="width: 100%">
        <div class="container-fluid">

            <ul class="nav navbar-nav navbar-left" style="font-family: arial" >
                <li class="dropdown">
                    <a style="font-size: 18px" href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Actions menu <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="{{ url('assessments/assessment-items') }}/{{ encrypt($payment->id) }}/{{ $flag }}"><i class="glyphicon glyphicon-eye-open"></i> View</a></li>
                        <li><a href="" onclick="print_assessment('{{ encrypt($payment->id) }}','normal')"><i class="glyphicon glyphicon-print"></i> Print normal bill</a></li>
                        <li><a href=""  onclick="print_assessment('{{ encrypt($payment->id) }}','nmb')"><i class="glyphicon glyphicon-print"></i> NMB transfer</a></li>
                        <li><a href=""  onclick="print_assessment('{{ encrypt($payment->id) }}','crdb')"><i class="glyphicon glyphicon-print"></i> CRDB transfer</a></li>
                        <li><a href=""  onclick="print_assessment('{{ encrypt($payment->id) }}','nbc')"><i class="glyphicon glyphicon-print"></i> NBC transfer</a></li>
                        <li>
                            @if($payment->isPaid == 1)
                                <a href=""  onclick="print_assessment('{{ encrypt($payment->id) }}','receipt')"><i class="glyphicon glyphicon-print"></i> Print receipt</a>
                            @endif
                        </li>
                    </ul>
                </li>

            </ul>

        </div>
    </nav>

@else
    <a style="width: 100%" class="btn btn-warning" href="{{ url('assessments/assessment-items') }}/{{ encrypt($payment->id) }}/{{ $flag }}"><i class="glyphicon glyphicon-eye-open"></i> View</a>
    <br><br>
    <a onclick="reRequestControlNumber('{{ encrypt($payment->id) }}')" class="btn btn-primary"><i class="glyphicon glyphicon-print"></i> Request control number</a>

@endif
