<table class="tdborder" style="width: 100%;" border="0" cellspacing="0" cellpadding="0">
    <thead>
    <tr>
        <th align="left">{product_number_lbl}</th><th align="left">{product_name_lbl}</th><th width="75">{price_lbl}</th><th width="200">{quantity_lbl}</th><th width="100">{total_price_lbl}</th>
    </tr>
    </thead>
    <tbody>
    {product_loop_start}
    <tr class="tdborder">
        <td>
            {product_number}
        </td>
        <td>
            <div class="cartproducttitle">{product_name}</div>
        </td>
        <td width="75" align="right">{product_price}</td>
        <td width="200" align="center">{update_cart}</td>
        <td width="100" align="right">{product_total_price}</td>
    </tr>
    {product_loop_end}
    </tbody>
</table>
<table style="width: 100%;" border="0" cellspacing="0" cellpadding="0">
    <tbody>
    <tr>
        <td width="50%" valign="top">
            <table border="0">
                <tbody>
                <tr>
                    <td class="cart_customer_note" colspan="2"><br /><b>{customer_note_lbl}:</b><br />{customer_note}</td>
                </tr>
                </tbody>
            </table>
            <br /></td>
        <td width="30%" valign="top"><br /><br />
            <table class="cart_calculations" border="0" width="100%">
                <tbody>

                {if discount}
                <tr class="tdborder">
                    <td><b>Product Subtotal:</b></td>
                    <td width="100" align="right"><div class="product_price">{product_subtotal}</div></td>
                </tr>

                <tr class="tdborder">
                    <td>{discount_lbl}</td>
                    <td width="100" align="right"><div class="product_price">{discount}</div></td>
                </tr>
                {discount end if}

                {if payment_discount}
                <tr class="tdborder">
                    <td><b>{totalpurchase_lbl}:</b></td>
                    <td width="100" align="right"><div class="product_price">{subtotal}</div></td>
                </tr>

                <tr class="tdborder">
                    <td>{payment_discount_lbl}</td>
                    <td width="100">{payment_order_discount}</td>
                </tr>
                {payment_discount end if}

                <tr class="tdborder">
                    <td><b>{total_lbl}:</b></td>
                    <td width="100" align="right"><div class="product_price">{total}</div></td>
                </tr>
                <tr>
                    <td colspan="4" align="center">
                        <strong>Shipping and tax will be added to invoice as required.</strong>
                    </td>
                </tr>
                </tbody>
            </table>
            <br />
            <div style="text-align: center; margin-bottom: 10px;">{terms_and_conditions}</div>
            <div style="padding: 0px 5px;">{checkout_button}</div><div style="float: left; padding: 0px 5px;">{shop_more}</div></td>
    </tr>
    </tbody>
</table>