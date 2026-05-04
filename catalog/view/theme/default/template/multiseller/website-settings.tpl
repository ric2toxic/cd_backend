<?php echo $header_seller; ?>
<div class="container">
    <?php
        if($statusclass != 'warning'){
    ?>
  	<ul class="breadcrumb">
    	<?php foreach ($breadcrumbs as $breadcrumb) { ?>
    		<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    	<?php } ?>
  	</ul>
    <?php }else{ ?>
	<div class="big-heading">
		<h1><?php echo $ms_account_register_seller; ?></h1>
	</div>
	<div class="stepwizard col-md-offset-3">
		<div class="stepwizard-row setup-panel">
			<div class="stepwizard-step">
				<a href="#step-1" type="button" class="btn btn-primary btn-circle" disabled="disabled">1</a>
				<p>Create Account</p>	
			</div>
			<div class="stepwizard-step">
				<a href="#step-2" type="button" class="btn btn-primary btn-circle" >2</a>
				<p>Seller Information</p>
			</div>
			<div class="stepwizard-step">
				<a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
				<p>Congratulations!</p>
			</div>
		</div>
	</div>
    <?php } ?>
    <div class="pull-right col-sm-4">
    <form id="ms-website_store_change" class="ms-form form-horizontal">
    <?php if(!empty($strs) && $count > 1){ ?>
    	<div class="control-label">
    		<div class="col-sm-5"><h4>Select Store</h4></div>
	    	<div class="col-sm-7">
		    	<select class ="form-control seller_store col-sm-6">
					<?php foreach ($stores as $value) { ?>
					<option  value="<?php echo $value['store_id']; ?>" 
					<?php if($value['store_id'] == $select_seller_store_id){ ?> selected
					<?php } ?>
					>
						<a><label class="control-label"><?php echo $value['name']; ?>
				        </label></a>
					</option>
					<?php } ?>
				</select>
			</div>
		</div>
	<?php } ?>
	</form>
    </div>

	<?php if (isset($error_warning) && $error_warning) { ?>
	 	<div class="alert alert-danger warning main"><?php echo $error_warning; ?></div>
	<?php } ?>

	<?php if (isset($success) && ($success)) { ?>
		<div class="alert alert-success"><?php echo $success; ?></div>
	<?php } ?>
  	<div class="row"><?php echo $column_left; ?>
	    <?php if ($column_left && $column_right) { ?>
	    <?php $class = 'col-sm-6'; ?>
	    <?php } elseif ($column_left || $column_right) { ?>
	    <?php $class = 'col-sm-9'; ?>
	    <?php } else { ?>
	    <?php $class = 'col-sm-12'; ?>
	    <?php } ?>
	    <div id="content" class="ms-product <?php echo $class; ?> nopadding ms-website-settings color_white box_sha"><?php echo $content_top; ?>
	    	<div class= "col-sm-3 nopadding">
				<ul class="nav nav-tabs1 left_seller_container" style= "margin-bottom:2px;">
				<?php /*
		          	<li><a href="#seller-category" data-toggle="tab" style="font-size:18px"><strong><?php echo $categories; ?></strong></a></li>
		          	*/
		          	?>
		          	<li><a href="#website-themes" data-toggle="tab" style="font-size:18px"><strong><?php echo $website_themes; ?></strong></a></li>
		          	<li><a href="#website-pages" data-toggle="tab" style="font-size:18px"><strong><?php echo $website_pages; ?></strong></a></li>
		          	<li><a href="#social-profile" data-toggle="tab" style="font-size:18px"><strong><?php echo $social_profile; ?></strong></a></li>
		          	<li><a href="#contact-details" data-toggle="tab" style="font-size:18px"><strong><?php echo $contact_details; ?></strong></a></li>
		          	<li><a href="#promotional_boxes" data-toggle="tab" style="font-size:18px"><strong>Promotional Boxes</strong></a></li>
		          	<li><a href="#footer" data-toggle="tab" style="font-size:18px"><strong>Footer</strong></a></li>
		          	<li><a href="#others" data-toggle="tab" style="font-size:18px"><strong>Others</strong></a></li>
		    	</ul>
		    </div>
		    <div class="col-sm-9">
		    	<div class="tab-content">
			        <div class="tab-pane" id="seller-category">
						<div class="form-group">
			                <div class="col-sm-12">
			                <div class="form-group">
			                <ul class="expectation_list">
			                	<?php $inc = 5; foreach($category as $value){ 
			                		foreach($seller_category as $seller_cat){ 
						                    if($value['category_id'] == $seller_cat['category_id']){
						                      $is_checked = "checked='checked'";
						                      break;
						                    }else{
						                      $is_checked = "";
						                    }
						            }
						            ?>
						            <li class="col-sm-4">
							        <div class="checkbox">
					                    <input id="checkbox-7-<?php echo $inc; ?>" type="checkbox" value="<?php echo $value['category_id']?>" name="category[]" <?php echo $is_checked;?>/>
					                    <label for="checkbox-7-<?php echo $inc; ?>"><span><?php echo $value['name']; ?></span></label>
					                </div>
					                </li>
							    <?php $inc++;  }  ?>
							    </ul>
			                </div>
			                </div>
			            </div>
			            <div class="buttons col-sm-4">
					        <input type="submit" class="seller_category btn btn-primary" value="Save"/>
					    </div>
			        </div>
			        <div class="tab-pane active" id="website-themes">
			        	<form id="ms-website_themes" class="ms-form form-horizontal">
							<div class="row contentblock col-sm-7">	
								<h3 class="subheading"></h3>
								<?php /* ?>
								<div class="form-group">
									<label class="col-sm-4 control-label"><h4><?php echo $text_themes_color; ?></h4></label>	
									<div class="col-sm-8">
									<select class ="form-control page_theme">
									<option><label><?php echo $text_select_theme_color; ?></label></option>
										<?php foreach($page_themes as $value){  ?>
										<option value="<?php echo $value['slug']; ?>">
											<a><label class="control-label"><?php echo $value['name']; ?>
									        </label></a>
										</option>
										<?php } ?>
									</select>
									</div>
								</div>
								<?php */ ?>
								<div class="form-group">
									<label class="col-sm-4 control-label"><h4><?php echo $text_desktop_logo; ?></h4><p class="photo_detail"><?php echo $text_logo_detail; ?></p></label>
					                <div class="col-sm-8">
					                  	<a id="thumb-image1" class="desktop_website_logo" data-toggle="image" type="file" ><img src="<?php echo $seller_logo['thumb']; ?>" alt="" title="" /></a>
					                  	<input type="hidden" class="config_logo" name="config_logo" value="<?php echo $seller_logo['image']; ?><?php //echo $image; ?>" id="input-image1" />
					                </div>
				            	</div>
				            	<div class="form-group">
									<label class="col-sm-4 control-label"><h4><?php echo $text_mobile_logo; ?></h4><p class="photo_detail"><?php echo $text_mobile_logo_detail; ?></p></label>
									<div class="col-sm-8">
					                  	<a id="thumb-image2" data-toggle="image" class="mobile_website_logo" ><img src="<?php echo $seller_mobile_logo['thumb']; ?>" alt="" title="" /></a>
					                  	<input type="hidden" class="mobile_logo" name="mobile_logo" value="<?php echo $seller_mobile_logo['image']; ?><?php //echo $image; ?>" id="input-image2" />
					                </div>
				            	</div>
								<div class="form-group">
									<label class="col-sm-4 control-label" for="input-icon"><h4><?php echo $text_favicon; ?></h4><p class="photo_detail"><?php echo $text_favicon_detail; ?></p></label>
									<div class="col-sm-8">
										<a id="thumb-image3" data-toggle="image"><img src="<?php echo $seller_favicon['thumb']; ?>" alt="" title="" /></a>
										<input type="hidden" name="config_icon" class="config_icon" value="<?php echo $seller_favicon['image']; ?>" id="input-image3" />
									</div>
								</div>
							</div>
			            	<div class="form-group col-sm-5 user_logo" style="display: block;">
			            		<h3 class="subheading"></h3>
			            		<a class="theme_show"></a>
							</div>
		            	</form>
		            	<div class="buttons col-sm-3">
					        <input type="submit" class="save_theme btn btn-primary" value="Save" data-theme=""/>
					    </div>
		            </div>
		            <div class="tab-pane" id="website-pages">
						<input type="submit" class="pull-right addnewpage websitepages_" data-class="new_page" value="<?php echo $text_add_new_page; ?>" />
						<div style="display: none">
							<div id="websitepages_new_page">
								<div class="form-group">
									<div class="col-sm-4">
										<label><?php echo $text_title; ?></label>
										<input type="text" value="" class="new_page_title form-control" placeholder="<?php echo $entry_enter_title; ?>">
									</div>
									<div class="col-sm-4">
										<label><?php echo $text_status; ?></label>
										<select name="seller[page_status]" class="new_page_status form-control">
											<option value="1" selected="selected"><?php echo $text_enable; ?></option>
											<option value="0"><?php echo $text_disable; ?></option>
										</select>
									</div>
						        </div>
						        <div class="form-group message_cont">
							        	<label><?php echo $entry_message_contant; ?></label>
							        </div>
						        <div class="form-group">
									<textarea class="textarea_new_page form-control" data-class="new_page" rows="10" placeholder="Enter Your content"></textarea>
								</div>
	                        	<input type="button" class="save_new_page btn btn-primary" value="Save" />
	                        </div>
						</div>

		            	<ul id ="sortable" style="list-style: none;">
							<table class="table table-bordered">
		            		<?php $i= 1;
		            	if(!empty($seller_page_info)){
		            	foreach($seller_page_info as $value){
		            	$dashpage = str_replace(" ","_",$value['title']); ?>
			            	<li id="item-<?php echo $i; ?>">
								<tr>
									<td>
										<?php echo $i; ?>
									</td>
									<td>
										<label><?php echo $value['title']; ?></label>
									</td>
									<td>
										<a class="websitepages_" data-class="<?php echo $dashpage; ?>">EDIT&nbsp&nbsp<i class="fa fa-pencil"></i></a>
									</td>
									<td>
										<a class="delete_page" data-class="<?php echo $dashpage; ?>" data-id = "<?php echo $value['id']; ?>">DELETE&nbsp&nbsp<i class="fa fa-trash"></i></a>
									</td>
								</tr>

			            	<div style="display:none">
			            		<div id="websitepages_<?php echo $dashpage; ?>">
									<div class="form-group">
										<div class="col-sm-4">
											<label><?php echo $text_title; ?></label>
											<input type="text" value="<?php echo $value['title']; ?>" class="page_title_<?php echo $dashpage; ?> form-control" placeholder="<?php echo $entry_enter_title; ?>">
										</div>
										<div class="col-sm-4">
											<label><?php echo $text_status; ?></label>
											<select name="seller[page_status]" class="page_status_<?php echo $dashpage; ?> form-control">
												<option value="1" <?php if($value['status'] == 1){ ?> selected<?php } ?> >
												<?php echo $text_enable; ?></option>
												<option value="0" <?php if($value['status'] == 0){ ?> selected<?php } ?>> <?php echo $text_disable; ?></option>
											</select>
										</div>
							        </div>
							        <div class="form-group message_cont">
							        	<label><?php echo $entry_message_contant; ?></label>
							        </div>
							        <div class="form-group">
		                        		<textarea name="seller['<?php echo $dashpage; ?>_page']" class="textarea_<?php echo $dashpage; ?>"><?php echo $value['content']; ?></textarea>
		                        	</div>
		                        	<input type="button" data-class="<?php echo $dashpage; ?>"data-id="<?php echo $value['id']; ?>" class="save_page btn btn-primary" value="Save" />
		                        </div>
		                    </div><br />

		        		<?php $i++;  } }else { ?>
						<h3><?php echo $no_pages; ?></h3>
						<?php } ?>
			            	</li>
							</table>
		            	</ul>
			        </div>
		            <div class="tab-pane" id="social-profile">
		            	<form id="ms-sellersocialdetail" class="ms-form form-horizontal">
		            		<div class="row contentblock ">
		            			<div class="col-md-7">
		            				<h3 class="subheading"></h3>
									<div class="form-group">
										<label class="col-sm-4 control-label"><h5><?php echo $text_facebook; ?></h5></label>
										<div class="col-sm-8">
											<input type="text" class="facebook form-control"  name="seller[facebook]" value="<?php echo $facebook; ?>" placeholder="Enter Your Facebook URL" />
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label"><h5><?php echo $text_twitter; ?></h5></label>
										<div class="col-sm-8">
											<input type="text" class="twitter form-control"  name="seller[twitter]" value="<?php echo $twitter; ?>" placeholder="Enter Your Twitter URL" />
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label"><h5><?php echo $text_instagram; ?></h5></label>
										<div class="col-sm-8">
											<input type="text" class="instagram form-control"  name="seller[instagram]" value="<?php echo $instagram; ?>" placeholder="Enter Your Instagram URL" />
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label"><h5><?php echo $text_google; ?></h5></label>
										<div class="col-sm-8">
											<input type="text" class="google form-control"  name="seller[google]" value="<?php echo $google; ?>" placeholder="Enter Your Google+ URL" />
										</div>
									</div>
								</div>
							</div>
						</form>
						<div class="buttons col-sm-3">
					        <input type="submit" class="save_social_profile btn btn-primary" value="Save"/>
					    </div>
		            </div>
		            <div class="tab-pane" id="contact-details">
		            	<form id="ms-sellerdetail" method="POST" action="index.php?route=seller/website-settings/contact_info" class="ms-form form-horizontal">
				    		<div class="row contentblock ">
					    		<div class="col-md-7">
									<h3 class="subheading"></h3>
						            <div class="form-group">
						                <label class="col-sm-4 control-label"><h5><?php echo $text_email; ?></h5></label>
						                <div class="col-sm-8">
						                    <input type="text" class="form-control"  name="seller[config_email]" value="<?php echo $store_email; ?>" placeholder="<?php echo $entry_email; ?>" />
						                </div>
						            </div>
						            <div class="form-group">
						                <label class="col-sm-4 control-label"><h5><?php echo $text_landline_no; ?></h5></label>
						                <div class="col-sm-8">
						                    <input type="text" class="form-control"  name="seller[config_telephone]" value="<?php echo $store_landline_no; ?>" placeholder="<?php echo $entry_landline_no; ?>" />
						                </div>
						            </div>
						            <div class="form-group">
						                <label class="col-sm-4 control-label"><h5><?php echo $text_mobile_no; ?></h5></label>
						                <div class="col-sm-8">
											<input type="text" class="form-control"  name="seller[seller_mobile]" value="<?php echo $store_mobile_no; ?>" placeholder="<?php echo $entry_mobile_no; ?>"/>
						                </div>
						            </div>
						            <div class="form-group">
							            <label class="col-sm-4 control-label" for="input-telephone"></label>
							            <div class="col-sm-8">
											<input type="checkbox" id="showWhatsAppNumber" value="WhatsApp Number" /><?php echo $text_not_whats_app_no; ?>
							            </div>
							        </div>
							        <div class="form-group" id="showWhatsAppNumberBox" style="display:none;">
							            <label class="col-sm-4 control-label" for="input-telephone"><h5><?php echo $text_whatsapp_no; ?></h5></label>
							            <div class="col-sm-8">
							              <input type="tel" value="<?php echo $store_whatsapp_no; ?>" id="input-whatsapp-telephone" name="seller[seller_whatsapp_no]" placeholder="<?php echo $entry_whatsapp_no; ?>" class="form-control" />
							            </div>
							        </div>
						            <div class="form-group">
						                <label class="col-sm-4 control-label"><h5><?php echo $text_address; ?></h5></label>
						                <div class="col-sm-8">
						                    <input type="textarea" class="form-control"  name="seller[config_address]" value="<?php echo $store_address; ?>" placeholder="<?php echo $entry_address; ?>" />
						                </div>
						            </div>
						        </div>
					        </div>
					        <div class="buttons">
					        	<input type="submit" id="ms-profile-button" class="btn btn-primary" value="Save"/>
					        </div>
				    	</form>
		            </div>
		            <div class="tab-pane" id="promotional_boxes">
						<form id="ms-sellerdetail" method="post" action="index.php?route=seller/website-settings/promotional_boxes" class="ms-form form-horizontal">
				    		<div class="row contentblock">
				    			<div class="col-md-12">
									<h3 class="subheading"> Promotional Boxes </h3>
									<div class="form-group">
										<label class="col-sm-4 control-label"><h4>Show or Hide Promotional box</h4></label>
										<div class="col-sm-8">
											<select class ="form-control show_hide_promo_select" name="show_hide_promo_select">
												<option value="1" <?php if($seller_promo_box == 1){ ?> selected="selected" <?php }?> >Show</option>
												<option value="0" <?php if($seller_promo_box == 0){ ?> selected="selected" <?php } ?> >Hide</option>
											</select>
										</div>
									</div>
									<div id="main_promo">
										<hr class="line_under_pro_boxes">
										<div class="form-group">
											<label class="col-sm-2 control-label"><h4>Box 1</h4></label>
											<div class="col-sm-10">
												<div class="col-sm-3 pull-left">
													<a id="promo_box_1" class="desktop_website_logo" data-toggle="image" ><img src="<?php echo $seller_promo_image_1; ?>" alt="" title="" /></a>
						                  			<input type="hidden" class="promo_box" name="promo_box[1][image]" value="<?php echo $seller_promo_image_1; ?>" id="promo_box-image1" />
												</div>
												<div class="col-sm-8 pull-left">
														<input type="text" class="form-control" name="promo_box[1][name]" value="<?php echo $seller_promo_name_1; ?>" placeholder="Enter Promotional message"/>
														<input type="text" class="form-control" name="promo_box[1][link]" value="<?php echo $seller_promo_link_1; ?>" placeholder="Enter Link URL (' http://www.wholesalebox.in ')"/>
													<?php if(isset($seller_promo_show_1) && !empty($seller_promo_show_1)){
															$checked = "checked='checked'";
									                }else{
															$checked = '';
									                } ?>
													<div class="checkbox show_hide_promo">
									                    <input name="promo_box[1][show]" class="display_none" id="checkbox-7-a" type="checkbox" value="1" <?php echo $checked; ?> />
									                    <label for="checkbox-7-a"><span>Show</span></label>
									                </div>
												</div>
											</div>
										</div>
										<hr class="line_under_pro_boxes">
										<div class="form-group">
											<label class="col-sm-2 control-label"><h4>Box 2</h4></label>
											<div class="col-sm-10">
												<div class="col-sm-3 pull-left">
													<a id="promo_box_2" class="desktop_website_logo" data-toggle="image" ><img src="<?php echo $seller_promo_image_2; ?>" alt="" title="" /></a>
						                  			<input type="hidden" class="promo_box" name="promo_box[2][image]" value="<?php echo $seller_promo_image_2; ?>" id="promo_box-image2" />
												</div>
												<div class="col-sm-8 pull-left">
													<input type="text" class="form-control" name="promo_box[2][name]" value="<?php echo $seller_promo_name_2; ?>" placeholder="Enter Promotional message"/>
													<input type="text" class="form-control" name="promo_box[2][link]" value="<?php echo $seller_promo_link_2; ?>" placeholder="Enter Link URL (' http://www.wholesalebox.in ')"/>
													<?php if(isset($seller_promo_show_1) && !empty($seller_promo_show_2)){
															$checked = "checked='checked'";
									                }else{
															$checked = '';
									                } ?>
													<div class="checkbox show_hide_promo">
									                    <input name="promo_box[2][show]" class="display_none" id="checkbox-7-b" type="checkbox" value="1" <?php echo $checked; ?> />
									                    <label for="checkbox-7-b"><span>Show</span></label>
									                </div>
												</div>
											</div>
										</div>
										<hr class="line_under_pro_boxes">
										<div class="form-group">
											<label class="col-sm-2 control-label"><h4>Box 3</h4></label>
											<div class="col-sm-10">
												<div class="col-sm-3 pull-left">
													<a id="promo_box_3" class="desktop_website_logo" data-toggle="image" ><img src="<?php echo $seller_promo_image_3; ?>" alt="" title="" /></a>
						                  			<input type="hidden" class="promo_box" name="promo_box[3][image]" value="" id="promo_box-image3" />
												</div>
												<div class="col-sm-8 pull-left">
													<input type="text" class="form-control" name="promo_box[3][name]" value="<?php echo $seller_promo_name_3; ?>" placeholder="Enter Promotional message"/>
													<input type="text" class="form-control" name="promo_box[3][link]" value="<?php echo $seller_promo_link_3; ?>" placeholder="Enter Link URL (' http://www.wholesalebox.in ')"/>
													<?php if(isset($seller_promo_show_3) && !empty($seller_promo_show_3)){
															$checked = "checked='checked'";
									                }else{
															$checked = '';
									                } ?>
													<div class="checkbox show_hide_promo">
								                    	<input name="promo_box[3][show]" class="display_none" id="checkbox-7-c" type="checkbox" value="1" <?php echo $checked; ?> />
								                    	<label for="checkbox-7-c"><span>Show</span></label>
								                	</div>
												</div>
											</div>
										</div>
										<hr class="line_under_pro_boxes">
										<div class="form-group">
											<label class="col-sm-2 control-label"><h4>Box 4</h4></label>
											<div class="col-sm-10">
												<div class="col-sm-3 pull-left">
													<a id="promo_box_4" class="desktop_website_logo" data-toggle="image" ><img src="<?php echo $seller_promo_image_4; ?>" alt="" title="" /></a>
						                  			<input type="hidden" class="promo_box" name="promo_box[4][image]" value="" id="promo_box-image4" />
												</div>
												<div class="col-sm-8 pull-left">
													<input type="text" class="form-control" name="promo_box[4][name]" value="<?php echo $seller_promo_name_4; ?>" placeholder="Enter Promotional message"/>
													<input type="text" class="form-control" name="promo_box[4][link]" value="<?php echo $seller_promo_link_4; ?>" placeholder="Enter Link URL (' http://www.wholesalebox.in ')"/>
													<?php if(isset($seller_promo_show_4) && !empty($seller_promo_show_4)){
															$checked = "checked='checked'";
									                }else{
															$checked = '';
									                } ?>
													<div class="checkbox show_hide_promo">
									                    <input name="promo_box[4][show]" class="display_none" id="checkbox-7-d" type="checkbox" value="1" <?php echo $checked; ?> />
									                    <label for="checkbox-7-d"><span>Show</span></label>
									                </div>
												</div>
											</div>
										</div>
										<div class="buttons col-sm-3">
									        <input type="submit" class="promotional_boxes btn btn-primary" value="Save"/>
									    </div>
									</div>
								</div>
				    		</div>
				    	</form>
					</div>
		            <div class="tab-pane" id="footer">
		            	<form id="ms-website_themes" class="ms-form form-horizontal">
							<div class="row contentblock col-sm-7">
								<h3 class="subheading"></h3>
								<div class="form-group">
									<label class="col-sm-4 control-label"><h4><?php echo $text_payment_cards_banner; ?></h4><p class="photo_detail"><?php echo $text_payment_cards_banner_detail; ?></p></label>
					                <div class="col-sm-8">
					                  	<a id="thumb-image_pcb" class="payment_cards_banner" data-toggle="image" type="file" ><img src="<?php echo $payment_cards_banner['thumb']; ?>" alt="" title="" /></a>
					                  	<input type="hidden" class="payment_banner" name="payment_banner" value="<?php echo $payment_cards_banner['image']; ?>" id="input-image_pcb" />
					                </div>
					            </div>
					            <div class="form-group">
					            	<label class="col-sm-4 control-label"><h4><?php echo $text_shipping_banner; ?></h4><p class="photo_detail"><?php echo $text_shipping_banner_detail; ?></p></label>
					                <div class="col-sm-8">
					                  	<a id="thumb-image_sb" class="shipping_banners" data-toggle="image" type="file" ><img src="<?php echo $shipping_banners['thumb']; ?>" alt="" title="" /></a>
					                  	<input type="hidden" class="shipping_banner" name="shipping_banner" value="<?php echo $shipping_banners['image']; ?>" id="input-image_sb" />
					                </div>
				            	</div>
				            	<div class="form-group">
					                <label class="col-sm-4 control-label"><h4>Copyright</h4></label>
					                <div class="col-sm-8">
					                    <input type="text" class="form-control copyright"  name="seller[copyright]" value="<?php echo $copyright; ?>" placeholder="Enter Your Copyright Text" />
					                </div>
					            </div>
							</div>
						</form>
						<div class="buttons col-sm-4">
					        <input type="submit" class="save_footer btn btn-primary" value="Save"/>
					    </div>
					</div>
		            <div class="tab-pane" id="others">
		    			<form id="ms-sellerdetail" class="ms-form form-horizontal">
				    		<div class="row contentblock ">
					    		<div class="col-md-12">
									<h3 class="subheading"></h3>
									<div class="form-group">
										<label class="col-sm-3 control-label"><h4>Store Type</h4></label>
										<div class="col-sm-8">
											<select name="store_type" class="form-control store_type">
												<?php foreach($store_type_values as $key=>$val){
												    $selected = '';
													if($key == $store_type){
														$selected='selected="selected"';
													}
												?>
												<option value="<?php echo $key;?>" <?php echo $selected;?>><?php echo $val;?></option>
												<?php } ?>


											</select>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3 control-label"><h4>Default store</h4></label>
										<div class="col-sm-8">
											<select name="default_store" class="form-control default_store">
												<option value="set" <?php if(isset($seller_default_store) && $seller_default_store == "set"){ ?>selected="selected" <?php } ?> >Wholesale Set</option>
												<option value="single" <?php if(isset($seller_default_store) && $seller_default_store == "single"){ ?>selected="selected" <?php } ?> >Single</option>
											</select>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3 control-label"><h4>Minimum Cart Value</h4></label>
										<div class="col-sm-8">
											<input type="text" class="form-control config_cart_limit"  name="seller[config_cart_limit]" value="<?php echo $config_cart_limit; ?>" placeholder="Enter Minimum Cart Value" />
										</div>
									</div>

								</div>
						    </div>
						</form>
						<div class="buttons col-sm-3">
					        <input type="submit" class="others btn btn-primary" value="Save"/>
					    </div>
					</div>
		        </div>
	        </div>
		</div>
    	<?php echo $content_bottom; ?>
    </div>
    <?php echo $column_right; ?>
