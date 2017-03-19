<hr />
<h1 style="margin: 0;">{cart_lbl}</h1>
<hr />
<table class="tdborder" style="width: 100%;" border="0">
    <thead>
    <tr>
        <th class="cartproductnumber">{product_number_lbl}</th>
        <th class="cartproductname">{product_name_lbl}</th>
        <th class="cartproductprice">{price_lbl} </th>
        <th class="cartquantity">{quantity_lbl}</th>
        <th class="cartproducttotalprice">{total_price_lbl}</th>
    </tr>
    </thead>
    <tbody>
    {product_loop_start}
    <tr class="tdborder">
        <td class="cartproductnumber">{product_number}</td>
        <td class="cartproductname">
            <div class="cartproducttitle">{product_name}</div>
            <div class="cartattribut">{product_attribute}</div>
            <div class="cartaccessory">{product_accessory}</div>
            <div class="cartwrapper">{product_wrapper}</div>
            <div class="cartuserfields">{product_userfields}</div>
        </td>
        <td class="cartproductprice">{product_price}</td>
        <td class="cartquantity">
            <table border="0">
                <tbody>
                <tr>
                    <td>{update_cart}</td>
                    <td>{remove_product}</td>
                </tr>
                </tbody>
            </table>
        </td>
        <td class="cartproducttotalprice">{product_total_price}</td>
    </tr>{product_loop_end}</tbody>
</table>
<p><strong class="discount_text"><br/></strong></p>
<table style="width: 100%;" border="0">
    <tbody>
    <tr>
        <td width="50%" valign="top">
            <table border="0">
                <tbody>
                <tr>
                    <td>{update}</td>
                    <td>{empty_cart}</td>
                </tr>
                <tr>
                    <td class="cart_discount_form" colspan="2">{discount_form_lbl}{coupon_code_lbl}<br />{discount_form}</td>
                </tr>
                </tbody>
            </table>
            <br />
        </td>
        <td width="50%" align="right" valign="top"><br /><br />
            <table class="cart_calculations" border="0"  style="width: 100%">
                <tbody>
                {if discount}
                <tr class="tdborder">
                    <td>{discount_lbl}</td>
                    <td  style="width: 100%">{discount}</td>
                    <td>{discount_lbl}</td>
                    <td  style="width: 100%">{discount_excl_vat}{discount_denotation}</td>
                </tr>
                {discount end if}
                {if payment_discount}<tr>
                    <td>{payment_discount_lbl}</td>
                    <td style="width: 100%;">{payment_order_discount}</td>
                </tr>
                {payment_discount end if}
                <tr class="tdborder">
                    <td><b>{total_lbl}:</b></td>
                    <td style="width: 100px; text-align: right;">
                        <div class="product_price">{total}</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4"><strong>{denotation_label}</strong></div></td>
                </tr>
                <tr>
                    <td align="center" colspan="4">
                        <strong>Shipping and tax will be added to invoice as required.</strong>
                    </td>
                </tr>
                </tbody>
            </table>
            <br />
            <div style="padding: 0px 5px;">
                <div style="float:left;" class="shop_more_button">{shop_more}</div>
                <div style="float:right;" class="checkout_button">{checkout}</div>
            </div>
        </td>
    </tr>
    </tbody>
</table>
