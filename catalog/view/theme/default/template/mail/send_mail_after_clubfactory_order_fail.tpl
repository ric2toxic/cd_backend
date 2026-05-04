<div>
    <p>Greetings Sir/Ma'am,</p><br />
    <p>Club factory unshiped fail order list</b>.</p><br>

    <p>
    	<b>ORDER LIST</b>:<br /><br />
		<?php 
			$result = '<div class="document_status_data"> <table id="notesTable" style="width:100%">
			                <tbody><tr>
			                    <th>Club Factory Order id</th>
			                    <th>Club Factory Order number</th>
			                    <th>Region</th>
			                    </tr> ';	
					foreach($order_details as $values){
					 	   $result .= '<tr>';
			    		   $result .= '<td>'. $values['club_factory_order_id'].'</td>';
			    		   $result .= '<td>'. $values['club_factory_order_number'].'</td>';
			    		   $result .= '<td>'. $values['message'].'</td>';
			               $result .= '</tr>';
					}
			$result .= '</tbody></table></div>';

			echo $result;
		?>
		</p>

		<br />

    <b>WHOLESALEBOX INTERNET PVT LTD,</b> <br/>
	<b>B-22, Crystal Mall, Banipark,</b><br />
	<b>Jaipur-302016, Rajasthan, India</b><br />
	<b>+91 8239778680</b><br /><br />
</div>