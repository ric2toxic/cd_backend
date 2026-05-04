<?php echo $header; ?>
<div class="container-fluid nopadding store_locator">
	<div class="direction_box container-fluid">
		<div class="locator-heading"><h1>Store Locator</h1></div>
		<div class="stores row">
                        <?php 
                            if(count($store_locators) > 0){ 
                                $i = 0;
                                foreach($store_locators as $sl){
                        ?>
			<div class="<?php echo $sl['location_id'].$i; ?> col-xs-<?php if($i == 0){ echo '12'; } else { echo '6'; } ?>"> <h3 ><?php echo $sl['name']; ?></h3>
			</div>
                        
                        <?php   
                                $i++; }  
                            } 
                        ?>
		</div>
	</div>
        
        <?php 
            if(count($store_locators) > 0){ 
                $i = 0;
                foreach($store_locators as $sl){
        ?>
	<div class="direction-popup container hide" style="z-index: 998;<?php if($sl['image'] != '' ){ ?> background-image: url(<?php echo STATIC_CONTENT_URL_SSL . 'img/dw=140,dh=100,q=90/' . $sl['image'] ?>)<?php } ?>" id="direction-popup-<?php echo $sl['location_id'].$i; ?>" > 

		<h4><?php echo $sl['name'] ?><img src="<?php echo STATIC_CONTENT_URL_SSL;?>img/dw=30,dh=30,q=90/wsb-marker.png"><i class="fa fa-times pull-right" aria-hidden="true" id="crossbtn-<?php echo $sl['location_id'].$i; ?>"></i></h4>
		<p><strong>Address :</strong> <?php echo $sl['address'] ?> <br/>
			<strong>Email :</strong> info@wholesalebox.in<br/>
                        <?php if( $sl['telephone'] != '') { ?>
                            <strong>Phone :</strong> <?php echo $sl['telephone'] ?> <br/>
                        <?php } 
                            if( $sl['timing'] != '' ) {
                        ?>
                           <strong>Opening Time :</strong> <?php echo $sl['timing'] ?> <br/>
                        <?php } ?>
			<strong>Whats App :</strong> (+91) 8696491521 <br/>
                        <?php if( $sl['direction_url'] != '') { ?>
                            <a href="<?php echo $sl['direction_url']; ?>">Get Directions</a>
                        <?php } ?>
                        
                </p>
		<h3>Product Categories in Store</h3>
		<ul>
			<li>
                            <a href="https://www.wholesalebox.in/index.php?route=product/search&search=_jp&category_id=69">Suits</a>,
                            <a href="https://www.wholesalebox.in/index.php?route=product/search&search=_jp&category_id=61">Kurtis</a>,
                            <a href="https://www.wholesalebox.in/index.php?route=product/search&search=_jp&category_id=85">Sarees</a>,
                            <a href="https://www.wholesalebox.in/index.php?route=product/search&search=_jp&category_id=103">Western</a>,
                            <a href="https://www.wholesalebox.in/index.php?route=product/search&search=_jp&category_id=121">Kidswear</a>,
                        </li>
			<li>
                            <a href="https://www.wholesalebox.in/index.php?route=product/search&search=_jp&category_id=119">Menswear</a>,
                            <a href="https://www.wholesalebox.in/index.php?route=product/search&search=_jp&category_id=154">Handicrafts</a>,
                            <a href="https://www.wholesalebox.in/index.php?route=product/search&search=_jp&category_id=105">Accessories</a>,
                            <a href="https://www.wholesalebox.in/index.php?route=product/search&search=_jp&category_id=126">Footwear</a>
                        </li>
		</ul>

	</div>
        
        <?php   
                $i++; }  
            } 
        ?>
	<div id="map"></div>

</div>
<script async defer
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCAwVsw0m7M9vHhGzZmzqPWZUiXblA_Uks&callback=initMap"></script>



<style type="text/css">
	html,
	body {
		height: 100%;
		margin: 0;
		padding: 0;
	}
	.show{display: block;}
	.hide{display: none;}
	.direction-popup ul{padding: 0px;}
	.direction-popup ul li{text-decoration: none; display: block; line-height: 1.3;font-size: 15px;}
	.direction-popup h4{color:#214097; font-size: 22px;}
	.direction-popup p{line-height: 2; font-size: 15px;}
	.direction-popup h4 i{color: #ccc; font-size: 30px; margin-top: -15px; cursor: pointer;}
	.direction-popup {
		-moz-user-select: text;
		border: 1px solid rgba(0, 0, 0, 0.2);
		border-radius: 2px;
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
		color: #000;
		outline: medium none;
		overflow: hidden;
		z-index: 990;
		height: auto;
		width: auto;
		padding: 20px;
		background:  #fff no-repeat right bottom ;

	}
        .direction-popup h3{font-size: 20px}  
	
	.stores h3{background: #fff; text-align: center; padding: 4px 2px; color: #282828; box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.16), 0 0 0 1px rgba(0, 0, 0, 0.08); cursor: pointer; margin: 6px 0px!important; font-size: 16px;}
	.stores h3 i{font-size: 23px; cursor: pointer;}
	.stores p{padding: 0px 20px;}
	#map {height: 600px;width: 100%; margin:auto;}
	.store_locator{	position: relative;}
	.direction_box{background:#fff; box-shadow: 2px 0 3px 0 rgba(0,0,0,0.2); z-index: 100;top: 0px;left: 10%; overflow-x: hidden;}
	.locator-heading{color: #4a4a4a;text-align: center;}
</style>

<script type="text/javascript">
    function initMap() {
        
        var store_location = '<?php echo json_encode($store_locators); ?>'; 
        var store_location_object = JSON.parse(store_location);        
        
        var locations = [];
        
        for (i in store_location_object) {
            var tempArray = []; 
            tempArray[0] = '<strong>'+store_location_object[i].name+'</strong><br>'+store_location_object[i].address+'<br>'
            if(store_location_object[i].direction_url != ''){
                tempArray[0] += '<a href="'+store_location_object[i].direction_url+'">Get Directions</a>'
            }
            tempArray[1] = store_location_object[i].lat;
            tempArray[2] = store_location_object[i].long;
            
            locations.push(tempArray);
        }

        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 5,
            center: new google.maps.LatLng(20.5937, 78.9629),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var infowindow = new google.maps.InfoWindow({}); 

        var marker, i;
        
        for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                map: map,
                icon: '<?php echo STATIC_CONTENT_URL_SSL;?>wsb-marker.png'
            });
            
            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                return function () {
                    infowindow.setContent(locations[i][0]);
                    infowindow.open(map, marker);
                }
            })(marker, i));
        }
    }
</script>

<script type="text/javascript"> 
        
        var store_location = '<?php echo json_encode($store_locators); ?>'; 
        var store_location_object = JSON.parse(store_location);        
        
        for (i in store_location_object) {
            
            var show_click = "."+store_location_object[i].location_id+i;
            var div_name = "#direction-popup-"+store_location_object[i].location_id+i;
            var hide_click = "#crossbtn-"+store_location_object[i].location_id+i;
            
            show_popoup(show_click,div_name,hide_click);
        }
        
        function show_popoup(show_click,div_name,hide_click){
            
            $(show_click).click(function(){   
                $(".direction-popup").removeClass('show').addClass('hide'); 
                $(div_name).removeClass('hide').addClass('show');
            });
            
            $(hide_click).click(function(){  
                $(div_name).removeClass('show').addClass('hide');
            });
        }
        
</script>

<?php echo $footer; ?>
