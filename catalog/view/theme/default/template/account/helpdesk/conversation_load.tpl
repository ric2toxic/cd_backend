
<?php if(!empty($conversation)){ ?>            
          <?php foreach($conversation as $record) {  ?>

        <div class="row">                                        
          <div class="col-sm-6"> 
              <?php
            date_default_timezone_set('Asia/Kolkata');

            $now = date('Y-m-d h:i:s'); //current time                  
        
            $created = date('Y-m-d h:i:s',strtotime($record['created_at']));
            
            $diff= date_diff(date_create($now),date_create($created));         
        
            // print_r($diff->i.'<br>'. $diff->s);               



            if($diff->d > 0){                    
            $time ='Said'.' '.$diff->d.(($diff->d > 1) ? ' days' : ' day ').' '.$diff->h.' '.(($diff->h > 1) ? 'hours' : 'hour');

             }elseif($diff->h > 0){ 
                $time ='Said'.' '.$diff->h.(($diff->h > 1) ? ' hours' : ' hour ').' '.$diff->i.' '.(($diff->i > 1) ? 'minutes' : ' minute ');            
             }elseif ($diff->i > 0) {
                $time ='Said' .' '.$diff->i.' '.(($diff->i > 1) ? 'minutes' : 'minute'). ' '.$diff->s.' '.(($diff->s > 0 ) ? 'seconds':'second');                        
             }elseif ($diff->h == 0 && $diff->i == 0 && $diff->s > 0 ) {
                $time ='Said'. ' '.$diff->s.' '.(($diff->s > 1) ? 'seconds' : 'second'); 
             }       


             ?>                                         

            <?php   
            if(!empty($record['agent_name'])){
              $user_name = $record['agent_name'];
            }else{
              $user_name = $record['customer_name'];
            }
            ?>

            <div class="helpdesk_letter">
                  <?php  
                  $capital = ucwords($user_name);
                  $first_letter = substr($capital, 0,1);
                  $letter = $first_letter; 
                  echo $letter;
                  ?>
            </div>    
                <b>&nbsp;<?php echo $user_name; ?></b>
                <?php 
                if(!empty($time)){
                  echo ', '.$time.' ago'; 
                }
                ?>

          </div> <!-- col-sm-6 closing -->
              

             <div class="clearfix"></div>  

          <div class="helpdesk_reply">                                        
              <p><?php print_r(nl2br($record['body'])); ?> </p>

              <?php if(!empty($record['attachment'])){

                foreach($record['attachment'] as $attachment_data){

               ?>  
              
                <div class="helpdesk_attachment">  
                  <div class="helpdesk_attachment_type">
                    <i class="fa fa-file-o" style="font-size:36px;"></i>

                      <?php $ext = pathinfo($attachment_data['attachment_name'], PATHINFO_EXTENSION);    
                        if(strlen($ext) <= 3){  ?>
                      <span class="helpdesk_file_type">                                      
                       <?php echo $ext;  ?>                                                                          
                     </span>
                    <?php } ?>
                    </div> 
                    <div class="helpdesk_attach_content">
                      <div> 
                      
                        <a href="<?php print_r($attachment_data['path']); ?>" class="filename" download=""><?php echo $attachment_data['attachment_name'] ?>  </a>
                    </div>
                   <div>(<?php $size = $attachment_data['size'];
                          $kb = $size/1024;echo round($kb,2);?> KB)
                   </div>
                  </div>
                </div>                              
               <?php } }?>
          </div><!-- helpdesk_reply closing  -->
      </div>       

<?php }?>                            
<?php }?>        