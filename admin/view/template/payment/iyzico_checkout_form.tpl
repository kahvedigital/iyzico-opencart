<?php echo $header; ?>
<div id="content">
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    <?php if ($message) { ?>
    <div class="<?php echo $hasError?'warning':'success'; ?>"><?php echo $message; ?></div>
    <?php } ?>
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
	<?php if ($error_version) { ?>
    <div class="warning"><?php echo $error_version; ?> <a href="<?php echo $version_update_link; ?>"> <?php echo $iyzico_update_button; ?></a> <?php echo $iyzico_or_text; ?></div>
    <?php } ?>
    <div class="box">
        <div class="heading">
            <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons"><a onclick="$('#form').submit();" class="button" id="saveKeys"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
        </div>
        <div class="content">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <table class="form">
                    <tr>
                        <td colspan="2">
                            <p><?php echo $text_iyzico_checkout_form_info; ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="required">*</span> <?php echo $entry_api_id_live; ?></td>
                        <td><input type="text" name="iyzico_checkout_form_api_id_live" value="<?php echo $iyzico_checkout_form_api_id_live; ?>" />
                            <?php if ($error_api_id_live) { ?>
                                <span class="error"><?php echo $error_api_id_live; ?></span>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="required">*</span> <?php echo $entry_secret_key_live; ?></td>
                        <td><input type="text" name="iyzico_checkout_form_secret_key_live" value="<?php echo $iyzico_checkout_form_secret_key_live; ?>" />
                            <?php if ($error_secret_key_live) { ?>
                                <span class="error"><?php echo $error_secret_key_live; ?></span>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_status; ?></td>
                        <td><select name="iyzico_checkout_form_status">
                                <?php if ($iyzico_checkout_form_status) { ?>
                                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                    <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                    <option value="1"><?php echo $text_enabled; ?></option>
                                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_class; ?></td>
                        <td><select name="iyzico_checkout_form_form_class">
                                <?php if ($iyzico_checkout_form_form_class == "responsive") { ?>
                                    <option value="popup"><?php echo $entry_class_popup; ?></option>
                                    <option value="responsive" selected="selected"><?php echo $entry_class_responsive; ?></option>
                                <?php } else { ?>
                                    <option value="popup" selected="selected"><?php echo $entry_class_popup ?></option>
                                    <option value="responsive"><?php echo $entry_class_responsive; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_order_status; ?></td>
                        <td><select name="iyzico_checkout_form_order_status_id">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status['order_status_id'] == $iyzico_checkout_form_order_status_id) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_cancel_order_status; ?></td>
                        <td><select name="iyzico_checkout_form_cancel_order_status_id">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status['order_status_id'] == $iyzico_checkout_form_cancel_order_status_id) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_sort_order; ?></td>
                        <td><input type="text" name="iyzico_checkout_form_sort_order" value="<?php echo $iyzico_checkout_form_sort_order; ?>" size="1" /></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<?php echo $footer; ?>


<style type="text/css">

    #form input[type='text'], input[type='password'] {
        width: 250px;
    }

</style>
<script type="text/javascript">
    window.onload = function() {

        var response = '<?php echo $response ?>';
        if (response == 1) {
            var el = document.getElementById('saveKeys');
            el.click();
        }
    };
</script>