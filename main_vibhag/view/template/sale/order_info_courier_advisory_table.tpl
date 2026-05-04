
<table class="table table-bordered table-hover" border="1">
  <thead>
    <tr>
      <td class="text-center">Courier</td>
      <td class="text-center">Serviceability</td>
      <td class="text-center">COD</td>
      <td class="text-center">Location</td>
    </tr>
  </thead>
  <tbody>
    <?php foreach( $logistic_data as $values ) { 
      $is_bluedart_apex = 0;
      if(strtolower($values['courier_logistic']) == 'bluedart apex') {
        $is_bluedart_apex = 1;
      }
    ?>
    <tr <?php if(!$values['is_serviceable']) { ?> style="background-color: #f5050552;" <?php } ?> >
        <td class="text-center"><b><?php echo $values['courier_logistic'];?></b></td>
        <td class="text-center"><?php echo $values['serviceability'];?></td>
        <?php if($is_bluedart_apex) { ?>
          <td class="text-center" style="font-weight: bold; color: red"><?php echo ($values['cod']) ? 'Available' : 'Not available';?></td>
        <?php } else { ?>
          <td class="text-center"><?php echo ($values['cod']) ? 'Available' : 'Not available';?></td>
        <?php } ?>
        
        <td class="text-center">
        <?php 
            echo ucfirst(strtolower($values['location']));
            if(strtolower($values['courier_logistic'])=='gati' && $values['distance'] > 0) {
                echo '<br>['.$values['distance'].' Km] ';
            }
        ?>
        </td>
      </tr>
  <?php } ?>

  </tbody>
</table>