<?php if(!empty($colors)){ ?>
<div class="col-sm-10 remove_div">
    <select name="config_colors" id="input-colors" html-token="<?php echo $token; ?>" class="form-control">
        <?php foreach ($colors as $color) { ?>
        <option value="<?php echo $color; ?>"><?php echo str_replace("_"," ",$color); ?></option>
        <?php } ?>
    </select>
    </div>
<?php }else{ ?>
<div class="col-sm-10 remove_div">
    <select name="config_colors" id="input-colors" html-token="<?php echo $token; ?>" class="form-control">
        <option value="<?php echo $color; ?>"><?php echo str_replace("_"," ",$default_style); ?></option>
    </select>
</div>

<?php } ?>
