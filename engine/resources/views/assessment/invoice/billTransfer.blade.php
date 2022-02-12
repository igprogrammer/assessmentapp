<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <title>Business Registrations and Licensing Agency</title>


    <link href="{{ asset('assets/payment/css/billreceipt.css') }}" rel="stylesheet" type="text/css" />
    <!-- Bootstrap 3.3.4 -->
    <link href="{{asset(url('bootstrap/css/bootstrap.min.css'))}}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
<!--    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />-->

    <style>

        @media print {
            html, body {
                height: 1175px;
                page-break-after: avoid;
                page-break-before: avoid;
            }
        }


    </style>

</head>

<body>


<div class="container-fluid">

    <div class="row">
        <div class="col-md-12 body" align="center">

            <div class="loginPic print_head">
                <div class="pr int_img">{!! Html::image(asset('assets/images/arm_s.png')) !!}</div>
                <div class="print_h">United Republic of Tanzania</div>
                <div class="print_b">Business Registrations and Licensing Agency</div>
                <div class="print_h">Order Form for Electronic Funds Transfer for {{ strtoupper($bankName) }}</div>
            </div>

        </div>

    </div>
    <table style="width: 1000px;">

        <tr>

            <td><b>(a). Remitter/Tax payer details :-</b></td>


        </tr>
        <tr>
            <td>
                Name of Account Holder(s)
            </td>
            <td>
                :..............................................................................................................................................
            </td>
        </tr>
        <tr>
            <td>
                Name of Commercial Bank
            </td>
            <td>
                :..............................................................................................................................................
            </td>

        </tr>
        <tr>
            <td>
                Bank Account Number
            </td>
            <td>
                :..............................................................................................................................................
            </td>
        </tr>
        <tr>
            <td>
                Signatories
            </td>
            <td>
                :.................................................................. | .........................................................................
            </td>
        </tr>
        <tr>
            <td>

            </td>
            <td>
                signature of the Transfer one &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;
                | signature of the Transfer two
            </td>
        </tr>


        <tr>

            <td><b>(b). Beneficiary details :-</b></td><td>Business Registrations and Licensing Agency</td>


        </tr>
        <tr>

            <td></td><td>:{{ strtoupper($bankName) }}</td>


        </tr>

        <tr>

            <td>Account Number </td>
            <td>
                <b>
                    :

                    @if(strtolower($bankName) == 'nmb')

                        @if($paymentInfo->currency == 'USD')
                            20410002044
                        @else
                            20401000047
                        @endif

                    @elseif(strtolower($bankName) == 'crdb')

                        @if($paymentInfo->currency == 'USD')
                            0250270344200
                        @else
                            01J1009833100
                        @endif

                    @elseif(strtolower($bankName) == 'nbc')


                        @if($paymentInfo->currency == 'USD')
                            011105017693
                        @else
                            011139000320
                        @endif


                    @endif


                </b>
            </td>


        </tr>
        <tr>

            <td>SWIFT Code </td>
            <td>
                <b>:

                    @if(strtolower($bankName) == 'nmb')
                        NMIBTZTZ
                    @elseif(strtolower($bankName) == 'crdb')
                        CORUTZTZ
                    @elseif(strtolower($bankName) == 'nbc')
                        NLCBTZTX
                    @endif

                </b>
            </td>



        </tr>

        <tr>

            <td>Control Number </td><td><b>:<?php echo strtoupper(str_pad($paymentInfo->invoice,8,0,STR_PAD_LEFT));?></b></td>


        </tr>

        <tr>

            <td>Payer Name </td><td><b>:{{ $payerName }}</b></td>


        </tr>

        <tr>

            <td>Beneficiary Account (Field 59 of MT103) </td>
            <td>
                <b>: /

                    @if(strtolower($bankName) == 'nmb')

                        @if($paymentInfo->currency == 'USD')
                            20410002044
                        @else
                            20401000047
                        @endif

                    @elseif(strtolower($bankName) == 'crdb')

                        @if($paymentInfo->currency == 'USD')
                            0250270344200
                        @else
                            01J1009833100
                        @endif

                    @elseif(strtolower($bankName) == 'nbc')


                        @if($paymentInfo->currency == 'USD')
                            011105017693
                        @else
                            011139000320
                        @endif


                    @endif

                </b>
            </td>


        </tr>

        <tr>

            <td>Payment Reference (Field 70 of MT103) </td><td><b>: /ROC/<?php echo strtoupper(str_pad($paymentInfo->invoice,8,0,STR_PAD_LEFT));?></b></td>


        </tr>

        <tr>

            <td>Transfer Amount </td><td><b>: {{ number_format($paymentInfo->amount, 2) }} ( {{ $paymentInfo->currency }} )</b></td>


        </tr>

        <tr>
            <td colspan="2">
                <div class="formRow tr_print">
                    <div class="pr_title">Amount in Words</div>
                    <div class="pr_info bold" style="width: 400px;">:
                        <?php echo ucfirst($amountInWords); ?>
                    </div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>

        <tr>
            <td>Being payment for</td><td>BRELA Revenue</td>
        </tr>

        <tr>
            <td colspan="2">
                <div class="print_sep">



                    <div class="formRow tr_print">
                        @if(!empty($paymentItems))

                            <?php
                            $sn = 1;
                            $total = 0;
                            ?>

                                @foreach($paymentItems as $item)

                                    <div class="pr_title">Billed Item (<?php echo $sn; ?>) </div>
                                    <div class="pr_info_items" style="width: 450px;">: {{ $item->item_name.'-'.$paymentInfo->reference }}</div>
                                    <div class="pr_info_s" style="float: right;text-align: right;">: {{ number_format($item->fee_amount,2) }} </div>
                                    <div class="clear"></div>


                                    <?php $total = $total + $item->fee_amount; $sn++;?>
                                @endforeach

                        @else
                            No item(s)
                        @endif

                    </div>



                </div>
            </td>
        </tr>


        <tr>
            <td colspan="2">
                <div class="formRow tr_print">
                    <div class="pr_title">Expires on</div>
                    <div class="pr_info">: {{ date('d-M-Y H:i:s', strtotime($paymentInfo->expire_date)) }}</div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="formRow tr_print">
                    <div class="pr_title">Prepared By</div>
                    <div class="pr_info" style="width: 400px;">: <strong> {{ $user->name }} </strong></div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <div class="formRow tr_print">
                    <div class="pr_title">Collection Centre</div>
                    <div class="pr_info" style="width: 400px;">: Head Quarters</div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="formRow tr_print">
                    <div class="pr_title">Printed By</div>
                    <div class="pr_info" style="width: 400px;">: <strong>{{ \Illuminate\Support\Facades\Auth::user()->name }}</strong></div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="formRow tr_print">

                    <div class="pr_title">Printed on</div>
                    <div class="pr_info">: {{ date('d-M-Y', strtotime(date('Y-m-d'))) }}</div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
