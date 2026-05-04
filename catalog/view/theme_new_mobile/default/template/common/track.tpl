<?php echo $header; ?>
   <div class="container">
        <div id="content" class="track_page">
            <div class="row">
                <h3><?php echo $text_redirect_msg ; ?></h3>
                <div class="global-ajax-loader loading"></div>
            </div>
        </div>
   </div>
<?php echo $footer; ?>
<script>
    $(document).ready(function(){
        window.location.href = "<?php echo $app_download_link; ?>";
    });
    <?php //echo app_download_link; ?>
</script>
