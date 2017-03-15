{perpagelimit:20}

<div class="category-main-wrapper">
	<div class="category-main-name"><h1>{category_main_name}</h1></div>
	<div class="category-main-image">{category_main_thumb_image_2}</div>
	<div class="category-main-description">{category_main_description}</div>
</div>

{if subcats}
<div class="category-subcats-wrapper">
	<div style="width: 640px; margin: 0 auto;">
	{category_loop_start}
	<div class="category-item">
		<div class="category-image">{category_thumb_image}</div>
		<div class="category-title"><p>{category_name}</p></div>
	</div>
	{category_loop_end}
	</div>
</div>
{subcats end if}

<div class="clear"></div>
<div id="tabs" class="category-tabs-wrapper">
	<ul>
	    <li><a href="#product-tab">Products</a></li>
		<?php if (("{rs_tab1_title_text}" != "") && ("{rs_tab1_article_id}" != "")) { ?><li><a href="#<?php echo strtolower(str_replace(" ", "_", "{rs_tab1_title_text}")) ?>">{rs_tab1_title_text}</a></li><?php } ?>
		<?php if (("{rs_tab2_title_text}" != "") && ("{rs_tab2_article_id}" != "")) { ?><li><a href="#<?php echo strtolower(str_replace(" ", "_", "{rs_tab2_title_text}")) ?>">{rs_tab2_title_text}</a></li><?php } ?>
		<?php if (("{rs_tab3_title_text}" != "") && ("{rs_tab3_article_id}" != "")) { ?><li><a href="#<?php echo strtolower(str_replace(" ", "_", "{rs_tab3_title_text}")) ?>">{rs_tab3_title_text}</a></li><?php } ?>
		<?php if (("{rs_tab4_title_text}" != "") && ("{rs_tab4_article_id}" != "")) { ?><li><a href="#<?php echo strtolower(str_replace(" ", "_", "{rs_tab4_title_text}")) ?>">{rs_tab4_title_text}</a></li><?php } ?>
		<?php if (("{rs_tab5_title_text}" != "") && ("{rs_tab5_article_id}" != "")) { ?><li><a href="#<?php echo strtolower(str_replace(" ", "_", "{rs_tab5_title_text}")) ?>">{rs_tab5_title_text}</a></li><?php } ?>
	</ul>
	<div id="product-tab">
        <table border="0" class="product-table">
            <thead>
                <tr>
                    <td class="product-name-label"><span>Description</span></td>
                    <td class="product-number-label"><span>Product&nbsp;No.</span></td>
                    <td class="product-graph-label"><span>Graph</span></td>
                    <td class="product-price-label"><span>Price</span></td>
                    <td class="product-quantity-label"><span>Qty / Buy / Quote</span></td>
                </tr>
            </thead>
			<tbody>
				{product_loop_start}
                <tr class="master">
                    <td class="product-name-value"><a class="show_hide" href="#" rel="#slidingDiv_{product_id}"><span id="handle" class="plusIcon"></span>{product_s_desc}</a></td>
                    <td class="product-number-value">{product_number}</td>
                    <td class="product-graph-value"><?php if('{producttag:rs_graph_link}' != '') { ?><a href="{producttag:rs_graph_link}" target="_blank">Graph</a><?php } else { ?>&mdash;<?php } ?></td>
                    <?php if(preg_replace('/<span id=(.*)><\/span>/', '', '{product_price}') != '') { ?>
                        <td class="product-price-value">{product_price}</td>
                        <td class="product-quantity-value">{form_addtocart:add_to_cart1}</td>
                    <?php } else { ?>
                        <td class="product-price-value" style="text-align: center;">&mdash;</td>
                        <td class="product-quote-value"><a href="contact-us/request-a-quote.html?product_id={product_id}"><img src="components/com_redshop/assets/images/quote.gif" alt="Quote" /></a></td>
                    <?php } ?>
                </tr>
				<tr class="detail">
                    <?php if(preg_replace('/<span id=(.*)><\/span>/', '', '{product_price}') != '') { ?>
                    <td colspan="5">
                    <?php } else { ?>
                    <td colspan="6">
                    <?php } ?>
						<div id="slidingDiv_{product_id}" class="product-details">
							<div class="detail-left">
								<table width="100%" border="0">
									<tr class="odd"><td class="attributes-left">Substrate Material</td><td class="attributes-right">{producttag:rs_substrate_material}</td></tr>
									<tr class="even"><td class="attributes-left">Absorption</td><td class="attributes-right">{producttag:rs_absorbtion}</td></tr>
									<tr class="odd"><td class="attributes-left">Angle Of Incidence (AOI)</td><td class="attributes-right">{producttag:rs_aoi}</td></tr>
									<tr class="even"><td class="attributes-left">Coating</td><td class="attributes-right">{producttag:rs_coating}</td></tr>
									<tr class="odd"><td class="attributes-left">Wavelength</td><td class="attributes-right">{producttag:rs_wavelength}</td></tr>
									<tr class="even"><td class="attributes-left">Calibrated Wavelength</td><td class="attributes-right">{producttag:rs_calibrated_wavelength}</td></tr>
									<tr class="odd"><td class="attributes-left">Reflection</td><td class="attributes-right">{producttag:rs_reflection}</td></tr>
								</table>
							</div>
							<div class="detail-right">
								<div class="product_question_link" style="text-align: center;"><span><a href="contact-us/request-a-quote.html?product_id={product_id}">Request A Quote</a></span></div>
								<div class="product_question_link" style="text-align: center;"><a class="redcolorproductimg" href="/component/redshop/?view=ask_question&tmpl=component&pid={product_id}">Ask Question About Product</a></div>
								<div class="product-graph">
                                	<div style="float: right; width: 200px; border: 1px solid #CCC; padding: 2px;">
                                    	<?php if(("{producttag:rs_graph_link}" != "") && ("{producttag:rs_graph_image}" != "")) { ?>
                                		<a href="{producttag:rs_graph_link}" target="_blank"><img width="200" height="200" src="{producttag:rs_graph_image}" /></a>
                                        <?php } else { ?>
                                        <div style="width: 200px; height: 200px; background: #CCC;"><p style="text-align: center;">No Graph Available</p>
                                        <?php } ?>
									</div>
								</div>
							</div>
						</div>
					</td>
				</tr>
				{product_loop_end}
			</tbody>
		</table>
		<div class="category_pagination">{pagination}</div>
	</div>
	<?php if (("{rs_tab1_title_text}" != "") && ("{rs_tab1_article_id}" != "")) { ?><div id="<?php echo strtolower(str_replace(" ", "_", "{rs_tab1_title_text}")) ?>">{article:{rs_tab1_article_id}}</div><?php } ?>
	<?php if (("{rs_tab2_title_text}" != "") && ("{rs_tab2_article_id}" != "")) { ?><div id="<?php echo strtolower(str_replace(" ", "_", "{rs_tab2_title_text}")) ?>">{article:{rs_tab2_article_id}}</div><?php } ?>
	<?php if (("{rs_tab3_title_text}" != "") && ("{rs_tab3_article_id}" != "")) { ?><div id="<?php echo strtolower(str_replace(" ", "_", "{rs_tab3_title_text}")) ?>">{article:{rs_tab3_article_id}}</div><?php } ?>
	<?php if (("{rs_tab4_title_text}" != "") && ("{rs_tab4_article_id}" != "")) { ?><div id="<?php echo strtolower(str_replace(" ", "_", "{rs_tab4_title_text}")) ?>">{article:{rs_tab4_article_id}}</div><?php } ?>
	<?php if (("{rs_tab5_title_text}" != "") && ("{rs_tab5_article_id}" != "")) { ?><div id="<?php echo strtolower(str_replace(" ", "_", "{rs_tab5_title_text}")) ?>">{article:{rs_tab5_article_id}}</div><?php } ?>
</div>
