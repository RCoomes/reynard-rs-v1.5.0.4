<div class="category-main-wrapper">
	<div class="category-main-name"><h1>{category_main_name}</h1></div>
	<div class="category-main-description">{category_main_description}</div>
</div>
<div>{if subcats}
	<div>{category_loop_start}
		<div id="categories">
			<div class="subcategory-item">
				<div class="subcategory-image">{category_thumb_image}</div>
				<h2 class="subcategory-title">{category_name}</h2>
			</div>
		</div>
	{category_loop_end}</div>
{subcats end if}</div>