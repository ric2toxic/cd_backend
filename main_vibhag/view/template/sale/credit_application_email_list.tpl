<?php if($customer_email_data) { ?>

  <div class="col-md-12 table-wrapper-scroll-y" style="align:center">
      <table class="table table-bordered table-striped">
      	<thead>
      		<tr>
      			<td>Subject</td>
      			<td>Email Body</td>
      			<td>Attachmemt</td>
      			<td>Date</td>
      		</tr>
      	</thead>
      	<tbody>
      		<?php  
	foreach($customer_email_data as $data) { 

  ?>
  			<tr>
      			<td><?php echo $data['subject'];?></td>
      			<td>
              <?php if(!empty($data['email_body'])){?>
                <a href="<?php echo STATIC_CONTENT_URL_SSL . $data['email_body']; ?>" target="_blank">Email Body</a>
              <?php }else{ echo '&nbsp';}?>


            </td>
      			<td>
              <?php if(!empty($data['attachmemt'])){?>
                <a href="<?php echo STATIC_CONTENT_URL_SSL . $data['attachmemt']; ?>" target="_blank">Attachmemt</a>
              <?php }else{ echo '&nbsp';}?>
            </td>
      			<td><?php echo date( "d/m/Y",strtotime($data['date'])); ?></td>
      		</tr>
      			<?php } ?>

      	</tbody>
      </table>
  </div>
<?php } ?>