<?php if($application_log) { ?>

  <div class="col-md-12 table-wrapper-scroll-y" style="align:center">
      <table class="table table-bordered table-striped">
      	<thead>
      		<tr>
      			<td>Name</td>
      			<td>Type</td>
      			<td>Action</td>
      			<td>Step</td>
      			<td>Date</td>
      		</tr>
      	</thead>
      	<tbody>
      		<?php  
	foreach($application_log as $log) { 

  ?>
  			<tr>
  				<td><?php if($log['type'] == 'CRM_USER') {
  					echo isset($crm_users[$log['user_id']]) ? $crm_users[$log['user_id']] : '';
  				} else if($log['type'] == 'KHUFIYA_USER') {
  					echo isset($khufiya_users[$log['user_id']]) ? $khufiya_users[$log['user_id']] : '';
  				} else {
  					echo $customer_name;
  				}?></td>
      			<td><?php echo $log['type']; ?></td>
      			<td><?php echo $log['action']; ?></td>
      			<td><?php echo $log['step']; ?></td>
      			<td><?php echo date( "d/m/Y",strtotime($log['date'])); ?></td>
      		</tr>
      			<?php } ?>

      	</tbody>
      </table>
  </div>
<?php } ?>