<table class="table table-striped table-bordered">
    <?php if ($payment_success == 'yes') {

    if ($same_date == 'yes' && $successful_cancel_counts < 1 && $successful_refund_counts == 0 && $display_cancel_option == 'yes'){
    ?>
    <tr class="order_can_cancel_tr">
        <td><?php echo $text_payment_cancel; ?></td>
        <td>
            <a class="button btn btn-primary" id="btn_cancel"><?php echo $text_order_cancel;?></a>
            <span class="btn btn-primary" id="img_loading_cancel" style="display:none;"><i class="fa fa-cog fa-spin fa-lg"></i></span>

        </td>
    </tr>
    <?php
        }

       if ($successful_cancel_counts == 0 && count($iyzico_transactions_refunds_data) > 0){ ?>
    <tr class="order_can_cancel_tr">
        <td><?php echo $text_items; ?></td>
        <td>
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <td class="text-left"><strong><?php echo $text_item_name; ?></strong></td>
                    <td class="text-left"><strong><?php echo $text_paid_price; ?></strong></td>
                    <td class="text-left"><strong><?php echo $text_total_refunded_amount; ?></strong></td>
                    <td class="text-left"><strong><?php echo $text_action; ?></strong></td>
                </tr>
                </thead>
                <tbody>
                <?php
                    foreach($iyzico_transactions_refunds_data as $key => $refund_data){
                ?>
                <tr>
                    <td class="text-left"><?php echo $refund_data['name']; ?></td>
                    <td class="text-left"><?php echo $refund_data['paid_price']; ?></td>
                    <td class="text-left"><?php echo $refund_data['total_refunded']; ?></td>
                    <td class="text-left">
                        <?php if ($refund_data['full_refunded'] == 'no') {  ?>
                        <div class="col-sm-6">
                            <input class="form-control" type="text" width="10" id="refund_field_<?php echo $refund_data['item_id']?>" value="<?php echo $refund_data['remaining_refund_amount']; ?>"/>
                        </div>
                        <div class="col-sm-6">
                            <a class="button btn btn-primary btn_refund" id="btn_refund_<?php echo $refund_data['item_id']?>" data-item-id="<?php echo $refund_data['item_id']?>" class="btn_refund"><?php echo $text_refund; ?></a>
                            <span class="btn btn-primary" id="img_loading_refund_<?php echo $refund_data['item_id']?>" style="display:none;"><i class="fa fa-cog fa-spin fa-lg"></i></span>
                        </div>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
        </td>
    </tr>
    <?php }
    }

    if ( (is_array($iyzico_transactions) && count($iyzico_transactions) > 0) ||(is_string($iyzico_transactions) && $iyzico_transactions != 'false')) {
    ?>
    <tr>
        <td><?php echo $text_transactions; ?></td>
        <td>
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <td class="text-left"><strong><?php echo $text_date_added; ?></strong></td>
                    <td class="text-left"><strong><?php echo $text_type; ?></strong></td>
                    <td class="text-left"><strong><?php echo $text_status; ?></strong></td>
                    <td class="text-left"><strong><?php echo $text_note; ?></strong></td>
                </tr>
                </thead>
                <tbody>
                <?php

                    foreach ($iyzico_transactions as $transaction) { ?>
                <tr>
                    <td class="text-left"><?php echo $transaction['date_created']; ?></td>
                    <td class="text-left"><?php echo $transaction['transaction_type']; ?></td>
                    <td class="text-left"><?php echo $transaction['transaction_status']; ?></td>
                    <td class="text-left"><?php echo $transaction['note']; ?></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
        </td>
    </tr>
    <?php } ?>
</table>

<script type="text/javascript">
    <?php if ($payment_success == 'yes') {
        if ($same_date == 'yes' && $successful_cancel_counts < 1 && $successful_refund_counts == 0){
        ?>
    $("#btn_cancel").click(function () {
        if (confirm('<?php echo $text_are_you_sure; ?>')) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {'order_id': <?php echo $order_id; ?>},
                url: 'index.php?route=extension/payment/iyzico_checkout_form/cancel&token=<?php echo $token; ?>',
                beforeSend: function () {
                    $('#btn_cancel').hide();
                    $('#img_loading_cancel').show();
                },
                success: function (data) {
                    if (data.message != '') {
                        alert(data.message);
                        window.location.reload();
                    }
                    if (data.success == 'true') {
                        $(".order_can_cancel_tr").hide();
                    }
                    $('#img_loading_cancel').hide();
                }
            });
        }
    });
    <?php
        }

        if ($successful_cancel_counts == 0 && count($iyzico_transactions_refunds_data) > 0) { ?>
            $("a.btn_refund").click(function(){
                var item_id = $(this).attr('data-item-id');
                var parent_tr = $(this).parent("tr");
                var amount = $("#refund_field_" + item_id).val();
                if (amount == undefined || amount.length < 1) {
                    alert("<?php echo $text_please_enter_amount; ?>");
                    return false;
                }
                if (amount <= 0) {
                    alert("<?php echo $text_please_enter_amount; ?>");
                    return false;
                }
                if (confirm('<?php echo $text_are_you_sure; ?>')) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        data: {'order_id': <?php echo $order_id; ?>, "item_id": item_id, "amount": amount},
                        url: 'index.php?route=extension/payment/iyzico_checkout_form/refund&token=<?php echo $token; ?>',
                        beforeSend: function () {
                            $('#btn_refund_' + item_id).hide();
                            $('#img_loading_refund_' + item_id).show();
                        },
                        success: function (data) {
                            if (data.message != '') {
                                alert(data.message);
                                window.location.reload();
                            }

                            $('#img_loading_refund_' + item_id).hide();
                        }
                    });
                }
            });
    <?php
        }
    }
    ?>
</script>