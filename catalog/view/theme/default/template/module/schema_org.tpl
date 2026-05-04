<script type="application/ld+json">
{
"@context": "http://schema.org/",
"@type": "Product",
"name": "<?php echo $product_name; ?>",
"image": "<?php echo STATIC_CONTENT_URL.$product_image; ?>",
"description": "<?php echo $description; ?>",

"aggregateRating": {
"@type": "AggregateRating",
<?php if(!empty($rating)) { ?>
"ratingValue": "<?php echo $rating; ?>",
<?php } ?>
"reviewCount": "<?php echo $reviews; ?>"
},
"offers": {
"@type": "Offer",
"priceCurrency": "INR",
<?php if(!empty($special)) { ?>
"price": "<?php echo $special; ?>",
<?php } else { ?>
"price": "<?php echo $schema_price; ?>",
<?php } ?>
"itemCondition": "http://schema.org/NewCondition",
"availability": "http://schema.org/InStock",
"seller": {
"@type": "Organization",
"name": "WholeSaleBox Internet Pvt. Ltd."
}
}
}
</script>
<script type="application/ld+json">
{
"@context": "http://schema.org",
"@type": "BreadcrumbList",
"itemListElement":
[
{
"@type": "ListItem",
"position": 1,
"item":
{
"@id": "https://www.wholesalebox.in",
"name": "WholesaleBox"
}
},
{
"@type": "ListItem",
"position": 2,
"item":
{
"@id": "<?php echo isset($category_info_schema['url']) ? $category_info_schema['url'] : ''; ?>",
"name": "<?php echo isset($category_info_schema['name']) ? $category_info_schema['name'] : ''; ?>"
}
},
{
"@type": "ListItem",
"position": 3,
"item":
{
"@id": "<?php echo isset($subcategory_info_schema['url']) ? $subcategory_info_schema['url'] : ''; ?>",
"name": "<?php echo isset($subcategory_info_schema['name']) ? $subcategory_info_schema['name'] : ''; ?>"
}
},
{
"@type": "ListItem",
"position": 4,
"item":
{
"@id": "<?php echo $product_info_schema; ?>",
"name": "<?php echo $product_name; ?>"
}
}

]
}
</script>