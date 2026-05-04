
  <?php foreach($credit_partners as $partner){ 
  ?>
  <div class="col-md-12" style="align:center">
      <button data-id="<?php echo $partner['url']; ?>" class="btn btn-success btn-large share_link" style="font-size: 16px;font-weight:800"><?php echo $partner['name'] ?></i></button>
  </div>
<?php } ?>
