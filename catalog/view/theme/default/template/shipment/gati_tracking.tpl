<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<div class="container" style="margin-top: 40px;">
<section class="docket-table-sec">
        <div class="container">
            <div class="row">
                <div class="col-xs-12" style="text-align: center; padding-bottom: 20px;"> <h2><u>Gati Docket Tracking History</u></h2>   </div>

                <div class="col-xs-12">                 
                    <div class="table-responsive">
                    <table class="table docket-table ">
                        <thead>
                            <tr>
                                <th>Docket No.</th>
                                <th>Reference No.</th>
                                <th>Origin</th>
                                <th>Destination</th>
                                <th>Pickup Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="data-surface">
                                <td class="td docket-number"><?php echo $data['Docket_No']; ?></td>
                                <td class="td reference-number"><?php echo $data['Reference']; ?></td>
                                <td class="td origin"><?php echo $data['Origin']; ?></td>
                                <td class="td destination"><?php echo $data['Destination']; ?></td>
                                <td class="td pickup-date"><?php echo $data['PickUpDate']; ?></td>
                                <td class="td status"><?php echo $data['Status']; ?></td>
                            </tr> 
                            <tr class="data-deep" id="dataDeep-539650242">
                                <td colspan="7" class="td-docket-details">
                                    <div class="docket-details clearfix" id="docketDetails-539650242">
                                        <div class="status-box" style="">
                                            <div class="row" style="margin-bottom: 20px; margin-top: 20px">
                                                <div class="col-md-6">
                                                    <b>Booking Date : </b><?php echo $data['Booking_date']; ?><br><br>
                                                    <b>Assured Dly. Dt : </b><?php echo $data['Assured_Dly_Dt']; ?><br>
                                                </div>
                                                
                                                <div class="col-md-6">                                          
                                                    <b>No. of Pkgs : </b><?php echo $data['No_Of_Packages']; ?><br><br>
                                                    <b>Weight : </b><?php echo $data['Weight']; ?>  (Kgs)<br>
                                                </div>
                                            </div>

                                            <div class="row" style="margin-bottom: 20px; margin-top: 20px">
                                                <div class="col-md-6">
                                                    <b>Receiver Name : </b><?php echo $data['Receiver_s_Name']; ?><br>
                                                </div>
                                            </div>                                  

                                            <div class="status-table-container">
                                                <div class="table-responsive">
                                                <table class="table status-table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th width="25%">Date</th>
                                                            <th width="25%">Time</th>
                                                            <th width="25%">Location</th>
                                                            <th width="25%">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="dkt-table-539650242" class="rmrl-container" rmrl-elemid="3">
                                                    <?php 
                                                        $tracking_status = unserialize($data['tracking_status']); 
                                                       if(!empty($tracking_status)) {
                                                            foreach($tracking_status as $values){
                                                        ?>

                                                            <tr class="dkt-item">
                                                                <td><?php echo $values['date'];?></td>
                                                                <td><?php echo $values['time'];?></td>
                                                                <td><?php echo $values['location'];?></td>
                                                                <td><?php echo $values['status'];?> </td>
                                                            </tr> 

                                                        <?php 
                                                            } 
                                                        } 
                                                    ?>

                                                </table>
                                                </div>                                              
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>    