</div>
<script type="text/javascript">


	function hide_promo(value){
		if(value == 0){
			document.getElementById("main_promo").style.opacity = "0.2";
			$('#main_promo').click(function(event){
				if($(".show_hide_promo_select").val() == '0'){
					return false;
				}else{
					return true;
				}
			});
		}else{
			document.getElementById("main_promo").style.opacity = "1.0";
		}
	}

	$(document).ready(function() {

		if($(".show_hide_promo_select").val() == '0'){
			hide_promo(0);
		}
		$(".show_hide_promo_select").change(function(){
			var value = $(this).val();
			hide_promo(value);
			$.ajax({
				url: 'index.php?route=seller/website-settings/hide_promo',
				type: 'POST',
				dataType: 'html',
				data: {show_hide_promo_select: value},
				success: function(){
				}
			});
		});
		$("#showWhatsAppNumber").click(function(){
			$("#showWhatsAppNumberBox").toggle();
		});
    	<?php foreach ($seller_page_info as $value) { ?>
    		<?php $dashpage = str_replace(" ","_",$value['title']); ?>
    		$('.textarea_<?php echo $dashpage; ?>').summernote({height:200});
    	<?php } ?>
		$(".textarea_new_page").summernote({height:200});
		$('#sortable').sortable({
			 update: function() {
	            var order = $(this).sortable("serialize");
        	}
    	});
    	var base64image = $('.desktop_website_logo').attr('src');
    	$(".nav a").click(function() {
	        var hash = this.getAttribute("href");
	        if (hash.substring(0, 1) === "#") {
	            hash = hash.substring(1);
	        }
	        location.hash = hash;
		});
		// on load of the page: switch to the currently selected tab
		var hash = window.location.hash;
		$('.nav a[href="' + hash + '"]').tab('show');
    });

	$(".websitepages_").click(function() {
    	var data_class = $(this).attr('data-class');
    	$.fancybox({
            'href'			: '#websitepages_'+data_class,
            'titleShow'		: false,
            'transitionIn'	: 'elastic',
            'transitionOut'	: 'elastic'
        });
	});
	$(".save_new_page").click(function(){
    	var content 	= $(".textarea_new_page").code();
		var page_title 	= $(".new_page_title").val();
		var status 		= $(".new_page_status").val();
    	$.ajax({
			url: 'index.php?route=seller/website-settings/addpages',
			type: 'POST',
			dataType: 'html',
			data: {content: content, page_title: page_title, status: status},
			success: function(){
				window.location.reload();
			}
		});
	});
	$(".save_page").click(function(){
		var data_class 	= $(this).attr('data-class');
		var data_id 	= $(this).attr('data-id');
    	var content 	= $(".textarea_" + data_class).code();
		var page_title 	= $(".page_title_" + data_class).val();
		var status 		= $(".page_status_" + data_class).val();
    	$.ajax({
			url: 'index.php?route=seller/website-settings/editpages',
			type: 'POST',
			dataType: 'html',
			data: {data_id: data_id, content: content, page_title: page_title, status: status},
			success: function(){
				window.location.reload();
			}
		});
	});

	$(".delete_page").click(function(){
		var data_id = $(this).attr('data-id');
		//alert(data_id);
		$.ajax({
			url: 'index.php?route=seller/website-settings/deletePages',
			type: 'POST',
			dataType: 'html',
			data: {data_id: data_id},
			success: function(){
				window.location.reload();
			}
		});
	});

	$(".page_theme").change(function () {
		var data_slug 	=	$(this).val();
		var path 		=	'<?php echo HTTP_SERVER; ?>catalog/view/theme/default/stylesheet/colors';
		var data 		= 	path+'/' +data_slug+'/'+data_slug+'.png';
		$(".user_logo").show();
		if(data_slug){
			$(".theme_show").html('<img src="'+data+'" alt="'+data_slug+'" title="Theme" />');
			$(".save_theme").attr('data-theme',data_slug);
		}
	});
	$(".seller_category").click(function () {
		category = [];
    	$('input[name^=\'category\']:checked').each(function (element) {
      		if (category.indexOf(this.value) < 0) category.push(this.value);
    	});
    	$.ajax({
      		url: 'index.php?route=seller/website-settings/updatesellercategory',
		    type: 'POST',
		    data: 'category=' + category.join(','),
		    dataType: 'html',
		    success: function (data) {
		    	if(data == 'success'){
					window.location.reload();
				}
      		}
   		});
	});
	$(".save_theme").click(function () {
		var config_logo = $('.config_logo').val();
		var mobile_logo = $('.mobile_logo').val();
		var config_icon = $('.config_icon').val();
		var seller_theme = $(this).attr('data-theme');
		$.ajax({
      		url: 'index.php?route=seller/website-settings/updatesellerdetails',
		    type: 'POST',
		    dataType: 'html',
		    data: {config_logo: config_logo, mobile_logo: mobile_logo, seller_theme: seller_theme, config_icon: config_icon},
		    success: function (data) {
		    	if(data == 'success'){
					window.location.reload();
				}
      		}
   		});
	});
	$(".seller_store").change(function () {
		var store_id 	=	$(this).val();
		$.ajax({
			url: 'index.php?route=seller/website-settings/change_seller_store',
			type: 'POST',
			dataType: 'html',
			data: {store_id: store_id},
			success: function () {
		    	window.location.reload();
      		}
		});
	});
	$(".save_social_profile").click(function(){
		var facebook 	= $('.facebook').val();
		var twitter 	= $('.twitter').val();
		var instagram 	= $('.instagram').val();
		var google 		= $('.google').val();
		$.ajax({
			url: 'index.php?route=seller/website-settings/updatesellerdetails',
			type: 'POST',
			dataType: 'html',
			data: {facebook: facebook, twitter: twitter, instagram: instagram, google: google},
			success: function (data) {
				if(data == 'success'){
					window.location.reload();
				}
      		}
		});
	});
	$(".others").click(function(){
		var store_type 	= $('.store_type').val();
		var config_cart_limit 	= $('.config_cart_limit').val();
		var default_store = $('.default_store').val();
		$.ajax({
			url: 'index.php?route=seller/website-settings/updatesellerdetails',
			type: 'POST',
			dataType: 'html',
			data: {store_type: store_type, config_cart_limit: config_cart_limit, default_store : default_store},
			success: function (data) {
				if(data == 'success'){
					window.location.reload();
				}
      		}
		});
	});

	$(".save_footer").click(function(){
		var payment_banner 		= $('.payment_banner').val();
		var shipping_banner 	= $('.shipping_banner').val();
		var copyright 			= $('.copyright').val();
		$.ajax({
      		url: 'index.php?route=seller/website-settings/updateSellerFooter',
		    type: 'POST',
		    dataType: 'html',
		    data: {payment_banner: payment_banner, shipping_banner: shipping_banner, copyright: copyright},
		    success: function (data) {
		    	if(data == 'success'){
					window.location.reload();
				}
      		}
   		});
	});
	// Image Manager
	$(document).delegate('a[data-toggle=\'image\']', 'click', function(e) {
		e.preventDefault();

		$('.popover').popover('hide', function() {
			$('.popover').remove();
		});

		var element = this;

		$(element).popover({
			html: true,
			placement: 'right',
			trigger: 'manual',
			content: function() {
				return '<button type="button" id="button-image" class="btn btn-primary"><i class="fa fa-pencil"></i></button>';
			}
		});

		$(element).popover('show');

		$('#button-image').on('click', function() {
			$('#modal-image').remove();

			$.ajax({
				url: 'index.php?route=common/filemanager&target=' + $(element).parent().find('input').attr('id') + '&thumb=' + $(element).attr('id'),
				dataType: 'html',
				beforeSend: function() {
					$('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
					$('#button-image').prop('disabled', true);
				},
				complete: function() {
					$('#button-image i').replaceWith('<i class="fa fa-pencil"></i>');
					$('#button-image').prop('disabled', false);
				},
				success: function(html) {
					$('body').append('<div id="modal-image" class="modal">' + html + '</div>');

					$('#modal-image').modal('show');
				}
			});

			$(element).popover('hide', function() {
				$('.popover').remove();
			});
		});

		$('#button-clear').on('click', function() {
			$(element).find('img').attr('src', $(element).find('img').attr('data-placeholder'));

			$(element).parent().find('input').attr('value', '');

			$(element).popover('hide', function() {
				$('.popover').remove();
			});
		});
	});
</script>
<?php echo $footer_seller; ?>