<div class="duplicate_customer_search_modal_table" style="height: 500px; overflow-y: scroll;">
<table style="width: 100%;">
	<thead>
		<th style="width: 20px;">
		 	
		</th>
		<th>
		 Name
		</th> 
		<th>
		 Email
		 <br>
		 Phone	
		</th>		
		<th>
		 City
		 <br>
		 Customer ID	
		</th>
		<th>
		 Date Added	
		 <br>
		  Master Customer
		</th>
	</thead>
	<tbody>
	<?php 
	if (!empty($customers) && count($customers)>0) {
		foreach ($customers as $key => $value) {
			?>
			<tr>
				<td align="center" style="width: 20px;"><input type="radio" class="master_customer_id change_customer_edit_order" name="master_customer_id" value="<?php echo $value['customer_id'];?>"></td>
				<td><?php echo $value['name'];?></td>
				<td><b><?php echo $value['email'];?></b><br><span style="color: red;"><?php echo $value['telephone'];?></span></td>
				<td>
				<?php 
				echo $value['city'];
				if ($value['postcode']!='') {
					echo ' ('.$value['postcode'].')';
				}
				?> <br><b><?php echo $value['customer_id'];?></b></td>
				<td><?php echo date('dS M, Y',strtotime($value['date_added']));?><br><b><?php
				 
				 if ($value['is_master_id'] && empty($change_customer_flag)) {
				 	echo '<button type="button" style="margin-left: 10px;" class="btn btn-success btn-xs">Master</button>';
				 }
				 ?></b></td>
			</tr>
			<?php 
			if(!empty($change_customer_flag)) {
				?>
				<tr class="customer_edit_<?php echo  $value['customer_id']; ?> hidden change_customer_for_edit">
					<td colspan="5">
						<span>
							<form action="<?php echo $save; ?>" onsubmit="return handleData(this)" method="post">
							What do want to edit? <br />
								<div class="row">
								    <div class="col-sm-4">
								        <div class="">
								            <label class="control-label" for="input-customer_id">Customer Id</label>
								            <input type="checkbox" name="customer_id" value="1">
								        </div>
								        <div class="">
								            <label class="control-label" for="input-firstname">Firstname</label>
								            <input type="checkbox" name="firstname" value="1">
								        </div>
								        <div class="">
								            <label class="control-label" for="input-lastname">Lastname</label>
								            <input type="checkbox" name="lastname" value="1">
								        </div>
								    </div>
								    <div class="col-sm-4">
								        <div class="">
								            <label class="control-label" for="input-telephone">Telephone</label>
								            <input type="checkbox" name="telephone" value="1">
								        </div>
								        <div class="">
								            <label class="control-label" for="input-email">Email</label>
								            <input type="checkbox" name="email" value="1">
								        </div>
								        <div class="">
								            <textarea name="comment" required="required" placeholder="Comment*" class="form-control"></textarea>
								        </div>
								    </div>
								    <div class="col-sm-4">
								        <div class="">
								        	
								        	<input type="hidden"  value="<?php echo $value['customer_id']; ?>" name="customer_id_for_edit" />
								        	<input type="hidden"  value="<?php echo $order_id; ?>" name="order_id" />
								        	<input type="hidden"  value="<?php echo $suborder_id; ?>" name="suborder_id" />
								            <input type="hidden"  value="1" name="is_customer_edit" />
								            <input type="submit" class="btn btn-success" value="Save" name="submit" />
								        </div>
								    </div>
								</div>
							</form>
						</span>
					</td>
				</tr>
				<?php
			}
		}
	}
	?>	
	</tbody>
</table>
</div>