<!--        <tr>
            <td colspan="2">
                <div class="formRow tr_print">
                    <div class="pr_title">Signature</div>
                    <div class="pr_info">: .....SYSTEM.....</div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>-->

        <tr>
            <td colspan="2">

                <p>
                    <b>Note to Commercial Bank:</b>
                <ol>
                    <li>Please capture the above information correctly.Do not change or add any text, symbols or digits on the information provided.</li>
                    <li>Field 59 of MT103 is an <b>"Account Number"</b> with value: <b> :/
                            @if(strtolower($bankName) == 'nmb')

                                @if($paymentInfo->currency == 'USD')
                                    20410002044
                                @else
                                    20401000047
                                @endif

                            @elseif(strtolower($bankName) == 'crdb')

                                @if($paymentInfo->currency == 'USD')
                                    0250270344200
                                @else
                                    01J1009833100
                                @endif

                            @elseif(strtolower($bankName) == 'nbc')


                                @if($paymentInfo->currency == 'USD')
                                    011105017693
                                @else
                                    011139000320
                                @endif


                            @endif
                        </b>. Must be captured correctly.
                    </li>
                    <li>
                        Field 70 of MT103 is a <b>"Control Number"</b> with value: <b> /ROC/<?php echo strtoupper(str_pad($paymentInfo->invoice,8,0,STR_PAD_LEFT));?></b>. Must be captured correctly.
                    </li>
                </ol>
                </p>

            </td>
        </tr>

        <tr>
            <td colspan="2">
                <div class="print_foot">Business Registrations and Licensing Agency &copy; {{ date('Y') }} All Rights Reserved (BRELA)</div>
            </td>
        </tr>
    </table>


</div>


</body>
</html>
