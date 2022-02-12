<html>
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <style type="text/css">
        TABLE {
            padding:0px;
            font-family:Arial, Helvetica, sans-serif;
            font-size:11px;
            height:auto;
            padding-bottom:00px;
        }
        .tablespacing td{
            padding: 1px 5px;
        }
        TABLE LABEL {
            display:block;
            width:250px;
            float:left
        }
        TABLE LABEL SPAN {
            font-style:italic;
        }
        TABLE SPAN {
            display:block;
            vertical-align:text-bottom
        }


    </style>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" style="margin-top:10px">

@include('assessments.number')
<?php

//include('number.php');

//$fee_results=mysql_query("select type_name,fee_amount from fees_type,payment_fees where type_id=fees_id AND payment_id='".$_REQUEST['serial_no']."' and account_type!='stamp'") ;
        /*$pay_info = \App\PaymentFee::join('fee_items','fee_items.id','=','payment_fees.fee_item_id')
            ->join('fees','fees.id','=','fee_items.fee_id')
            ->where('payment_id','=',$payment->id)->get();*/
        $pay_infos = \App\Fee::join('fee_items','fee_items.fee_id','=','fees.id')
            ->join('payment_fees','payment_fees.fee_item_id','=','fee_items.id')
            ->where('payment_id','=',$payment->id)->get();

//$fee_stamp=mysql_query("select type_name,fee_amount from fees_type,payment_fees where type_id=fees_id AND payment_id='".$_REQUEST['serial_no']."' and account_type='stamp'") ;
//$nn=mysql_num_rows($fee_results);
$n=0;
$sum=0;
$sums=0;
$words = \App\Currency::where('code','=',$payment->currency)->first();
$words = $words->name;

//$words=mysql_query("SELECT name FROM CURRENCY WHERE code='".$_REQUEST['currency']."'");
//$words=mysql_result($words,0,'name');

$amount_words= convert_number($payment->amount).' '.$words.' only.';

$payment_customer_info = \App\Customer::join('payments','customers.id','=','payments.customer_id')
    ->where('payments.id','=',$payment->id)->first();


//$sqlname=mysql_query("SELECT customer_name,amount,account_code,status,re_assessment_description FROM payment,customers WHERE customer_id=customer and payment_id='".$_REQUEST['serial_no']."'");
//$nameresult=mysql_fetch_array($sqlname);

$numbers = $payment_customer_info->account_code;

//$numbers = $nameresult['account_code'];
$code = substr((string)$numbers,-2);


?>

