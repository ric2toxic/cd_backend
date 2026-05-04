
<!DOCTYPE html>
<head>
	<!--//Author Divya Porwal
	 	// February 2017 -->

	<script src="https://cdnjs.cloudflare.com/ajax/libs/free-jqgrid/4.13.6/js/jquery.jqgrid.min.js"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" ></script>
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"   integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E="   crossorigin="anonymous"></script>

    <script  src="catalog/view/javascript/jquery/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
    
</head>
<body>
	
	<h1>Import from API</h1>
	

	<input type="text" name="link" id = "link"><br>
	  <br>
  	  <br>
	  <button id = "submit">Submit</button>
	


	
</body>
<script>
	$('#submit').on('click', function() {
		var data = $('#link').val();
		alert(data);
		$.ajax({
			url:  'index.php?route=inventory/import/readAPILink&token=<?php echo $this->session->data['token']; ?>',
			type: 'GET',
			datatype : 'json',		
			data: {api_link: data},
			success: function (data) {
					
			}
			});
		
						
	
	});

</script>

</html>
