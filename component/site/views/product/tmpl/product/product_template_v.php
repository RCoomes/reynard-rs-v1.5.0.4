<h1 style="margin-top: 0;">{product_number} - {product_name}</h1>
<div class="product-details" style="display: block; padding-left: 0; width: 100%;">
    <div class="detail-left">
        <table style="width: 100%;" border="0">
            <tbody>
            <tr class="odd">
                <td class="attributes-left">Beam Deviation</td>
                <td class="attributes-right">{rs_beam_deviation}</td>
            </tr>
            <tr class="even">
                <td class="attributes-left">Bevel</td>
                <td class="attributes-right">{rs_bevel}</td>
            </tr>
            <tr class="odd">
                <td class="attributes-left">Calibrated Wavelength</td>
                <td class="attributes-right">{rs_calibrated_wavelength}</td>
            </tr>
            <tr class="even">
                <td class="attributes-left">Centration</td>
                <td class="attributes-right">{rs_centration}</td>
            </tr>
            <tr class="odd">
                <td class="attributes-left">Clear Aperture (CA)</td>
                <td class="attributes-right">{rs_clear_aperture}</td>
            </tr>
            <tr class="even">
                <td class="attributes-left">Coating</td>
                <td class="attributes-right">{rs_coating}</td>
            </tr>
            <tr class="odd">
                <td class="attributes-left">Dimensions</td>
                <td class="attributes-right">{rs_dimensions}</td>
            </tr>
            <tr class="even">
                <td class="attributes-left">Edge Finish</td>
                <td class="attributes-right">{rs_edge_finish}</td>
            </tr>
            <tr class="odd">
                <td class="attributes-left">Laser Damage</td>
                <td class="attributes-right">{rs_laser_damage}</td>
            </tr>
            <tr class="even">
                <td class="attributes-left">Material</td>
                <td class="attributes-right">{rs_material}</td>
            </tr>
            <tr class="odd">
                <td class="attributes-left">Parallelism</td>
                <td class="attributes-right">{rs_parallelism}</td>
            </tr>
            <tr class="even">
                <td class="attributes-left">Surface Flatness</td>
                <td class="attributes-right">{rs_surface_flatness}</td>
            </tr>
            <tr class="odd">
                <td class="attributes-left">Surface Quality (SQ)</td>
                <td class="attributes-right">{rs_surface_quality}</td>
            </tr>
            <tr class="even">
                <td class="attributes-left">Operating Temperature</td>
                <td class="attributes-right">{rs_temperature}</td>
            </tr>
            <tr class="odd">
                <td class="attributes-left">Thickness</td>
                <td class="attributes-right">{rs_thickness}</td>
            </tr>
            <tr class="even">
                <td class="attributes-left">Transmitted Wavefront Error (TWE)</td>
                <td class="attributes-right">{rs_transmitted_wavefront_error}</td>
            </tr>
            <tr class="odd">
                <td class="attributes-left">Wavelength</td>
                <td class="attributes-right">{rs_wavelength}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="detail-right">
        <table style="width: 100%;" border="0">
            <tbody>
            <tr>
                <?php if(preg_replace('/<span id=(.*)><\/span>/', '', '{product_price}') != '') { ?>
                    <td align="center" class="product-price-value">{product_price}</td>
                    <td align="center" class="product-quantity-value">{form_addtocart:add_to_cart1}</td>
                    <td align="center" class="product-quote-button"><a href="/contact-us/request-a-quote.html?product_id={product_id}"><img src="components/com_redshop/assets/images/quote.gif" alt="Quote" /></a></td>
                <?php } else { ?>
                    <td align="center" class="product-quote-button"><a href="/contact-us/request-a-quote.html?product_id={product_id}"><img src="components/com_redshop/assets/images/quote.gif" alt="Quote" /></a></td>
                <?php } ?>
            </tr>
            </tbody>
        </table>
        <div class="product_question_link" style="text-align: center;"><span><a
                    href="/contact-us/request-a-quote.html?product_id={product_id}">Request
                    A Quote</a></span></div>
        <div class="product_question_link" style="text-align: center;">{compare_products_button}</div>
        <div class="product_question_link" style="text-align: center;"><a class="redcolorproductimg" href="/index.php?option=com_redshop&view=ask_question&pid={product_id}&tmpl=component&Itemid=0">Ask Question About Product</a></div>
        <div class="product-graph">
            <div style="width: 200px; margin: 0 auto; border: 1px solid #CCC; padding: 2px;">
                <?php if(("{rs_graph_link}" != "") && ("{rs_graph_image}" != "")) { ?>
                    <a href="{rs_graph_link}" target="_blank"><img width="200" height="200" src="{rs_graph_image}" /></a>
                <?php } else { ?>
                    <div style="width: 200px; height: 200px; background: #CCC;"><p style="text-align: center;">No Graph Available</p></div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>