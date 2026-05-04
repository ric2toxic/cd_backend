<label ><?php echo $text_courier_advisory; ?></label>
  <div class="portion_courier_advisory">
    <button type="button" class="close_button"><i class="fa fa-close"></i></button>
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <td class="text-center">Courier</td>
          <td class="text-center">Serviceability</td>
          <td class="text-center">COD</td>
          <td class="text-center">Location</td>
        </tr>
      </thead>
      <tbody>
        <?php foreach( $logistic_data as $values ) { ?>
        <tr <?php if(!$values['is_serviceable']) { ?> style="background-color: #f5050552;" <?php } ?> >
            <td class="text-center"><b><?php echo $values['courier_logistic'];?></b></td>
            <td class="text-center"><?php echo $values['serviceability'];?></td>
            <td class="text-center"><?php echo ($values['cod']) ? 'Available' : 'Not available';?></td>
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

    <table class="table table-bordered table-hover">
      <h3>B2C Requirement </h3>
        <tr>
          <td><b><?php echo $text_b2c_stat_levy_type; ?></b></td>
          <td><?php echo $paperwork['b2c_stat_levy_type']; ?></td>
          <td><b><?php echo $text_b2c_stat_levy_liable; ?></b></td>
          <td><?php echo $paperwork['b2c_stat_levy_liable']; ?></td>
        </tr>
        <tr>
          <td><b><?php echo $text_b2c_paperwork_req; ?></b></td>
          <td><?php echo $paperwork['b2c_paperwork_req']; ?></td>
          <td><b><?php echo $text_b2c_paperwork_exem_lim; ?></b></td>
          <td><?php echo $paperwork['b2c_paperwork_exem_lim']; ?></td>
        </tr>

    </table>

    <table class="table table-bordered table-hover">
      <h3>B2B Requirement </h3>
      <tr>
        <td colspan="2"><b><?php echo $text_b2b_paperwork_inb_req; ?></b></td>
        <td  colspan="2"><?php echo $paperwork['b2b_paperwork_inb_req']; ?></td>
      </tr>
      <tr>
        <td  colspan="2"><b><?php echo $text_b2b_paperwork_outb_req; ?></b></td>
        <td  colspan="2"><?php echo $paperwork['b2b_paperwork_outb_req']; ?></td>
      </tr>
    </table>
    <?php if(!$data_ajax){ ?>
    <button class="btn btn-info btn-xs courier_add_notes"><?php echo $text_paperwork_additional_notes.' >>> ';  ?></button>
    <?php } ?>
  </div>

<script type="text/javascript">
  $('.courier_advisory').click(function(){
    $('.portion_courier_advisory').show();
    $('.portion_important_info').hide();
  });
   $('.close_button').click(function(e){
    e.stopPropagation();
    $(this).parent().hide();
    $('.courier_advisery_popup').addClass('hidden');
  });

  $('.courier_add_notes').click(function(){
    $('#tab_additnl_info').click();
    $(window).scrollTop($('#courier_additional_remarks').offset().top-20);
  })

</script>
