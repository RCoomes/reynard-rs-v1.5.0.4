<?php $category_model = $this->getModel('category'); ?>

<h1 class="category-title">{category_main_name}</h1>
<div class="category-description">{category_main_description}</div>

<div id="categories">
	<?php foreach ($model->_data as $category) { ?>

		<div class="category" style="clear:both;"></div>
		<div class="category" style="clear:both;">

		<?php
		$category_model->_id = $category->category_id;
		$category_model->getData();
		echo '<div><h2 class="category-title" style="text-decoration: underline;">'.$category->category_name.'</h2></div>';
		?>

		<div id="subcategories" style="clear:both;">

		<?php		
		foreach ($category_model->_data as $row) {
			
			$link = JRoute::_ ( 'index.php?option='.$option.'&view=category&cid='.$row->category_id.'&manufacturer_id='.$this->manufacturer_id.'&layout=detail&Itemid='.$tmpItemid );

			$middlepath = REDSHOP_FRONT_IMAGES_RELPATH.'category/';
			$title = " title='".$row->category_name."' ";
			$alt = " alt='".$row->category_name."' ";
			$product_img = REDSHOP_FRONT_IMAGES_ABSPATH."noimage.jpg";
			$linkimage = $product_img;

			if ($row->category_full_image && file_exists ( $middlepath.$row->category_full_image ))
			{
				$product_img = $objhelper->watermark('category',$row->category_full_image,$w_thumb,$h_thumb,WATERMARK_CATEGORY_THUMB_IMAGE,'0');
				$linkimage = $objhelper->watermark('category',$row->category_full_image,'','',WATERMARK_CATEGORY_IMAGE,'0');
			}
			else if (CATEGORY_DEFAULT_IMAGE && file_exists ( $middlepath.CATEGORY_DEFAULT_IMAGE ))
			{
				$product_img = $objhelper->watermark('category',CATEGORY_DEFAULT_IMAGE,$w_thumb,$h_thumb,WATERMARK_CATEGORY_THUMB_IMAGE,'0');
				$linkimage = $objhelper->watermark('category',CATEGORY_DEFAULT_IMAGE,'','',WATERMARK_CATEGORY_IMAGE,'0');
			}

			if (CAT_IS_LIGHTBOX)
			{
				$cat_thumb = "<a class='modal' href='".REDSHOP_FRONT_IMAGES_ABSPATH.$row->category_full_image."' rel=\"{handler: 'image', size: {}}\" ".$title.">";
			}
			else
			{
				$cat_thumb = "<a href='".$link."' ".$title.">";
			}
			$cat_thumb .= "<img src='".$product_img."' ".$alt.$title.">";
			$cat_thumb .= "</a>";

?>

			<div class="category-item">
				<div class="category-image"><?php echo $cat_thumb; ?></div>
				<div class="category_description">
					<h3 class="category-title"><a href='<?php echo $link; ?>'><?php echo $row->category_name; ?></a><br /><a href='<?php echo $link; ?>'><?php echo $row->category_short_description; ?></a></h3>
					<div class="category-description">&nbsp;</div>
				</div>
			</div>

		<?php } ?>
	</div>
    </div>
<?php } ?>

    </div>
