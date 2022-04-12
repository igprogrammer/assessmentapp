<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" moznomarginboxes mozdisallowselectionprint>
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


<div class="container-fluid">

    <div class="row">
        <div class="col-md-12 body" align="center">

            <div class="loginPic print_head">
                <div class="pr int_img">{!! Html::image(asset('assets/images/arm_s.png')) !!}</div>
                <div class="print_h">Jamhuri ya Muungano wa Tanzania</div>
                <div class="print_h">United Republic of Tanzania</div>
                <div class="print_b">Business Registrations and Licensing Agency</div>

                <div class="print_h">Exchequer Receipt</div>
                <div class="print_b">Stakabadhi ya Malipo ya Serikali </div>
                @if($isCopyReceipt == 1)
                    <div class="print_h">*****This is a printed copy*****</div>
                @endif
            </div>

        </div>

    </div>

    <table style="width:100%;font-size: 16px;">
        <tr>
            <td style="width:999px;">
                <div class="formRow tr_print">
                    <div class="pr_title">Receipt Number  </div>
                    <div class="pr_info bold">:
                        {{ $paymentInfo->PayRefId ?? $paymentInfo->receiptNo }}
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="formRow tr_print">
                    <div class="pr_title">Received from  </div>
                    <div class="pr_info bold">: {{ $payerName }} </div>
                    <div class="clear"></div>
                </div>
                <div class="formRow tr_print">
                    <div class="pr_title">Amount  </div>
                    <div class="pr_info">: {{ $paymentInfo->paidAmount }} {{ $paymentInfo->currency }}</div>
                    <div class="clear"></div>
                </div>
                <div class="formRow tr_print">
                    <div class="pr_title">Amount in words  </div>
                    <div class="pr_info" style="width: 450px;">: {{ ucfirst($amountInWords) }}</div>
                    <div class="clear"></div>
                </div>

                <div class="formRow tr_print">
                    <div class="pr_title">In respect of  </div>
                    <div class="pr_info" style="width: 800px;">



                        @if(!empty($paymentItems))

                            <?php
                            $sn = 1;
                            $total = 0;
                            ?>

                            @foreach($paymentItems as $item)

                                <div class="pr_title">Billed Item (<?php echo $sn; ?>) </div>
                                <div class="pr_info_items" style="width: 450px;">: {{ $item->fee_name.'-'.$item->reference }}</div>
                                <div class="pr_info_s_r" style="float: right;text-align: right;">: {{ $item->item_amount }} </div>
                                <div class="clear"></div>


                                <?php $total = $total + $item->item_amount; $sn++;?>
                            @endforeach

                        @else
                            No item(s)
                        @endif



                    </div>
                    <div class="clear"></div>
                </div>


            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="formRow tr_print">
                    <div class="pr_title">Bill Reference  </div>
                    <div class="pr_info">: {{ $paymentInfo->reference }}</div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <div class="formRow tr_print">
                    <div class="pr_title">Payment Control Number  </div>
                    <div class="pr_info">: {{ $paymentInfo->controlNumber }}</div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <div class="formRow tr_print">
                    <div class="pr_title">Payment Date  </div>
                    <div class="pr_info">: {{ date('d-M-Y', strtotime($paymentInfo->payDate)) }}</div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>


        <tr>
            <td colspan="2">
                <div class="formRow tr_print">
                    <div class="pr_title">Issued by</div>
                    <div class="pr_info" style="width: 400px;">: <strong>{{ $accountant->name }}</strong></div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="formRow tr_print">
                    <div class="pr_title">Date issued</div>
                    <div class="pr_info" style="width: 400px;">: {{ date('d-M-Y H:i:s', strtotime(date($paymentInfo->printedDate))) }}</div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>


        <tr>
            <td colspan="2">
                <div class="formRow tr_print">
                    <div class="pr_title">Signature</div>
                    <div class="pr_info">: .......................................</div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="print_foot">Business Registrations and Licensing Agency &copy; 2021 All Rights Reserved (BRELA)</div>
            </td>
        </tr>
    </table>


</div>

</body>
</html>