<table id="Table_01" width="600" height="750" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="600"  height="100" valign="top" align="right">
            <table width="600" border="0" cellspacing="0" cellpadding="0" height="60">
                <tr>
                    <td width="200"><img src="images/brelalogo.jpg" width="200" height="60">
                    </td>

                    <td>
                        <div style="font-family:tahoma; font-size:12px; display:block; padding-left:0px;width:420px;margin-left:10px;">
                            <b>BUSINESS REGISTRATIONS AND LICENSING AGENCY (BRELA).</b><br>
                            <b>P.O.BOX</b> 9393, Dar es Salaam.<br>
                            <b>PHONE :</b> +255 22 2180141, 2180113, 2181113.&nbsp;&nbsp;<b>FAX:</b> +255 22 2180371<br>
                            <b>E-MAIL:</b> usajili@brela.go.tz, brela@brela.go.tz,info@brela.go.tz<br>
                            <b>WEBSITE:</b> http://www.brela.go.tz
                        </div>
                    </td>
                    <td width="70" height="60">
                        <img src="images/logotz.jpg">
                    </td>
                </tr>
            </table>
        </td>

    </tr>
    <tr>
        <td width="800"  colspan="2" valign="top">
            <h2 align="center"><?php if($payment_customer_info->status == '1'){echo 'Re-assessment Invoice No: ';}else{echo 'Control Number: ';} ?>
                <?php
                //$sno=$_REQUEST['serial_no'];
                /*$sno = $payment->id;
                $query = "SELECT * FROM payment where payment_id='$sno'";
                $query_result = mysql_query($query);
                $query_row = mysql_fetch_array($query_result);*/
                //$control_number = $query_row['invoice'];
                $control_number = $payment->invoice;

                ?>
                <?php echo strtoupper(str_pad($control_number,5,0,STR_PAD_LEFT));?> <?php if($payment->re_assessment_description != null){ echo ' From previously '.$payment->re_assessment_description;}?>
                <?php //if($code == 42){ echo "<br>". "(Pay only through CRDB Bank)"; }?></h2>
            <table width="780" border="0">
                <tr><td colspan="3">
                        <table border="1" width="100%" style="font-weight:bold" cellpadding="0" cellspacing="0" class="tablespacing">
                            <tr><td rowspan="2">Divisional Account Bank</td><td>Companies</td><td>Business Names</td>
                                <td>Industrial Property(IP) T&S Marks/Patent</td><td>Industrial Licence</td><td>Business Licence</td><td>Stamp Duty</td><td>BS(Others)</td></tr>
                            <tr><td>440331</td><td>440332</td><td>440322</td><td>440341</td><td>440342</td><td>440343</td><td>440350</td></tr>
                            <tr><td>CRDB : TZS</td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td></tr>
                            <tr><td>CRDB : USD</td><td>&nbsp;&nbsp;&nbsp;</td><td></td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td><td></td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td></tr>
                            <tr><td>NMB : TZS</td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td></tr>
                            <tr><td>NMB : USD</td><td>&nbsp;&nbsp;&nbsp;</td><td></td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td><td></td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td></tr>
                            <tr><td>TIGO PESA:</td><td>&nbsp;&nbsp;&nbsp;</td><td></td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td><td></td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td></tr>
                            <tr><td>M-PESA:</td><td>&nbsp;&nbsp;&nbsp;</td><td></td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td><td></td><td>&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td></tr>
                            <tr><td>AMOUNT</td><td>
                                    <?php if($payment_customer_info->account_code == 440331){
                                        /*while($fee=mysql_fetch_object($fee_results)){
                                            $sum+=$fee->fee_amount;
                                        }*/

                                        if (!empty($pay_infos)){
                                            foreach ($pay_infos as $pay_info){
                                                $sum = $sum + $pay_info->fee_amount;
                                            }
                                        }else{
                                            echo "No record found";
                                        }

                                        if($sum!=0)echo $payment->currency."  : ".number_format($sum);
                            }?></td><td>
                                    <?php if($payment_customer_info->account_code == 440332){
                                        /*while($fee=mysql_fetch_object($fee_results)){
                                            $sum+=$fee->fee_amount;
                                        }

                                        if($sum!=0)echo $_REQUEST['currency']."  : ".number_format($sum);*/

                                        if (!empty($pay_infos)){
                                            foreach ($pay_infos as $pay_info){
                                                $sum = $sum + $pay_info->fee_amount;
                                            }
                                        }else{
                                            echo "No record found";
                                        }

                                        if($sum!=0)echo $payment->currency."  : ".number_format($sum);


                                    }?>
                                </td><td>
                                    <?php
                                    if($payment_customer_info->account_code==440322 or $payment_customer_info->account_code==440320){
                                        /*while($fee=mysql_fetch_object($fee_results)){
                                            $sum+=$fee->fee_amount;
                                        }
                                        if($sum!=0)echo $_REQUEST['currency']."  : ".number_format($sum);*/
                                        if (!empty($pay_infos)){
                                            foreach ($pay_infos as $pay_info){
                                                $sum = $sum + $pay_info->fee_amount;
                                            }
                                        }else{
                                            echo "No record found";
                                        }

                                        if($sum!=0)echo $payment->currency."  : ".number_format($sum);

                                    }?>
                                </td>
                                <td>
                                    <?php if($payment_customer_info->account_code == 440341){
                                        /*while($fee=mysql_fetch_object($fee_results)){
                                            $sum+=$fee->fee_amount;
                                        }

                                        if($sum!=0)echo $_REQUEST['currency']."  : ".number_format($sum);*/

                                        if (!empty($pay_infos)){
                                            foreach ($pay_infos as $pay_info){
                                                $sum = $sum + $pay_info->fee_amount;
                                            }
                                        }else{
                                            echo "No record found";
                                        }

                                        if($sum!=0)echo $payment->currency."  : ".number_format($sum);


                                    }?>
                                </td>
                                <td>
                                    <?php if($payment_customer_info->account_code == 440342){
                                        /*while($fee=mysql_fetch_object($fee_stamp)){
                                            $sums+=$fee->fee_amount;
                                        }
                                        if($sums!=0)echo $_REQUEST['currency']."  : ".number_format($sums);*/

                                        if (!empty($pay_infos)){
                                            foreach ($pay_infos as $pay_info){
                                                $sum = $sum + $pay_info->fee_amount;
                                            }
                                        }else{
                                            echo "No record found";
                                        }

                                        if($sum!=0)echo $payment->currency."  : ".number_format($sum);

                                    }?>
                                </td>
                                <td>
                                    <?php if($payment_customer_info->account_code == 440343){
                                        /*while($fee=mysql_fetch_object($fee_stamp)){
                                            $sums+=$fee->fee_amount;
                                        }

                                        if($sums!=0)echo $_REQUEST['currency']."  : ".number_format($sums);*/

                                        if (!empty($pay_infos)){
                                            foreach ($pay_infos as $pay_info){
                                                $sum = $sum + $pay_info->fee_amount;
                                            }
                                        }else{
                                            echo "No record found";
                                        }

                                        if($sum!=0)echo $payment->currency."  : ".number_format($sum);

                                    }?>
                                </td>

                                <td>
                                    <?php if($payment_customer_info->account_code == 440350){
                                        /*while($fee=mysql_fetch_object($fee_results)){
                                            $sums+=$fee->fee_amount;
                                        }
                                        if($sums!=0)echo $_REQUEST['currency']."  : ".number_format($sums);*/

                                        if (!empty($pay_infos)){
                                            foreach ($pay_infos as $pay_info){
                                                $sum = $sum + $pay_info->fee_amount;
                                            }
                                        }else{
                                            echo "No record found";
                                        }

                                        if($sum!=0)echo $payment->currency."  : ".number_format($sum);

                                    }?>
                                </td>
                            </tr>
                        </table>
                    </td></tr>
                <tr><td colspan="3"><p style="color:red;display:block; padding-top:10px;font-size:10px"><strong>NB:Weka alama ya vema (&#10004;) kwenye njia ya malipo uliyotumia.Provide a tick (&#10004;) for a channel you have made payment through</strong></p></td></tr>


                <tr>
                    <td colspan="3">
			<span style="padding-left:500px; font-weight:bold; text-transform:uppercase">
<!--
			<?php //echo "<a href='javascript:cust_form(".$_REQUEST['serial_no'].")' style='color:#000000; text-decoration:none'>Invoice No:</a>"; ?>
<?php echo "<a href='javascript:cust_form(".$payment->id.")' style='color:#000000; text-decoration:none'>Invoice No:</a>"; ?>
<?php //echo "<a href='javascript:cust_form(".$_REQUEST['serial_no'].")' style='color:#000000; text-decoration:none'>Receipt No:</a>"; ?>
<?php echo "<a href='javascript:cust_form(".$payment->id.")' style='color:#000000; text-decoration:none'>Receipt No:</a>"; ?>
<?php //echo strtoupper(str_pad($_REQUEST['serial_no'],8,0,STR_PAD_LEFT));?>
<?php echo strtoupper(str_pad($payment->id,8,0,STR_PAD_LEFT));?>
-->

			</span><br>
                        <label>
                            Name<BR>
                        </label>
                        <!--<span style="padding-left:200px; font-weight:bold; text-transform:uppercase"><?php //echo strtoupper(stripslashes(htmlspecialchars($nameresult['customer_name']))); ?></span>-->
                        <span style="padding-left:200px; font-weight:bold; text-transform:uppercase"><?php echo strtoupper(stripslashes(htmlspecialchars($payment_customer_info->customer_name))); ?></span>
                        <span>..................................................................................................................................................................................................
			</span>
                    </td></tr>
                <tr><td colspan="3" style="">
                        <?php
                        //$fee_results=mysql_query("select type_name,fee_amount,fname,fyear from fees_type,payment_fees where type_id=fees_id AND payment_id='".$_REQUEST['serial_no']."'") ;
                        //$nn=mysql_num_rows($fee_results);
                        $sum=0;?>
                        <label>
                            Payment details<BR>
                        </label>
                        <span style="padding-left:200px; font-weight:bold; text-transform:uppercase">
                            <?php
                            /*while($fee=mysql_fetch_object($fee_results)){
                                $f=($fee->fname)?"($fee->fname-$fee->fyear)":"";echo $fee->type_name.$f." [ ".$_REQUEST['currency'].number_format($fee->fee_amount)."]";$sum+=$fee->fee_amount; $n+=1; if($n==$nn){echo"&nbsp;.";}else{echo ",&nbsp;";}
                            }*/


                            if (!empty($pay_infos)){
                                foreach ($pay_infos as $pay_info){
                                    $f=($pay_info->fname)?"($pay_info->fname-$pay_info->fyear)":"";echo $pay_info->fee_name.$f." [ ".$pay_info->currency.number_format($pay_info->fee_amount)."], ";$sum+=$pay_info->fee_amount; $n+=1;
                                    /*if($n==$nn){
                                        echo"&nbsp;.";
                                    }else{
                                        echo ",&nbsp;";
                                    }*/
                                }
                            }else{
                                echo "No assessment item(s) found";
                            }


                            echo "<br>Total ".$payment->currency."  : ".number_format($sum);?> <?php echo strtoupper($amount_words); ?>
                        </span>
                        <span>.................................................................................................................................................................................................
			</span>

                        <?php
                        /*if (!empty($control_number) || $control_number != null){
                            $b_query = "select * from booking b where b.invoice='$control_number'";
                            $b_result = pg_query($b_query);
                            $b_row = pg_fetch_array($b_result);
                            $expire_days = $b_row['expire_days'];
                        }*/

                            if (!empty($control_number) || $control_number != null){

                                $booking = \Illuminate\Support\Facades\DB::connection('pgsql')->table('booking')->select()->where('invoice',$control_number)->first();
                                if (!empty($booking)){
                                    $expire_days = $booking->expire_days;
                                }else{
                                    $expire_days = '0';
                                }

                            }


                        ?>
                    </td></tr>
                <tr><td colspan="3"><p style="display:block;padding-top:10px;font-size:12px;padding-bottom:10px"><i><strong>NB: This Assessment shall remain valid only for a period of ( <?php echo $expire_days; ?> ) days from the date of assessment<br>Failure to comply you will have to re-submit all the documents.</strong></i></p></td></tr>
                <tr><td width="200">
                        Name of the Assessor<br>
                        <?php
                            $user = \App\User::find($payment->user_id);
                        ?>
                        <span style="text-decoration:none; text-transform:uppercase; font-weight:bold; padding-top:10px; display:block"><?php echo strtoupper($user->name); ?></span>
                    </td>
                    <td width="200">
                        Sign<br>
                        <span style="text-decoration:underline; text-transform:uppercase; font-weight:bold; padding-top:10px; display:block"></span>
                        <span style="text-transform:uppercase; font-weight:bold; display:block; padding-top:10px">.........................................</span>
                    </td>
                    <td width="200">
                        Date<br>
                        <span style="text-decoration:underline; text-transform:uppercase; font-weight:bold; display:block; padding-top:10px"><?php echo strtoupper($payment_customer_info->date_of_payment); ?></span>
                    </td></tr>

                <tr>
                    <td colspan="3"><p style="color:red;display:block; padding-top:10px;font-size:10px"><strong>NB: (1) Andika Namba ya ankara(Control Number) katika slip ya benki.Malipo yakifanyika
                                Benki bila kufuata utaratibu wa ankara(Invoice) haiwezekani kupata risiti ya BRELA wala huwezi kupata huduma BRELA.<br />
                                (2.) Usichanganye ADA YA USAJILI na STAMPU YA USHURU unapojaza 'slip' ya benki/ Do not mix-up REGISTRATION FEES and the STAMP DUTY in one bank slip<br>
                                <?php
                                if($code == 42){?>
                                (3).Malipo ya Leseni ya Biashara yafanyike kupitia benki ya CRDB tu (Business License Payments must only be made through CRDB Bank).
                                <?php }?>

                                <br/>
                            </strong>
                        </p>
                        <strong>
                            <h3>Payments Options</h3><br/>
                            1. Through Direct Bank Deposits<br/>

                            <?php if($payment->account_code != 440342){?>
                            Direct Bank Deposit can be made through any branch of CRDB or NMB Bank  using Control Number: <?php echo $control_number; ?><br/><br/>
                            <?php }else{?>
                            Direct Bank Deposit can be made through any branch of CRDB or NMB  Bank using Control Number: <?php echo $control_number; ?><br/><br/>
                            <?php }?>

                            <?php if($payment->account_code !=440342 ||  $payment->account_code == 440342){?>
                            2. Through Mobile Money<br/>
                            Use AirTell Money / Tigopesa/ MPesa/ HalloPesa select Government Payment and Control Number: <?php echo $control_number; ?><br/><br/>
                            <?php } ?>


                            <?php
                            if($payment->account_code != 440342){?>
                        <!--3. Through Bank Transfer<br/> -->
                            <?php if($payment->currency=='TSHs'){ ?>
                        <!--Transfer can be made from any Commercial Bank to:
   NMB ( Account Number: 20401000047) / CRDB ( Account Number: 01J1009833100 ) using TISS /
   SWIFT by specifying:Field 59 ( Account Number: ) and Field 70 ( Control Number: <?php //echo $control_number; ?>)<br/><br/> -->
                            <?php }else{?>
                        <!--Transfer can be made from any Commercial Bank to:
   NMB ( Account Number : 20410002044) / CRDB ( Account Number: 0250270344200) using TISS /
   SWIFT by specifying:Field 59 ( Account Number: ) and Field 70 (Control Number: <?php echo $control_number; ?>)<br/><br/> -->
                            <?php } ?>

                            <?php }else{?>



                        <!--2. Through Bank Transfer<br/>
 <?php if($payment->currency=='TSHs'){ ?>
                                Transfer can be made from any Commercial Bank to:
                                  CRDB ( Account Number: 0150413356300 ) using TISS /
                                   SWIFT by specifying:Field 59 ( Account Number: ) and Field 70 ( Control Number: <?php echo $control_number; ?>)<br/><br/> -->
                            <?php }else{?>
                        <!--Transfer can be made from any Commercial Bank to:
CRDB ( Account Number: 0250413356300) using TISS /
   SWIFT by specifying:Field 59 ( Account Number: ) and Field 70 (Control Number: <?php echo $control_number; ?>)<br/><br/> -->
                            <?php } ?>


                            <?php } ?>




                            Njia za kulipia Huduma:<br/><br/>

                            1. Kwa njia ya kuweka Fedha kwenye Tawi la Benki /Wakala wa Benki<br/>

                            <?php if($payment->account_code != 440342){?>
                            Nenda kwenye Tawi lolote la Benki / Wakala wa Benki ya CRDB /NMB kwa kutumia Namba ya Kumbukumbu ya Malipo: <?php echo $control_number;
                            }else{?>
                            Nenda kwenye Tawi lolote la Benki / Wakala wa Benki ya CRDB au NMB kwa kutumia Namba ya Kumbukumbu ya Malipo: <?php echo $control_number; ?>
                            <?php }?>

                            <br/><br/>
                            <?php if($payment->account_code != 440342 ||  $payment->account_code == 440342){?>
                            2. Kwa njia ya Simu ya Mkononi.<br/>
                            Tumia mtandao wa simu wa AirTell Money / Tigopesa/ MPesa/ HalloPesa na chagua Malipo ya serikali kisha weka namba ya Kumbukumbu ya
                            Malipo: <?php echo $control_number;; ?><br/><br/>
                            <?php }else{ ?>

 <?php } ?>


                            <?php if($payment->account_code!=440342){?>
                        <!--3. Kwa njia ya Kuhamisha Fedha <br/> -->
                            <?php if($payment->currency=='TSHs'){ ?>

                        <!--Unaweza kuhamisha Fedha moja kwa moja kutoka katika Benki yoyote kwenda kwenye Akaunti zetu zilizoko
    katika Benki ya NMB ( Namba ya Akaunti: 20401000047) / CRDB ( Namba ya Akaunti: 01J1009833100)  kwa njia ya TISS / SWIFT kwa kujaza: Namba ya Akaunti (Field 59) na Kumbukumbu ya Malipo: (<?php //echo $control_number; ?>) (Field 70) -->
                            <?php }else{?>
                        <!--Unaweza kuhamisha Fedha moja kwa moja kutoka katika Benki yoyote kwenda kwenye Akaunti zetu zilizoko
    katika Benki ya NMB ( Namba ya Akaunti: 20410002044) / CRDB ( Namba ya Akaunti: 0250270344200)  kwa njia ya TISS / SWIFT kwa kujaza: Namba ya Akaunti (Field 59) na Kumbukumbu ya Malipo: ( <?php //echo $control_number; ?>) (Field 70) -->
                            <?php }?>
                            <?php }else{?>
                        <!--2. Kwa njia ya Kuhamisha Fedha <br/> -->
                            <?php if($payment->currency=='TSHs'){ ?>
                        <!--Unaweza kuhamisha Fedha moja kwa moja kutoka katika Benki yoyote kwenda kwenye Akaunti zetu zilizoko
    katika Benki ya  CRDB ( Namba ya Akaunti: 0150413356300)  kwa njia ya TISS / SWIFT kwa kujaza: Namba ya Akaunti (Field 59) na Kumbukumbu ya Malipo: ( --><?php //echo $control_number; ?> <!--) (Field 70) -->
                            <?php }else{?>
                        <!--Unaweza kuhamisha Fedha moja kwa moja kutoka katika Benki yoyote kwenda kwenye Akaunti zetu zilizoko
    katika Benki ya CRDB ( Namba ya Akaunti: 0250413356300)  kwa njia ya TISS / SWIFT kwa kujaza: Namba ya Akaunti (Field 59) na Kumbukumbu ya Malipo
	: ( --><?php //echo $control_number; ?><!-- ) (Field 70) -->
                            <?php }?>

                            <?php }
                            ?>
                        </strong>
                    </td></tr>


            </table>
</body>
</html>