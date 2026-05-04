<?php
	$arr_internship_roles = array('Sales', 'Marketing', 'Operation', 'IT', 'HR', 'Other');
	$arr_profile_desired = array('Sales(Retailer Onboarding)', 'Operations', 'Business Development(Seller Sign Up)', 'HR');

?>
<?php echo $header; ?>
<div class="container">
  <div class="row"><?php echo $column_left; ?>
      <div id="content" class="campushunt card_box"><?php echo $content_top; ?>
	  <div class="heading_title">

       <h1>Campus Hunt Register</h1>
		</div>
	    <div class="campustalenthunt">	
			<form runat="server" method="post" action="">
				<div class="cth_field1">
					<div class="error">

					</div>
					
					<div class="row">
						<div class="col-sm-6 col-x6-12">
							<div class="cth_name_field1">
								<h3>Member 1</h3>
								<div class="form-group required">
									<input type="text" value="<?php echo $name[0];?>" class="form-control form_name" placeholder="Name" name="name[0]">
									<?php if ($error_name_0) { ?>
									<div class="text-danger"><?php echo $error_name_0; ?></div>
									<?php } ?>
								</div>
								<div class="form-group required">
								<input type="email" value="<?php echo $email[0];?>" class="form-control form_name" placeholder="Email ID" name="email[0]">
									<?php if ($error_email_0) { ?>
									<div class="text-danger"><?php echo $error_email_0; ?></div>
									<?php } ?>
								</div>
								<div class="form-group required">
									<input type="tel" value="<?php echo $contact[0];?>" class=" form-control form_name" placeholder="Mobile Number" name="contact[0]">
									<?php if ($error_contact_0) { ?>
									<div class="text-danger"><?php echo $error_contact_0; ?></div>
									<?php } ?>
								</div>
								<div class="form-group">
									<input type="text" value="<?php echo $grade_point[0];?>" class=" form-control form_name" placeholder="Grade Point (Please mention with base 4.3/5 or 88% or 8.8/10)" name="grade_point[0]">

								</div>
								<div class="form-group">
									<input type="text" value="<?php echo $internship_company[0];?>" class=" form-control form_name" placeholder="Internship Company" name="internship_company[0]">

								</div>

								<div class="form-group">


									<select name="internship_role[0]" class="form-control select">
										<option value="">Internship Area of Work</option>
										<?php

											foreach($arr_internship_roles as $internship_role_val){
											   $selected  = '';

											   if(strtolower(trim($internship_role_val)) == strtolower(trim($internship_role[0])) ){
											       $selected = 'selected = "selected"';
											   }
										?>
												<option value="<?php echo $internship_role_val; ?>" <?php echo $selected; ?> ><?php echo $internship_role_val;?></option>
										<?php
											}
										?>
									</select>

								</div>

								<div class="form-group">


									<select name="desired_profile[0]" class="form-control select">
										<option value="">Preferred Profile for WholesaleBox</option>
										<?php
											foreach($arr_profile_desired as $desired_role){
											   $selected  = '';
											   if($desired_role == $desired_profile[0] ){
											       $selected = 'selected = "selected"';
											   }
										?>
										<option value="<?php echo $desired_role; ?>" <?php echo $selected; ?> ><?php echo $desired_role;?></option>
										<?php
											}
										?>
									</select>

								</div>

							</div>
						</div>
						<div class="col-sm-6 col-x6-12">
						
							<div class="cth_name_field1">
								<h3>Member 2</h3>
								<div class="form-group required">
									<input type="text" value="<?php echo $name[1];?>" class="form-control form_name" placeholder="Name" name="name[1]">
									<?php if ($error_name_1) { ?>
									<div class="text-danger"><?php echo $error_name_1; ?></div>
									<?php } ?>
								</div>
								<div class="form-group required">
									<input type="email" value="<?php echo $email[1];?>" class="form-control form_name" placeholder="Email ID" name="email[1]">
									<?php if ($error_email_1) { ?>
									<div class="text-danger"><?php echo $error_email_1; ?></div>
									<?php } ?>
								</div>
								<div class="form-group required">
									<input type="tel" value="<?php echo $contact[1];?>" class="form-control form_name" placeholder="Mobile Number" name="contact[1]">
									<?php if ($error_contact_1) { ?>
									<div class="text-danger"><?php echo $error_contact_1; ?></div>
									<?php } ?>
								</div>
								<div class="form-group">
									<input type="text" value="<?php echo $grade_point[1];?>" class=" form-control form_name" placeholder="Grade Point (Please mention with base 4.3/5 or 88% or 8.8/10)" name="grade_point[1]">

								</div>
								<div class="form-group">
									<input type="text" value="<?php echo $internship_company[1];?>" class=" form-control form_name" placeholder="Internship Company" name="internship_company[1]">

								</div>

								<div class="form-group">


									<select name="internship_role[1]" class="form-control select">
										<option value="">Internship Area of Work</option>
										<?php
											foreach($arr_internship_roles as $internship_role){
											   $selected  = '';
											   if($internship_role == $internship_role[1] ){
											       $selected = 'selected = "selected"';
											   }
										?>
										<option value="<?php echo $internship_role; ?>" <?php echo $selected; ?> ><?php echo $internship_role;?></option>
										<?php
											}
										?>
									</select>

								</div>

								<div class="form-group">


									<select name="desired_profile[1]" class="form-control select">
										<option value="">Preferred Profile for WholesaleBox</option>
										<?php
											foreach($arr_profile_desired as $desired_role){
											   $selected  = '';
											   if($desired_role == $desired_profile[1] ){
											       $selected = 'selected = "selected"';
											   }
										?>
										<option value="<?php echo $desired_role; ?>" <?php echo $selected; ?> ><?php echo $desired_role;?></option>
										<?php
											}
										?>
									</select>

								</div>
							</div>
						</div>

					</div>
				
				
					<div class="row">
						<div class="col-sm-6 col-x6-12">
							<div class="cth_name_field1">
								<h3>Member 3</h3>
								<div>
									<input type="text" value="<?php echo $name[2];?>" class="form-control form_name" placeholder="Name" name="name[2]">
									<?php if ($error_name_2) { ?>
									<div class="text-danger"><?php echo $error_name_2; ?></div>
									<?php } ?>
								</div>
								<div>
									<input type="email" value="<?php echo $email[2];?>" class="form-control form_name" placeholder="Email ID" name="email[2]">
									<?php if ($error_email_2) { ?>
									<div class="text-danger"><?php echo $error_email_2; ?></div>
									<?php } ?>
								</div>
								<div>
									<input type="tel" value="<?php echo $contact[2];?>" class="form-control form_name" placeholder="Mobile Number" name="contact[2]">
									<?php if ($error_contact_2) { ?>
									<div class="text-danger"><?php echo $error_contact_2; ?></div>
									<?php } ?>
								</div>
								<div class="form-group">
									<input type="text" value="<?php echo $grade_point[2];?>" class=" form-control form_name" placeholder="Grade Point (Please mention with base 4.3/5 or 88% or 8.8/10)" name="grade_point[2]">

								</div>
								<div class="form-group">
									<input type="text" value="<?php echo $internship_company[2];?>" class=" form-control form_name" placeholder="Internship Company" name="internship_company[2]">

								</div>

								<div class="form-group">


									<select name="internship_role[2]" class="form-control select">
										<option value="">Internship Area of Work</option>
										<?php
											foreach($arr_internship_roles as $internship_role){
											   $selected  = '';
											   if($internship_role == $internship_role[2] ){
											       $selected = 'selected = "selected"';
											   }
										?>
										<option value="<?php echo $internship_role; ?>" <?php echo $selected; ?> ><?php echo $internship_role;?></option>
										<?php
											}
										?>
									</select>

								</div>

								<div class="form-group">


									<select name="desired_profile[2]" class="form-control select">
										<option value="">Preferred Profile for WholesaleBox</option>
										<?php
											foreach($arr_profile_desired as $desired_role){
											   $selected  = '';
											   if($desired_role == $desired_profile[2] ){
											       $selected = 'selected = "selected"';
											   }
										?>
										<option value="<?php echo $desired_role; ?>" <?php echo $selected; ?> ><?php echo $desired_role;?></option>
										<?php
											}
										?>
									</select>

								</div>
							</div>
							</div>
						<div class="col-sm-6 col-x6-12">
							<div class="cth_name_field1">
								<h3>Member 4</h3>
								<div>
									<input type="text" value="<?php echo $name[3];?>" class="form-control form_name" placeholder="Name" name="name[3]">
									<?php if ($error_name_3) { ?>
									<div class="text-danger"><?php echo $error_name_3; ?></div>
									<?php } ?>
								</div>
								<div>
									<input type="email" value="<?php echo $email[3];?>" class="form-control form_name" placeholder="Email ID" name="email[3]">
									<?php if ($error_email_3) { ?>
									<div class="text-danger"><?php echo $error_email_3; ?></div>
									<?php } ?>
								</div>
								<div>
									<input type="tel" value="<?php echo $contact[3];?>" class="form-control form_name" placeholder="Mobile Number" name="contact[3]">
									<?php if ($error_contact_3) { ?>
									<div class="text-danger"><?php echo $error_contact_3; ?></div>
									<?php } ?>
								</div>
								<div class="form-group">
									<input type="text" value="<?php echo $grade_point[3];?>" class=" form-control form_name" placeholder="Grade Point (Please mention with base 4.3/5 or 88% or 8.8/10)" name="grade_point[3]">

								</div>
								<div class="form-group">
									<input type="text" value="<?php echo $internship_company[3];?>" class=" form-control form_name" placeholder="Internship Company" name="internship_company[3]">

								</div>

								<div class="form-group">


									<select name="internship_role[3]" class="form-control select">
										<option value="">Internship Area of Work</option>
										<?php
											foreach($arr_internship_roles as $internship_role){
											   $selected  = '';
											   if($internship_role == $internship_role[3] ){
											       $selected = 'selected = "selected"';
											   }
										?>
										<option value="<?php echo $internship_role; ?>" <?php echo $selected; ?> ><?php echo $internship_role;?></option>
										<?php
											}
										?>
									</select>

								</div>

								<div class="form-group">


									<select name="desired_profile[3]" class="form-control select">
										<option value="">Preferred Profile for WholesaleBox</option>
										<?php
											foreach($arr_profile_desired as $desired_role){
											   $selected  = '';
											   if($desired_role == $desired_profile[3] ){
											       $selected = 'selected = "selected"';
											   }
										?>
										<option value="<?php echo $desired_role; ?>" <?php echo $selected; ?> ><?php echo $desired_role;?></option>
										<?php
											}
										?>
									</select>

								</div>


							</div>
						</div>
					</div>	
				</div>
				<br /><br />
				<div class="row" >

						<div class="col-sm-4 col-x6-12 teamform" >
							<div class="cth_field2">
								<input type="text" value="<?php echo $team_name; ?>" class="form-control form_name" placeholder="Enter Your Team Name" name="team_name">
								<?php if ($error_team_name) { ?>
								<div class="text-danger"><?php echo $error_team_name; ?></div>
								<?php } ?>
								<input type="city" value="<?php echo $city; ?>" class="form-control form_name" placeholder="City Name " name="city">
								<?php if ($error_city) { ?>
								<div class="text-danger"><?php echo $error_city; ?></div>
								<?php } /* ?>
								<select class="form-control select" name="campus">
									<option value="0">Select your campus</option>
									<?php
										foreach($campuses_list as $campus_db){
									?>
											<option value="<?php echo $campus_db['campus_id']; ?>"><?php echo $campus_db['campus_name']; ?></option>
									<?php
										}
									?>

								</select>
								<?php */ ?>
								<input type="text" value="<?php echo $campus; ?>" class="form-control form_name" placeholder="Enter Your College Name" name="campus">
								<?php if ($error_campus) { ?>
								<div class="text-danger"><?php echo $error_campus; ?></div>
								<?php } ?>
								<br/>
							<button class="btn btn-primary" name="submit">Submit</button>
								<br /><br />
							</div>
						</div>

				</div>
				
			</form>
			
	    </div>
       <?php echo $content_bottom; ?>
	  </div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>