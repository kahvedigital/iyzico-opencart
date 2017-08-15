<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-free-checkout" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
		 <?php if ($error_version) { ?>
        <div class="alert alert-danger"><i class="fa fa-check-circle"></i> <?php echo $error_version; ?> <a href="<?php echo $version_update_link; ?>"><i class="fa fa-arrow-down"></i> <?php echo $iyzico_update_button; ?></a> <?php echo $iyzico_or_text; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i>
            <?php echo $text_iyzico_checkout_form_info; ?>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-free-checkout" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-order-status">  <?php echo $entry_api_id_live; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="iyzico_checkout_form_api_id_live" value="<?php echo $iyzico_checkout_form_api_id_live; ?>" class="form-control"/>
                            <?php if ($error_api_id_live) { ?>
                            <span class="text-danger"><?php echo $error_api_id_live; ?></span>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-order-status"> <?php echo $entry_secret_key_live; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="iyzico_checkout_form_secret_key_live" value="<?php echo $iyzico_checkout_form_secret_key_live; ?>" class="form-control"/>
                            <?php if ($error_secret_key_live) { ?>
                            <span class="text-danger"><?php echo $error_secret_key_live; ?></span>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-iyzico-checkout-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="iyzico_checkout_form_status" class="form-control">
                                <?php if ($iyzico_checkout_form_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="iyzico_checkout_form_form_classs"><?php echo $entry_class; ?></label>
                        <div class="col-sm-10">
                            <select name="iyzico_checkout_form_form_class" class="form-control">
                                <?php if ($iyzico_checkout_form_form_class == "responsive") { ?>
                                <option value="popup"><?php echo $entry_class_popup; ?></option>
                                <option value="responsive" selected="selected"><?php echo $entry_class_responsive; ?></option>
                                <?php } else { ?>
                                <option value="popup" selected="selected"><?php echo $entry_class_popup ?></option>
                                <option value="responsive"><?php echo $entry_class_responsive; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-order-status">
                            <span data-toggle="tooltip" title="<?php echo $order_status_after_payment_tooltip; ?>">
                                <?php echo $entry_order_status; ?>
                            </span>
                        </label>
                        <div class="col-sm-10">
                            <select name="iyzico_checkout_form_order_status_id" id="input-order-status" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $iyzico_checkout_form_order_status_id) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-cancel-order-status"><span data-toggle="tooltip" title="<?php echo $order_status_after_cancel_tooltip; ?>"><?php echo $entry_cancel_order_status; ?></span></label>
                        <div class="col-sm-10">
                            <select name="iyzico_checkout_form_cancel_order_status_id" id="input-cancel-order-status" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status['order_status_id'] == $iyzico_checkout_form_cancel_order_status_id) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_sort_order; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="iyzico_checkout_form_sort_order" value="<?php echo $iyzico_checkout_form_sort_order; ?>" size="1" class="form-control"/>
                        </div>
                    </div>
            </div>
        </div>
        </form>
    </div>
</div>
</div>
</div>
<?php echo $footer; ?> 