<label ><?php echo $text_courier_advisory; ?></label>
  <div class="portion_courier_advisory">
    <button type="button" class="close_button"><i class="fa fa-close"></i></button>
    <?php echo $courier_advisory_table;?>
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
