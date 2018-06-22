<link rel="stylesheet" type="text/css" href="view/stylesheet/checkoutapi/checkoutapi.css" />

<?php echo $header; ?>
    <div id="content">

        <div class="breadcrumb">
            <?php foreach ($breadcrumbs as $breadcrumb)
            { ?>
                <?php echo $breadcrumb['separator']; ?>
                <a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
            <?php
            } ?>
        </div>

        <?php if ($error_warning)
        { ?>
            <div class="warning">
                    <?php echo $error_warning; ?>
            </div>
        <?php
        } ?>
        <div class="box">


            <div class="checkoutapi-wrapper">
                <a href="http://dev.checkout.com/" class="checkoutapi-logo" target="_blank">
                    <img src="https://www.checkout.com/static/img/checkout-logo/logo.svg" alt="Checkout.com" border="0" style="width: 50%;padding-top:10px;" />
                </a>

                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                    <div class="setting">
                        <h3 class="setting-header">Setting for Checkout.com Gateway 3.0</h3>
                            <div class="field">
                                <table class="form">
                                    <tr>
                                        <td><?php echo $entry_status; ?></td>
                                        <td><select name="checkoutapipayment_status">
                                                <?php if ($checkoutapipayment_status) { ?>
                                                <option value="1" selected="selected"><?php echo $text_status_on; ?></option>
                                                <option value="0"><?php echo $text_status_off; ?></option>
                                                <?php } else { ?>
                                                <option value="1"><?php echo $text_status_on; ?></option>
                                                <option value="0" selected="selected"><?php echo $text_status_off; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $entry_test_mode; ?></td>
                                        <td><select name="test_mode">
                                                <?php if ($test_mode == 'sandbox') { ?>
                                                <option value="sandbox" selected="selected"><?php echo $text_mode_sandbox; ?></option>
                                                <?php } else { ?>
                                                <option value="sandbox"><?php echo $text_mode_sandbox; ?></option>
                                                <?php } ?>
                                                <?php if ($test_mode == 'live') { ?>
                                                <option value="live" selected="selected"><?php echo $text_mode_live; ?></option>
                                                <?php } else { ?>
                                                <option value="live"><?php echo $text_mode_live; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $entry_secret_key; ?></td>
                                        <td><input type="text" name="secret_key" value="<?php echo $secret_key; ?>" />
                                            <?php if ($error_secret_key) { ?>
                                            <span class="error"><?php echo $error_secret_key; ?></span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $entry_public_key; ?></td>
                                        <td><input type="text" name="public_key" value="<?php echo $public_key; ?>" />
                                            <?php if ($error_public_key) { ?>
                                            <span class="error"><?php echo $error_public_key; ?></span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $entry_integration_type; ?></td>
                                        <td><select name="integration_type">
                                                <?php if ($integration_type == 'checkoutjs') { ?>
                                                <option value="checkoutjs" selected="selected"><?php echo $text_checkout_js; ?></option>
                                                <?php } else { ?>
                                                <option value="checkoutjs"><?php echo $text_checkout_js; ?></option>
                                                <?php } ?>
                                                <?php if ($integration_type == 'frames') { ?>
                                                <option value="frames" selected="selected"><?php echo $text_frames_js; ?></option>
                                                <?php } else { ?>
                                                <option value="frames"><?php echo $text_frames_js; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $entry_payment_action; ?></td>
                                        <td><select name="payment_action">
                                                <?php if ($payment_action == 'authorization') { ?>
                                                <option value="authorization" selected="selected"><?php echo $text_auth_only; ?></option>
                                                <?php } else { ?>
                                                <option value="authorization"><?php echo $text_auth_only; ?></option>
                                                <?php } ?>
                                                <?php if ($payment_action == 'capture') { ?>
                                                <option value="capture" selected="selected"><?php echo $text_auth_capture; ?></option>
                                                <?php } else { ?>
                                                <option value="capture"><?php echo $text_auth_capture; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $entry_is_3d; ?></td>
                                        <td><select name="is_3d">
                                                <?php if ($is_3d == 1) { ?>
                                                <option value=1 selected="selected"><?php echo $text_is_3d_no; ?></option>
                                                <?php } else { ?>
                                                <option value=1><?php echo $text_is_3d_no; ?></option>
                                                <?php } ?>
                                                <?php if ($is_3d == 2) { ?>
                                                <option value=2 selected="selected"><?php echo $text_is_3d_yes; ?></option>
                                                <?php } else { ?>
                                                <option value=2><?php echo $text_is_3d_yes; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $entry_save_card; ?></td>
                                        <td><select name="save_card">
                                                <?php if ($save_card == 'no') { ?>
                                                <option value='no' selected="selected"><?php echo $text_save_card_no; ?></option>
                                                <?php } else { ?>
                                                <option value='no'><?php echo $text_save_card_no; ?></option>
                                                <?php } ?>
                                                <?php if ($save_card == 'yes') { ?>
                                                <option value='yes' selected="selected"><?php echo $text_save_card_yes; ?></option>
                                                <?php } else { ?>
                                                <option value='yes'><?php echo $text_save_card_yes; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $entry_autocapture_delay; ?></td>
                                        <td><input type="text" name="autocapture_delay" value="0" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?php echo $entry_gateway_timeout; ?></td>
                                        <td><input type="text" name="gateway_timeout" value="<?php echo $gateway_timeout;?>" /></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $entry_successful_order_status; ?></td>
                                        <td><select name="checkout_successful_order">
                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                <?php if ($order_status['order_status_id'] == $checkout_successful_order) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected">
                                                    <?php echo $order_status['name']; ?>
                                                </option>
                                                <?php } else { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>">
                                                    <?php echo $order_status['name']; ?>
                                                </option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $entry_failed_order_status; ?></td>
                                        <td><select name="checkout_failed_order">
                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                <?php if ($order_status['order_status_id'] == $checkout_failed_order) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected">
                                                    <?php echo $order_status['name']; ?>
                                                </option>
                                                <?php } else { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>">
                                                    <?php echo $order_status['name']; ?>
                                                </option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select></td>
                                    </tr>
                                    
                                    <tr>
                                        <td><?php echo $entry_sort_order; ?></td>
                                        <td><input type="text" name="checkoutapipayment_sort_order" value="<?php echo $checkoutapipayment_sort_order; ?>" size="1" /></td>
                                    </tr>
                                 </table>
                             </div>
                        <h3 class="setting-header">Advance option for Checkout.js</h3>
                        <div class="field">
                            <table class="form">
                                <tr>
                                    <td><?php echo $entry_logo_url; ?></td>
                                    <td><input type="text" name="logo_url" value="<?php echo $logo_url; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $entry_theme_color; ?></td>
                                    <td><input type="text" name="theme_color" value="<?php echo $theme_color; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $entry_button_color; ?></td>
                                    <td><input type="text" name="button_color" value="<?php echo $button_color; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $entry_icon_color; ?></td>
                                    <td><input type="text" name="icon_color" value="<?php echo $icon_color; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $entry_currency_format; ?></td>
                                    <td><select name="currency_format">
                                            <?php if ($currency_format == 'symbol') { ?>
                                            <option value="false" selected="selected"><?php echo $text_symbol; ?></option>
                                            <?php } else { ?>
                                            <option value="true"><?php echo $text_code; ?></option>
                                            <?php } ?>
                                            <?php if ($currency_format == 'code') { ?>
                                            <option value="true" selected="selected"><?php echo $text_code; ?></option>
                                            <?php } else { ?>
                                            <option value="false"><?php echo $text_symbol; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <h3 class="setting-header">Advance option for Frames Js</h3>
                        <table class="form">
                            <tr>
                                <td><?php echo $entry_frames_theme;?></td>
                                <td><select name="frames_theme">
                                        <?php if ($frames_theme == 'standard') { ?>
                                        <option value="standard" selected="selected"><?php echo $text_theme_standard; ?></option>
                                        <?php } else { ?>
                                        <option value="standard"><?php echo $text_theme_standard; ?></option>
                                        <?php } ?>
                                        <?php if ($frames_theme == 'simple') { ?>
                                        <option value="simple" selected="selected"><?php echo $text_theme_simple; ?></option>
                                        <?php } else { ?>
                                        <option value="simple"><?php echo $text_theme_simple; ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo $entry_localpayment_enable; ?></td>
                                <td><select name="localpayment_enable">
                                        <?php if ($localpayment_enable == 'no') { ?>
                                        <option value="no" selected="selected"><?php echo $text_lp_no; ?></option>
                                        <?php } else { ?>
                                        <option value="no"><?php echo $text_lp_no; ?></option>
                                        <?php } ?>
                                        <?php if ($localpayment_enable == 'yes') { ?>
                                        <option value="yes" selected="selected"><?php echo $text_lp_yes; ?></option>
                                        <?php } else { ?>
                                        <option value="yes"><?php echo $text_lp_yes; ?></option>
                                        <?php } ?>
                                        
                                    </select>
                                </td>
                            </tr>
                        </table>

                    </form>
                </div>
            </div>
            <div class="heading">
                <div class="buttons">
                    <a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a>
                    <a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a>
                </div>
            </div>
        </div>
<?php echo $footer;?>