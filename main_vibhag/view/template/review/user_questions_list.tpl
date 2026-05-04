<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>

  <div class="container-fluid" id="user_questions">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i><?php echo $heading_title; ?></h3>
      </div>
      <div class="panel-body">
      <div class="well">
        <div class="row">

          <div class="col-sm-4">
            <div class="form-group">
              <label class="control-label" for="input-seller-code"><?php echo $entry_seller; ?></label>
              <input type="text" name="filter_seller_code" value="<?php echo ($filter_seller_code ?? ''); ?>" placeholder="<?php echo $entry_seller; ?>" id="input-seller-code" class="form-control" />
            </div>
          </div>

          <div class="col-sm-4">
            <div class="form-group">
              <label class="control-label" for="input-answered"><?php echo $entry_answer; ?></label>
              <select name="filter_answered" id="input-answered" class="form-control">
                <option value="*">-----Select------</option>
                <?php if (($filter_answered ?? '') === '1') { ?>
                <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_yes; ?></option>
                <?php } ?>
                <?php if (($filter_answered ?? '') === '0') { ?>
                <option value="0" selected="selected"><?php echo $text_no; ?></option>
                <?php } else { ?>
                <option value="0"><?php echo $text_no; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="col-sm-4">
            <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
          </div>

        </div>
      </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-questions">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
                <tr>
                  <td rowspan="2"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td rowspan="2"><?php echo $column_image; ?></td>
                  <td rowspan="2"><?php echo $column_seller_company; ?></td>
                  <td rowspan="2"><?php echo $column_question; ?></td>
                  <td rowspan="2"><?php echo $column_customer_detail; ?></td>
                  <td rowspan="2"><?php echo $column_date_added; ?></td>
                  <td colspan="4" class="text-center"><?php echo $column_action; ?></td>
                </tr>
                <tr>
                  <td><?php echo $column_is_answer; ?></td>
                </tr>
              <tbody>
              <?php if(isset($questions)){ foreach ($questions as $question) { ?>
                <tr>
                  <td><input type="checkbox" name="selected[]" value="<?php echo $question['question_id']; ?>" /></td>
                  <td class="order_list_comment">
                    <a href="<?php echo $question['product_link']; ?>" target="_blank">
                      <img src="<?php echo $question['product_image']; ?>" /> <br/>
                      <span>
                        <?php echo $question['product_model']; ?>
                        <span><i class="fa fa-pencil"></i> </span>
                      </span>
                    </a>
                  </td>
                  <td class="seller_details">
                    <?php echo $question['seller_company']; ?><br/>
                    <span><?php echo $question['seller_mobile_no']; ?></span><br/>
                    <span><?php echo $question['seller_email']; ?></span>
                  </td>
                  <td>
                    <span id= "question_text_<?php echo $question['question_id']; ?>" >
                      <?php echo $question['question']; ?>
                    </span>
                    <span class="question" data-qid="<?php echo $question['question_id']; ?>">
                      <span class="question_edit_input" id="question_edit_<?php echo $question['question_id']; ?>" data-qid="<?php echo $question['question_id']; ?>"><i class="fa fa-pencil"></i> </span>
                    </span>
                    <span id="quest_input_<?php echo $question['question_id'];?>" style="display: none;">
                      <textarea id="input_question_text_<?php echo $question['question_id'];?>" data-qid = "<?php echo $question['question_id'];?>" name="queston_text_update" cols="13" rows="5"><?php echo $question['question']; ?></textarea>
                      <span class="question_close_input" data-qid="<?php echo $question['question_id']; ?>"><i class="fa fa-ban"></i> </span>
                    </span>
                  </td>
                  <td class="customer_details">
                    <?php if($question['customer_id'] > 0){ ?>
                    <a href="<?php echo $question['customer_link'];?>" target="_blank"><?php echo $question['customer_name'];?></a> <br />
                    <?php }else{ ?>
                      <?php echo $question['customer_name'];?> <br />
                    <?php } ?>
                    <span><?php echo $question['customer_telephone']; ?></span> <br/>
                    <span><?php echo $question['customer_email']; ?></span>
                  </td>
                  <td><?php echo $question['date_added']; ?></td>

                  <td class="text-center"><input type="checkbox" name="is_answered" class="question_answered" data-qid = "<?php echo $question['question_id']; ?>" <?php if($question['is_answered']){ echo "checked = checked";  }?> /> </td>
                </tr>
              <?php } } ?>

              </tbody>
            </thead>
          </table>
        </div>
        </form>
          <div class="row"><?php echo $pagination; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $('.question_answered').click(function() {
    qid = $(this).attr('data-qid');
    var answered = 0;
    if (this.checked) {
      answered = 1;
    }
    $.ajax({
      url:'index.php?route=review/user_questions/mark_as_answered&token=<?php echo $token; ?>&is_answered=' + encodeURIComponent(answered)+'&qid='+qid,
    });
  });
</script>
<script>
  $('#button-filter').on('click', function() {
    url = 'index.php?route=review/user_questions&token=<?php echo $token; ?>';

    var filter_seller_code = $('input[name=\'filter_seller_code\']').val();

    if (filter_seller_code) {
      url += '&filter_seller_code=' + encodeURIComponent(filter_seller_code);
    }

    var filter_answered = $('select[name=\'filter_answered\']').val();

    if (filter_answered !== '*') {
      url += '&filter_answered=' + encodeURIComponent(filter_answered);
    }
console.log(url);
    location = url;
  });
</script>

<script type="text/javascript">
  // update for question
  $('.question').click(function(){
    var question_id = $(this).attr('data-qid');
    $('#question_text_'+ question_id).hide();
    $('#quest_input_'+ question_id).show();
    $('input#input_question_text_'+question_id).focus();
    $('#question_edit_'+question_id).hide();
  });

  $('.question_close_input').click(function(){
    var question_id = $(this).attr('data-qid');
    $('#question_text_'+ question_id).show();
    $('#quest_input_'+ question_id).hide();
    $('#question_edit_'+question_id).show();
  });

  // Update weight
  $('textarea[name=\'queston_text_update\']').on("keypress", null, function(e) {
    if (e.keyCode == 13) {

      var question_text = $(this).val();
      var qid = $(this).attr('data-qid');

      $.ajax({
        url: 'index.php?route=review/user_questions/update_question&token=<?php echo $token; ?>&qid='+qid+'&question_text='+question_text,
        success: function(json) {
          //window.location.reload();
          $('span#question_text_'+ qid).text(json['qt']);
          $('#question_text_'+ qid).show();
          $('#quest_input_'+ qid).hide();
          $('#question_edit_'+qid).show();
        },
      });
    }
  });

  $('input').on('keypress',function(e){
    if (e.keyCode == 13) {
      $('#button-filter').trigger('click');
    }
  });
</script>
