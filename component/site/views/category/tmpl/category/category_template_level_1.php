<h1 class="category-title">{category_main_name}</h1>

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