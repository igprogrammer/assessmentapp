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
<!--
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
-->

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
                <div class="pr int_img">
                    {!! Html::image(asset('assets/images/arm_s.png')) !!}
                </div>
                <div class="print_h">United Republic of Tanzania</div>
                <div class="print_b">Business Registrations and Licensing Agency</div>
                <div class="print_h">Government Bill</div>
                @if($isCopyBill == 1)
                    <div class="print_h">*****This is a printed copy*****</div>
                @endif
            </div>

        </div>

    </div>
    <div class="row">
        <div class="col-md-12 body" align="center">
            <table style="width: 900px;">

                <tr>
                    <td style="width: 900px;padding-top: -20px;">
                        <div class="formRow tr_print">
                            <div class="pr_title">Control Number  </div>
                            <div class="pr_info bold">: {{ strtoupper(str_pad($paymentInfo->invoice,8,0,STR_PAD_LEFT)) }}</div>
                            <div class="clear"></div>
                        </div>
                        <div class="formRow tr_print">
                            <div class="pr_title">Payment Ref  </div>
                            <div class="pr_info bold">: {{ $paymentInfo->reference }}</div>
                            <div class="clear"></div>
                        </div>
                        <div class="formRow tr_print">
                            <div class="pr_title">Service Provider Code</div>
                            <div class="pr_info bold">: SP135</div>
                            <div class="clear"></div>
                        </div>
                        <div class="formRow tr_print">
                            <div class="pr_title">Payer Name  </div>
                            <div class="pr_info bold">: {{ strtoupper($payerName) }}</div>
                            <div class="clear"></div>
                        </div>
                        <div class="formRow tr_print">
                            <div class="pr_title">Payer Phone  </div>
                            <div class="pr_info bold">: {{ $booking->phone_number }}</div>
                            <div class="clear"></div>
                        </div>
                        <div class="formRow tr_print">
                            <div class="pr_title">Bill Description  </div>
                            <div class="pr_info bold">: BRELA Revenue</div>
                            <div class="clear"></div>
                        </div>

                    </td>
                    <td style="width: 200px;">
                        {!! QrCode::size(180)->generate($qrcodedata); !!}
                            <h6 id="billqrtext">SCAN &amp; PAY by MPESA or TIGO PESA APPs</h6>
                    </td>
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
                            <div class="pr_title">&nbsp;&nbsp;&nbsp;</div>
                            <div class="pr_title bold">Total Billed Amount</div>
                            <div class="pr_info bold" style="float: right;text-align: right;">: <?php echo number_format($total, 2); ?> ({{ $booking->currency }})</div>
                            <div class="clear"></div>
                        </div>
                    </td>
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
                    <td colspan="2">
                        <div class="formRow tr_print">
                            <div class="pr_title">Expires on</div>
                            <div class="pr_info">: {{ date('d-M-Y H:i:s', strtotime($booking->expire_date)) }}</div>
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
                <tr>
                    <td colspan="2">
                        <div class="formRow tr_print">
                            <div class="pr_title">Signature</div>
                            <div class="pr_info">: ................</div>
                            <div class="clear"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">

                        <div class="formRow tr_print">
                            <div class="col-md-12" style="font-size: 11px;font-family: Trebuchet Ms;">
                                <table style="width:100%;">
                                    <tr>
                                        <td style="width:50%;"><strong>Jinsi ya Kulipa</strong></td>
                                        <td><strong>How to Pay</strong></td>
                                    </tr>
                                    <tr>
                                        <td>1. Kupitia Benki: Fika tawi lolote au wakala wa benki ya CRDB, NBC, NMB.  Namba ya kumbukumbu: <strong><?php echo strtoupper(str_pad($paymentInfo->invoice,8,0,STR_PAD_LEFT));?></strong>.</td>
                                        <td>1. Via Bank: Visit any branch or bank agent of CRDB, NBC, NMB. Reference Number:<strong><?php echo strtoupper(str_pad($paymentInfo->invoice,8,0,STR_PAD_LEFT));?></strong>.</td>
                                    </tr>

                                    <tr>
                                        <td>2. Kupitia Mitandao ya Simu:
                                            <ul style="list-style: inside;margin: 0px 10px;">
                                                <li>Ingia kwenye menyu ya mtandao husika</li>
                                                <li>Chagua 4 (Lipa Bili)</li>
                                                <li>Chagua 5 (Malipo ya Serikali)</li>
                                                <li>Ingiza <strong><?php echo strtoupper(str_pad($paymentInfo->invoice,8,0,STR_PAD_LEFT));?></strong> kama namba ya kumbukumbu</li>
                                            </ul></td>
                                        <td>2. Via Mobile Network Operators (MNO): Enter to the<br> respective USSD Menu of MNO
                                            <ul style="list-style: inside;margin: 0px 10px;">
                                                <li>Select 4 (Make Payments)</li>
                                                <li>Select 5 (Government Payments)</li>
                                                <li>Enter <strong><?php echo strtoupper(str_pad($paymentInfo->invoice,8,0,STR_PAD_LEFT));?></strong> as reference number</li>
                                            </ul></td>
                                    </tr>

                                </table>
                            </div>
                            <div class="clear"></div>
                        </div>

                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="print_foot">Business Registrations and Licensing Agency &copy; {{ date('Y') }} All Rights Reserved (BRELA)</div>
                    </td>
                </tr>

            </table>
        </div>
    </div>



</div>

</body>
</html>
