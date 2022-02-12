<div class="col-md-12">
    <div class="panel panel-info">
        <!-- Default panel contents -->
        <div class="panel-heading">Selected items</div>
        <div class="panel-body">
            <table class="table">
                <thead>
                <tr>
                    <th>
                        Item name
                    </th>
                    <th>
                        Fee amount
                    </th>
                    <th>
                        Date of payment
                    </th>
                    <th>
                        Account code
                    </th>
                    <th>
                        Form
                    </th>
                    <th>
                        Year
                    </th>
                    <th>
                        Filing year
                    </th>
                    <th>
                        Action
                    </th>
                </tr>
                </thead>
                <tbody>

                <?php
                    if (count($temp_items) > 0){
                        $total_amount = 0;
                        foreach ($temp_items as $temp_item){
                            $fee_item = \App\Models\Assessment\FeeItem::find($temp_item->fee_item_id);
                            $fee = \App\Models\Assessment\Fee::find($fee_item->fee_id);
                            $fee_name = $fee->fee_name;
                            ?>
                            <tr>
                                <td>
                                    <input type="text" name="item_name" id="item_name" class="form-control" placeholder="Item name" readonly value="<?php echo $fee_name;?>">
                                    <input type="hidden" name="item_ids[]" value="<?php echo $temp_item->fee_item_id; ?>">
                                </td>
                                <td>
                                    <input type="text" name="fee_amount" id="fee_amount" class="form-control" placeholder="Amount" readonly value="<?php echo $temp_item->fee_amount?>">
                                </td>
                                <td>
                                    <input type="text" name="date_of_payment" id="item_amount" class="form-control" placeholder="Date of payment" readonly value="<?php echo $temp_item->date_of_payment?>">
                                </td>
                                <td>
                                    <input type="text" name="account_code" id="penalty_amount" class="form-control" placeholder="Account code" readonly value="<?php echo $temp_item->account_code?>">
                                </td>
                                <td>
                                    <input type="text" name="fname" id="fname" class="form-control" placeholder="Form name" readonly value="<?php echo $temp_item->fname?>">
                                </td>
                                <td>
                                    <input type="text" name="fyear" id="fyear" class="form-control" placeholder="Form year" readonly value="<?php echo $temp_item->fyear?>">
                                    <input type="hidden" name="temp_payment_id" value="<?php echo $temp_payment->id; ?>">
                                </td>
                                <td>
                                    <input type="text" name="filing_date" id="filing_date" class="form-control" placeholder="Filing date" readonly value="<?php echo $temp_item->filing_date;?>">
                                    <input type="hidden" name="temp_payment_id" value="<?php echo $temp_payment->id; ?>">
                                </td>
                                <td>
                                    <a class="btn btn-danger" onclick="remove_item('<?php echo $temp_item->id; ?>','<?php echo $temp_payment->id; ?>')"> Remove item</a>
                                </td>
                            </tr>
                        <?php $total_amount = $total_amount + $temp_item->fee_amount; } ?>
                                <tr>
                                    <td></td>
                                    <td>
                                        <b>Total</b> <?php echo $total_amount; ?>
                                        <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>">
                                    </td>
                                    <td colspan="4"></td>
                                </tr>
                    <?php }else{  ?>
                <tr>
                    <td>
                        No item found
                    </td>
                </tr>
                    <?php }
                ?>


                </tbody>
            </table>
        </div>
    </div>
</div>
