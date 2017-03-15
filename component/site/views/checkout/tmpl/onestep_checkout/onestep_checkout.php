<hr />
<h1 style="margin: 0;"><?php echo JText::_('Payment Details' );?></h1>
<hr />

<table border="0" cellspacing="2" cellpadding="2" width="100%">
    <tbody>
    <tr>
        <td style="width: 33.33%; vertical-align: top;">
            <fieldset class="adminform">
                <legend style="padding: 0;"><b>{billing_address_information_lbl}</b> {edit_billing_address}</legend>
                {billing_address}
            </fieldset>
        </td>
        <td style="width: 33.33%; vertical-align: top;">
            <fieldset class="adminform">
                <legend style="padding: 0;"><b>{shipping_address_information_lbl}</b></legend>
                {shipping_address}
            </fieldset>
        </td>
        <td style="width: 33.33%; vertical-align: top;">
            <table border="0" class="table">
                <tbody>
                <tr>
                    <td>{shippingbox_template:shipping_box}</td>
                </tr>
                <tr>
                    <td>{shipping_template:shipping_method}</td>
                </tr>
                <tr>
                    <td>{payment_template:payment_method}</td>
                </tr>
              </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3" style="text-align: right; padding: 5px 21px;">
        	<b>Would you like to add a reference # or PO # to complete this order?</b> {requisition_number}
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <table border="0" class="table" style="width: 100%">
                <tbody>
                <tr>
                    <td>
                        <table border="0" class="table" style="width: 100%">
                            <tbody>
                            <tr>
                                <td>{checkout_template:checkout}</td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>