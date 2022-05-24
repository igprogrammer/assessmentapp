<table class="table table-striped table-bordered" style="width: 100%">
    <tr>
        <th>
            SNO
        </th>
        <th>
            Number
        </th>
        <th>
            Name
        </th>
        <th>
            Date
        </th>
        <th>
            Currency
        </th>
        <th>
            Amount
        </th>
        <th>
            Control number
        </th>
        <th>
            Assessor
        </th>

        <th>
            Status
        </th>

        <th>
            Action
        </th>

    </tr>

    @if(count($payments) > 0)
        <?php $sn = 1; ?>
        @foreach($payments as $payment)

            <tr>
                <td>{{ $sn }}</td>
                <td>
                    <?php $customer = \App\Models\Customer\Customer::find($payment->customer_id); ?>
                    @if(!empty($customer))
                        {{ $customer->company_number ?? 'No customer' }}
                    @endif
                </td>
                <td>{{ $customer->customer_name }}</td>
                <td>{{ $payment->date_of_payment }}</td>
                <td>{{ $payment->currency }}</td>
                <td>{{ $payment->billAmount }}</td>
                <td>{{ $payment->invoice }}</td>
                <td>
                <?php $user = \App\Models\User::find($payment->user_id); ?>
                {{ $user->name }}
                <td>
                    @if((int)$payment->invoice >= initialControlNumber())
                        @if($payment->isPaid == 1)
                            <a class="btn btn-success" style="width: 100%"><i class="glyphicon glyphicon-check"> Paid</i></a>
                        @else
                            <a class="btn btn-info" style="width: 100%"><i class="glyphicon glyphicon-remove"> Not paid</i></a>
                        @endif
                    @else
                        <a class="btn btn-danger" style="width: 100%"><i class="glyphicon glyphicon-remove-circle"> No control number</i></a>
                    @endif
                </td>
                <td>
                    @include('assessment.assessment.printingActionControls')
                </td>
            </tr>
            <?php $sn++; ?>
        @endforeach



        <tr>
            <td colspan="10">
                <div class="pagination">

                </div>
            </td>
        </tr>

    @else
        <tr>
            <td colspan="10"><b>No record found.</b></td>
        </tr>
    @endif


</table>
