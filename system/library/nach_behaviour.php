<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

/*
 * @info NachBehaviour class to handle all behaviour type functionality related to NACH schedules
 *         This behaviour classes using multiple action(Model) classes Like: Nach, BankHoliday
 *            to access base level function into DB and Data 
 *        Base uses add/edit/redistribute/shift/change_status etc. for  NACH schedule(s)
 * @author Nishu, May 2019
 */

class NachBehaviour {

    ////////////////////////// PUBLIC /////////////////////////////////

    /**
     * Constructor to create object of this class and to use functionality of NACH behaviour class
     * @param $registry - Registry object
     *
     * @warning Do not pass Controller object for $registry param. If you are in the context of a
     *          controller, you can generally get the Registry object as $this->registry
     * @throws Exception
     * @author Nishu, May 2019
     */
    public function __construct(Registry $registry) {
        $this->registry = $registry;
        
        // Throw Exception if Database\DB instance is not available
        if ( empty($registry->get('db')) || !($registry->get('db') instanceof Database\DB) ) {
            throw new Exception('Valid Database\DB instance does not exist in the $registry during __construct of NachBehaviour object!');
        }
        $this->db      = $registry->get('db');
        
        //Create object of NACH main action class
        $this->nach = NachAction::getInstance($registry);
    }

    /**
     * @info Public method to get next coming working date(s) for customer specific crontab string,
     *         which is helpful to generate/regenerate NACH Schedules,
     *
     * @param array $data -- Keys in the $data array are defined as below:
     *   'nach_schedule_crontab'  -> Crontab string to generate next working date. Default = self::DEFAULT_NACH_SCHEDULE_CRONTAB
     *
     *   'delivered_date'         -> Delivered date of suborder. Default = Current date
     *
     *   'days_before_nach_start' -> Number of days after 'delivered_date' for schedule to begin. Default= self::DEFAULT_DAYS_BEFORE_NACH_START
     *
     *   'schedule_days_for_nach' -> Number of days, to determine how many working dates to be calculated. Default= self::DEFAULT_SCHEDULE_DAYS_FOR_NACH
     *
     *   'suborder_id' -> If provided, it ignores those dates for which the schedule already exists for that suborder_id
     *
     * @return array of Dates
     * @throws Exception
     * @author Nishu, May 2019
     */
    public function getNextComingCronDates(array $data) : array {
        
        $date_arr = array();
        
        // Input sanitization
        $nach_schedule_crontab = trim($data['nach_schedule_crontab'] ?? self::DEFAULT_NACH_SCHEDULE_CRONTAB);
        $delivered_date = trim($data['delivered_date'] ?? date('Y-m-d'));
        $days_before_nach_start = (int)($data['days_before_nach_start'] ?? self::DEFAULT_DAYS_BEFORE_NACH_START);
        $schedule_days_for_nach = (int)($data['schedule_days_for_nach'] ?? self::DEFAULT_SCHEDULE_DAYS_FOR_NACH);
        $suborder_id = trim($data['suborder_id'] ?? '');
        
        // Schedule start date is determined by three conditions:
        // 1. If schedule generation is done < self::CURRENT_DATE_NACH_SCHEDULE_GENERATION_HOUR_LIMIT AM (system time), 
        //    then it has to be >= Current date
        // 2. If schedule generation is done >= self::CURRENT_DATE_NACH_SCHEDULE_GENERATION_HOUR_LIMIT AM (system time), 
        //    then it has to be > Current date
        // 3. It has to be >= delivered_date + days_before_nach_start
        $schedule_start_date = max( ((int)date('H') < self::CURRENT_DATE_NACH_SCHEDULE_GENERATION_HOUR_LIMIT ? 
                                     strtotime('today') : 
                                     strtotime('tomorrow')), 
                                   strtotime($delivered_date . '+' . $days_before_nach_start . ' days'));
        $schedule_start_date = date('Y-m-d', $schedule_start_date);
        
        // Loop until we get $schedule_days_for_nach count for dates
        while ( count($date_arr) < $schedule_days_for_nach ) {
            
            // Get atleast 3 times the number of days to generate schedule for to cover for bank holidays
            // Get from CronExpression library. Include the $schedule_start_date as well
            $cron_obj = Cron\CronExpression::factory($nach_schedule_crontab);
            $cron_dates = $cron_obj->getMultipleRunDates(3*$schedule_days_for_nach, $schedule_start_date, false, true, null);

            // Get bank holiday dates from the $schedule_start_date to last date received in the $cron_dates
            $select_data = array('holiday_date');
            $filter_data = array();
            $filter_data['filter_date_from'] = $schedule_start_date;
            /** @noinspection PhpUndefinedMethodInspection */
            $last_cron_date = (array_values(array_slice($cron_dates, -1))[0])->format('Y-m-d');
            $filter_data['filter_date_end'] = $last_cron_date;
            $bank_holiday = BankHolidayAction::getInstance($this->registry);
            $bank_holiday_dates = $bank_holiday->getBankHolidayDates($filter_data, $select_data);
            $bank_holiday_dates = (empty($bank_holiday_dates) ? array() : array_column($bank_holiday_dates, 'holiday_date'));
            
            // Loop over the cron dates       
            foreach ($cron_dates as $date) {

                // Return the dates if we have already got the $schedule_days_for_nach count for dates
                if( count($date_arr) >= $schedule_days_for_nach ) {
                    return $date_arr;
                }
            
                // Get the date in Y-m-d format
                /** @noinspection PhpUndefinedMethodInspection */
                $nach_debit_date = $date->format('Y-m-d');

                // Check that the date is not a Bank holiday and No schedule exists as well on that date for the suborder
                if (!in_array($nach_debit_date, $bank_holiday_dates )
                    && !$this->checkIfNachScheduleExists($suborder_id, $nach_debit_date)) {

                    $date_arr[] = $nach_debit_date;
                }
            }
            
            // Reset the schedule_start_date to last_date + 1 day. In case, the required count of dates is not done yet
            $schedule_start_date = date('Y-m-d', strtotime('+1 day', strtotime($last_cron_date)));
        }

        return $date_arr;
    }

    /**
     * Public method to generate NACH Schedule for deliverd wsb_credit orders
     *
     * Logic:- Get unique delivered suborder(s) for creating NACH schedule(s)
     *             - Get suborder bal for all suborder
     *             - If pending suborder balance to recover then create schedule(s)
     *             - Update field 'wsb_credit_nach_done' in oc_suborder as value 'YES'
     * @throws Exception
     * @author Nishu, March 2019
     */
    public function generateNachSchedule(){

        $nach_not_created_once = true;
        $auto_nach_enabled     = true;
        $check_delivered       = true;

        //Step1 : Get Suborder(s) which has been delivered and not scheduled NACH yet
        $data = $this->nach->getSubordersForNachSchedule($nach_not_created_once, $auto_nach_enabled, $check_delivered);

        //Step2 : Get next schedule date for Crontab string using third party library 
        if(!empty($data)){
            $data = array_combine(
                        array_column($data, 'suborder_id'), 
                        $data)
                    ;

            foreach ($data as $suborder_id => $suborder) {

                $order_id     = $suborder['order_id'];
                $suborder_bal = Suborder::getSubOrderBalanceAmount($this->registry, (int)$order_id, $suborder_id);

                if($suborder_bal < 0.00){
                    
                    $suborder_bal = abs($suborder_bal);

                    //Prepare NACH Schedule and INSERT into DB
                    $nach_schedules = $this->prepareNachSchedules($suborder, (float)$suborder_bal);

                    //Add NACH schedule entry into DB, By simply insert query
                    foreach ($nach_schedules as $key => $nach_schedule) {
                        //addNachSchedule (Simple INSERT database query in NACH main class)
                        $this->nach->addNachSchedule($nach_schedule);

                    }//End of Foreach LOOP
                }
                
                //Mark suborder as wsb_credit_nach payment done
                Suborder::updateField( $this->db, 'wsb_credit_nach_done', 'YES', $suborder_id) ;
                    
            }//End of Outter Foreach Loop
        }
    }

    /**
     * Behaviour to add new Bank Holiday dates.
     * 
     * Logic:  - If input date is older than Current System Date, then error is reported. Also invalid format is reported.
     *         - Dates are inserted in the bank_holiday table and any other errors during that process are reported.
     *         - NACH Schedules on valid inserted dates are disabled (status is updated to SUDDEN_BANK_HOLIDAY), and
     *           the amount id (re-)distributed into remaining editable schedules for the corresponding suborder_id.
     * 
     * @param array $dates Array of all the dates to be added
     * @param string $remark Represents the user comment/remark on why bank holiday is being added
     * 
     * @return array If no errors, empty. Else contains various error messages.
     */
    public function addBankHolidayDates(array $dates, string $remark) : array {

        // Return summary initialization
        $error_summary = array();
        
        // Validation of $remark
        $remark = trim($remark);
        if ( empty($remark) ) {
            $error_summary[] = 'Non-empty remark/comment is required while adding new Bank holiday date(s). No dates could be added.';
            return $error_summary;
        }
        
        // Initializing common data before loop
        $bank_holiday_obj = BankHolidayAction::getInstance($this->registry);
        $edit_schedule = array();
        $edit_schedule['status'] = 'SUDDEN_BANK_HOLIDAY';
        $edit_comment = 'Schedule deffered due to late marking of Bank Holiday (Reason: ' . $remark . ')';

        //Loop over dates
        foreach ($dates as $date) {
            
            $error = '';
            // Date Validations
            if (!validateDate($date, 'Y-m-d') ) { // check for valid yyyy-mm-dd            
                $error = 'Invalid input date value: ' . $date . ' Should be in yyyy-mm-dd format.';
            } elseif(strtotime($date) < strtotime('today')){
                $error = 'Input date: ' . $date . ' is disallowed. We cannot mark Bank holiday for Old date.';
            } elseif(strtotime($date) == strtotime('today') && !$this->checkIfCurrentTimeIsGoodToEdit()) {
                $error = 'Input date: ' . $date . ' is Today! Time limit is over for marking Current date ' . 
                         'as Bank Holiday. Last time deadline to update is: ' . 
                         self::DAILY_NACH_SCHEDULE_EDIT_HOUR_LIMIT . ':' . self::DAILY_NACH_SCHEDULE_EDIT_MINUTE_LIMIT;
            }
            
            // If error - populate summary, continue to next date
            if ( !empty($error) ) {
                $error_summary[] = $error;
                continue;
            }
            
            // ADD the date - catch any exception and store in error summary
            try {
                $bank_holiday_obj->addBankHolidayDate(array('holiday_date' => $date, 'remark'=> $remark));

                // GET Editable NACH schedule(s) on the holiday date, if any, to SHIFT
                $scheds = $this->getEditableNachSchedulesInDateRange($date, $date, array('nach_schedule_id','suborder_id','nach_debit_amount'));
                
                // Update the schedules to SUDDEN_BANK_HOLIDAY status, one by one and Distribute their amount in remaining schedules
                foreach ($scheds as $sd) {
                    $nach_schedule_id = (int)$sd['nach_schedule_id'];
                    $edit_log = $this->nach->editNachSchedule($nach_schedule_id, $edit_schedule, $edit_comment);
                    if (!empty($edit_log)) {
                        $dist_comment = 'Redistribution due to SUDDEN_BANK_HOLIDAY in Schedule ID: ' . $nach_schedule_id . ' dated ' . $date;
                        $this->distributeAmountInRemainingSchedules($sd['suborder_id'],
                                                          $date . ' +1 day', // Next day of the holiday marked
                                                                   (float)$sd['nach_debit_amount'],
                                                                   $dist_comment,
                                                                   array($nach_schedule_id),
                                                   false);
                    }
                }
            } catch (Exception $e) {
                $error_summary[] = $e->getMessage();
            }
        } // End of Loop on dates

        return $error_summary;
    }


    /*
     * Behvaiour to change debit amount in a given schedule.
     * 
     * Logic:  - Checks and gets if the NACH schedule is editable or not, based on mandatory conditions.
     *         - Calculate the difference amount between old amount and new amount (to be edited to).
     *         - If difference exists, edits the amount in the given schedule.
     *         - Re-Distributes the difference amount in Remaining Schedules.
     * 
     * @param $nach_schedule_id - Int - nach_schedule_id which is to be edited
     * @param $new_amount - Float - new amount to be put in the schedule. Should be > 0 else @throws Exception
     * @param $comment - String - User comment on why amount is changed. Should be non-empty after trim else @throws Exception
     * 
     * @return Array - If no errors, empty. Else contains various error messages.
     * 
     * @author Nishu, May 2019
     */
    public function changeAmountInNachSchedule(int $nach_schedule_id, float $new_amount, string $comment) : array {
        
        // Return summary initialization
        $error_summary = array();
        
        // Validations / Assertions
        $comment = trim($comment);
        if (empty($comment)) $error_summary[] = 'Proper Comment (non empty) is required to change amount in NACH Schedule!!';
        
        $new_amount = round($new_amount, 2);
        if ($new_amount <= 0.00) $error_summary[] = 'New Amount has to be positive while changing Amount in NACH Schedule!!';
        
        // Checks and gets if the NACH schedule is editable or not, based on mandatory conditions
        $base_schedule_details = $this->checkAndGetIfNachScheduleIsEditable($nach_schedule_id);
        if(empty($base_schedule_details)) $error_summary[] = 'NACH Schedule is not editable (Amount cannot be changed)!!';
        
        // Calculate the difference amount between old amount and new amount (to be edited to).
        $old_amount = round((float)$base_schedule_details['nach_debit_amount'], 2);
        $difference_amt = $old_amount - $new_amount;
        if ($difference_amt === 0.00) $error_summary[] = 'Old and New Changed amount of the Schedule are same; Nothing Changed!!';
        
        if ( !empty($error_summary) ) return $error_summary;
 
        // If difference exists, edits the amount in the given schedule
        $edit_schedule = array();
        $edit_schedule['nach_debit_amount'] = $new_amount;
        $remark = 'Amount changed and Redistributed to next schedules. Comment: ' . $comment;
        $this->nach->editNachSchedule($nach_schedule_id, $edit_schedule, $remark);
        
        // Re-Distributes the difference amount in Remaining Schedules
        $remark2 = 'Redistribution in this schedule, due to amount change in Schedule ID: ' . $nach_schedule_id . 
                   ' dated ' . $base_schedule_details['nach_debit_date'] . '. Comment: ' . $comment;
        $this->distributeAmountInRemainingSchedules($base_schedule_details['suborder_id'],
                                                    $base_schedule_details['nach_debit_date'],
                                                    $difference_amt,
                                                    $remark2, 
                                                    array($nach_schedule_id),
                                    false);
        return $error_summary;
    }

    /**
     * @info Distribute a given difference Amount into remaining Editable schedules of a given suborder, from a given date.
     *
     * Difference amount is split equally into all the schedules. In case of balance amount left, it is adjusted
     * completely into the last schedule. It is possible to skip certain schedules, for instance, ignore the schedule
     * whose amount was changed, which eventually triggered redistribution. Also, it is possible to ignore redistribution,
     * if not a singe schedule (whether editable or not) has been generated for the given suborder. For eg: credit note
     * generation should cause adjustment in schedules. However, sometimes a CN can be generated before the actual
     * schedules have been generated. So, we can simply ignore the redistribution in this case, because the schedule
     * generating cron will then auto-adjust for the credit note amount.
     *
     * @param string $suborder_id Suborder Id
     * @param string $date_from Date to consider schedules from. Can be a valid string parseable by @function strtotime().
     * @param float $amount_to_distribute Amount to be redistributed. If the value is 0, then does nothing.
     * @param string $comment Comment on why the difference amount is being redistributed.
     * @param array $skip_nach_schedule_id Array of int. To skip specific schedule(s) for redistributing the amount into. Defaults to empty array.
     * @param bool $ignore_no_schedule Ignore if not a single schedule exists for the Suborder (editable or not). Defaults to false.
     * @throws Exception if no valid @param $comment is provided. It should not be empty string after trimming.
     * @throws Exception
     * @author Madhur, 2019
     */
    public function distributeAmountInRemainingSchedules(string $suborder_id,
                                                         string $date_from,
                                                         float  $amount_to_distribute, 
                                                         string $comment, 
                                                         array  $skip_nach_schedule_id = array(),
                                                         bool   $ignore_no_schedule = false) :  void {
        
        // Validation / Assertions
        $comment = trim($comment);
        if (empty($comment)) throw new Exception('Comment needed while Distributing an amount in remaining schedules!');

        $amount_to_distribute = round($amount_to_distribute,2);
        if ( $amount_to_distribute === 0.00 ) {
            // Do nothing
        } elseif ( $amount_to_distribute < 0.0 ) { // Negative Difference Amount

            // Get remaining EDITABLE schedules
            $select_data = array('nach_schedule_id', 'nach_debit_amount');
            $remaining_schedule_details = $this->getEditableNachSchedulesOfSuborder($suborder_id, $date_from, $select_data, $skip_nach_schedule_id);

            // Redistribute amount (if $remaining_schedule_details is empty, this function will do nothing and return back the difference amount)
            $this->reDistributeNegativeDifferenceAmount($remaining_schedule_details, $amount_to_distribute, $comment);

        } else {  // Positive Difference Amount

            // Check if there are no schedules generated at all for this suborder, and ignore_no_schedule mode is true
            if ($ignore_no_schedule) {
                $filter_data = array();
                $filter_data['filter_suborder_id'] = $suborder_id;
                $filter_data['limit']              = 1;
                $select_data = array('suborder_id');
                if (empty($this->nach->getNachSchedules($filter_data, $select_data))) return; // Return if no schedule ever created
            }

            // Create a new schedule as soon as a vacant slot is found after a gap of 1 day (+2 days)
            $suborder = array();
            $suborder['suborder_id']            = $suborder_id;
            $suborder['schedule_days_for_nach'] = 1;
            $suborder['days_before_nach_start'] = 0;
            $suborder['nach_schedule_crontab']  = self::DEFAULT_NACH_SCHEDULE_CRONTAB;
            $suborder['delivered_date']         = $date_from . ' +2 days';
            $nach_schedules = $this->prepareNachSchedules($suborder, $amount_to_distribute);
            foreach ($nach_schedules as $key => $nach_schedule) {
                $this->nach->addNachSchedule($nach_schedule, true, $comment);
            }
        }
    }

    /**
     * Behvaiour to defer a given NACH Schedule.
     *
     * Logic:  - Checks and gets if the NACH schedule is editable or not, based on mandatory conditions.
     *         - If editable, edits the deffered_by_customer of given schedule to 1.
     *         - Re-Distributes the amount of the deffered schedule.
     *
     * @param int $nach_schedule_id - nach_schedule_id which is to be deffered.
     * @return array If no errors, empty. Else contains various error messages.
     * @throws Exception
     * @author Nishu, May 2019
     */
    public function deferNachSchedule(int $nach_schedule_id) : array {
        
        // Return error summary initialization
        $error_summary = array();
        
        // Checks and gets if the NACH schedule is editable or not, based on mandatory conditions
        $base_schedule_details = $this->checkAndGetIfNachScheduleIsEditable($nach_schedule_id);
        if( empty($base_schedule_details) ){
            $error_summary[] = 'Given NACH Schedule ID: '. $nach_schedule_id .' is not editable (not defferable)!';
            return $error_summary;
        }
        
        // If editable, edits the deffered_by_customer of given schedule to 1.
        $edit_schedule = array();
        $edit_schedule['deffered_by_customer'] = 1;
        $remark = 'Schedule is deffered and its amount is redistributed to next coming schedules';
        $this->nach->editNachSchedule($nach_schedule_id, $edit_schedule, $remark);
        
        // Re-distribute the amount in the Remaining Schedules
        $amount_to_distribute = round((float)$base_schedule_details['nach_debit_amount'],2);
        $remark2 = 'Redistribution in this schedule, due to Schedule ID: ' . $nach_schedule_id . ' marked Deffered';
        $this->distributeAmountInRemainingSchedules($base_schedule_details['suborder_id'],
                                                    $base_schedule_details['nach_debit_date'],
                                                    $amount_to_distribute,
                                                    $remark2,
                                                    array($nach_schedule_id),
                                    false);
                                                    
        return $error_summary;
    }
    
    /*
     * Behvaiour to defer a NACH Schedule(s) of a given customer, on a given date.
     * 
     * Logic:  - Gets the schedules of the given customer on the given date.
     *         - Call deferNachSchedule on the fetched schedules.
     * 
     * @param $customer_id - Int - Given customer id, whose schedules are to be deffered
     * @param $date - String - Given date
     * 
     * @return Array - All error messages (if any)
     * 
     * @author Nishu, May 2019
     */
    public function deferNachSchedulesOfCustomer(int $customer_id, string $date) : array {
        
        $error_summary = array();
        
        // Validate $date. Should be non-empty and of valid YYYY-MM-DD format
        $date = trim($date);
        if ( empty($date) ) {
            $error_summary[] = 'Invalid date given for Deffering. It should be non-empty !!';
        } elseif ( !validateDate($date, 'Y-m-d') ) {
            $error_summary[] = 'Invalid date given: ' . $date . ', for Deffering. It should be in valid YYYY-MM-DD format !!';
        }
        
        if ( $customer_id  <= 0 ) {
            $error_summary[] = 'Invalid Customer ID: ' . $customer_id . ' given for Deffering. Nothing could be done!';
        }

        /** @noinspection DuplicatedCode */
        if ( !empty($error_summary) ) {
            return $error_summary;
        }
            
        // Gets the schedules of the given customer on the given date.
        $filter_data = array();
        $filter_data['filter_nach_debit_date_from'] = $date;
        $filter_data['filter_nach_debit_date_to']   = $date;
        $filter_data['filter_customer_id']          = $customer_id;
        $filter_data = $this->applyMandatoryFiltersForEditableNachSchedule($filter_data);
        $select_data = array( 'nach_schedule_id' );
        $schedules = $this->nach->getNachSchedules($filter_data, $select_data);
        
        // Error message when no schedule could be found for Deffering
        if ( empty($schedules) ) {
            $error_summary[] = 'No Active Schedule found to Deffer for the CID: ' . $customer_id . ' on ' . 
                               'date: ' . $date . '; Nothing could be done!';
        }
        
        // Deffer them in a Loop using deferNachSchedule method
        foreach ($schedules as $key => $schedule) {
            try {
                $errors = $this->deferNachSchedule((int)$schedule['nach_schedule_id']);
                foreach ($errors as $error) {
                    $error_summary[] = $error;
                }
            } catch (Throwable $e) {
                $error_summary[] = $e->getMessage();
            }
        }

        return $error_summary; 
    }

    /*
     * Behvaiour to shift a given NACH Schedule, to a date (if given, else to the very end).
     * 
     * Logic:  - Validate Date and Comment. Date shoule be > CURRENT_DATE, and Not a bank Holiday. Comment should be non empty.
     *         - Check and get details if NACH Schedule is editable. If not, return error message.
     *         - If $new_date is given, check that the schedule's current date is not same as new date, else return error message.
     *           - We get NACH Schedule on the new date. If any schedule is found:  
     *             - If the found schedule is editable, then its amount is revised.
     *             - Else, if the found schedule is not editable, then its details are changed to Shifted schedule
     *           - Else, No schedule is found, we create a new schedule based on the Shifted schedule
     *         - Else, if no $new_date given, We shift it to a date beyond the last date out of all schedules for this Suborder
     *         - Finally, edit the Shifted Schedule to SHIFT_SCHEDULE status
     * 
     * @param $nach_schedule_id - Int - nach_schedule_id which is to be shifted.
     * @param $new_date- String - New date (to which Schedule will be shifted). Should be in valid Y-m-d format
     *                            If the $new_date is empty, then it shifts the Schedule to very end of the suborder of the schedule.
     *                            If given, $new_data has to be more than Current System date. Schedules cant be shifted to 
     *                            current date. If the $new_date < CURRENT_DATE(), then error message is reported.
     * @param $comment - String - Comment on why the schedule is being shifted. Mandatory. If empty, error message reported.
     * 
     * @return Array - If no errors, empty. Else contains various error messages.
     * 
     * @author Nishu, May 2019
     * @author Madhur, June 2019
     */
    public function shiftNachSchedule(int $nach_schedule_id, string $new_date, string $comment) : array {
        
        $error_summary = $this->validateInputsForShiftSchedule($new_date, $comment);
        
        // Check given NACH schedule is editable
        $base_schedule_details = $this->checkAndGetIfNachScheduleIsEditable($nach_schedule_id);
        if( empty($base_schedule_details) ){
            $error_summary[] = 'Given NACH Schedule ID: '. $nach_schedule_id .' is not editable (not shiftable)!';
            
        } elseif ( !empty($new_date) && strtotime($base_schedule_details['nach_debit_date']) == strtotime($new_date) ) {
        // Check that the new date is not same as the current date of the schedule being shifted
            $error_summary[] = 'Current Schedule Date: ' . $base_schedule_details['nach_debit_date'] . 
                               ', cannot be Same as the New date: ' . $new_date . ' !!';
        }
        
        // If any error upto this point, we return the error instead of proceeding further
        if ( !empty($error_summary) ) {
            return $error_summary;
        }
        
        // Storing Current Schedule (the one being shifted) data in easy to access variables
        $curr_suborder_id = $base_schedule_details['suborder_id'];
        $curr_nach_debit_amount = round((float)$base_schedule_details['nach_debit_amount'], 2);
        $curr_status = $base_schedule_details['status'];
        $curr_deffered_by_customer = $base_schedule_details['deffered_by_customer'];
        $curr_nach_debit_date = $base_schedule_details['nach_debit_date'];
        
        // Initializing variable to store data in case we need to Add new schedules
        $add_schedule_data = array();
        $add_schedule_remark = '';
        
        // If $new_date is given, we get NACH Schedule on the new date (if existing)
        if ( !empty($new_date) ) {            
            
            $filter_data = array();
            $filter_data['filter_nach_debit_date_from'] = $new_date;
            $filter_data['filter_nach_debit_date_to']   = $new_date;
            $filter_data['filter_suborder_id']          = $curr_suborder_id; 
            $select_data = array('nach_schedule_id', 'nach_debit_amount', 'status', 'deffered_by_customer');
            $schedule_to_edit = $this->nach->getNachSchedules($filter_data, $select_data);
            
            // If any schedule is found, then we edit it.
            if ( !empty($schedule_to_edit[0]) ) {
                $schedule_id_to_edit = (int)$schedule_to_edit[0]['nach_schedule_id'];
                $editable = $this->checkAndGetIfNachScheduleIsEditable($schedule_id_to_edit);
                
                $edit_schedule = array();
                // If the schedule is editable, then its amount is revised
                if ( !empty($editable) ) {
                    $edit_schedule['nach_debit_amount'] = $curr_nach_debit_amount + 
                                                          round((float)$schedule_to_edit[0]['nach_debit_amount'], 2);
                    $remark = 'Schedule ID: ' . $nach_schedule_id . ' dated ' . $curr_nach_debit_date . 
                              ' shifted and its amount added in this schedule. Comment: ' . $comment;
                } else { 
                    // Else, if the schedule is not editable, then its details are changed to Shifted schedule
                    $edit_schedule['nach_debit_amount'] = $curr_nach_debit_amount;
                    $edit_schedule['status'] = $curr_status;
                    $edit_schedule['deffered_by_customer'] = $curr_deffered_by_customer;
                    $remark = 'Schedule ID: ' . $nach_schedule_id. ' dated ' . $curr_nach_debit_date . 
                              ' shifted, replacing this earlier Inactive schedule. Comment: ' . $comment;
                }
                
                $this->nach->editNachSchedule($schedule_id_to_edit, $edit_schedule, $remark);
                $comment .= '; This schedule shifted to date: ' . $new_date . '; ID: ' . $schedule_id_to_edit;
                
            } else {
                // No schedule is found, we create a new schedule based on the Shifted schedule
                $add_schedule_data['suborder_id']        = $curr_suborder_id;
                $add_schedule_data['nach_debit_date']    = $new_date;
                $add_schedule_data['nach_debit_amount']  = $curr_nach_debit_amount;
                $add_schedule_remark = 'Schedule ID: ' . $nach_schedule_id . ' dated ' . $curr_nach_debit_date . 
                                       ' shifted, causing creation of this Schedule. Comment: ' . $comment;
            }
            
        } else { // We shift it to a date beyond the last date out of all schedules for this Suborder

            $last_nach_date = $this->getLastScheduleDateOfSuborder($curr_suborder_id);
            
            // Get a day beyond the last date
            $suborder = array();
            $suborder['suborder_id']            = $curr_suborder_id;
            $suborder['schedule_days_for_nach'] = 1;
            $suborder['days_before_nach_start'] = 0;
            $suborder['nach_schedule_crontab']  = self::DEFAULT_NACH_SCHEDULE_CRONTAB;
            $suborder['delivered_date']         = date('Y-m-d', strtotime($last_nach_date.' + 1 day'));

            $add_schedule_data = $this->prepareNachSchedules($suborder, $curr_nach_debit_amount)[0];
            $add_schedule_remark = 'Schedule ID: ' . $nach_schedule_id . ' dated ' . $curr_nach_debit_date . 
                                   ' shifted to end, creating this schedule; Last NACH date was ' . $last_nach_date . 
                                   '. Comment: ' . $comment;
        }
        
        // Add if any Schedule to add, with Logging enabled.
        if ( !empty($add_schedule_data) ) {
            $new_schedule_id = $this->nach->addNachSchedule($add_schedule_data, true, $add_schedule_remark);
            $comment .= '; Schedule shifted to new ID: ' . $new_schedule_id . ' dated ' . $add_schedule_data['nach_debit_date'];
        }
        
        // Now edit the Shifted Schedule to SHIFT_SCHEDULE status
        $edit_schedule = array();
        $edit_schedule['status'] = 'SHIFT_SCHEDULE';
        $this->nach->editNachSchedule($nach_schedule_id, $edit_schedule, $comment);
        
        return $error_summary;
    }

    /*
     * Behvaiour to shift a NACH Schedule(s) of a given customer.
     * 
     * Logic:  - Validates input New date and comment. Returns error message(s), if invalid.
     *         - Validates Given date. It should be non-empty, valid YYYY-MM-DD format, and cannot be same as New date
     *         - Gets all the editable schedules of the Customer on the given date
     *         - Shift them in a Loop using shiftNachSchedule method.
     * 
     * @param $customer_id - Int - Given customer id, whose schedules are to be shifted
     * @param $given_date - String - Date to be shifted from. Required non-empty and valid YYYY-MM-DD format, else error reported
     * @param $new_date - String - Date to be shifted to. Can be empty. If empty, shifted to end of resepective suborder.
     * @param $comment - String - Required non-empty, else error reported.
     * 
     * @return Array - All error messages (if any)
     * 
     * @author Nishu, May 2019
     * @author Madhur, June 2019
     */
    public function shiftNachSchedulesOfCustomer(int $customer_id, string $given_date, string $new_date, string $comment) : array {

        // Validates input date and comment
        $error_summary = $this->validateInputsForShiftSchedule($new_date, $comment);
        
        // Validate $given_date. Should be non-empty and of valid YYYY-MM-DD format
        $given_date = trim($given_date);
        if ( empty($given_date) ) {
            $error_summary[] = 'Invalid date given to shift FROM. It should be non-empty !!';
        } elseif ( !validateDate($given_date, 'Y-m-d') ) {
            $error_summary[] = 'Invalid date given: ' . $given_date . ', to shift FROM. It should be in valid YYYY-MM-DD format !!';
        } elseif ( strtotime($given_date) == strtotime($new_date) ) {
            $error_summary[] = 'Given date: ' . $given_date . ', cannot be Same as the New date: ' . $new_date . ' !!';
        }

        /** @noinspection DuplicatedCode */
        if ( !empty($error_summary) ) {
            return $error_summary;
        }
        
        // Gets all the editable schedules of the Customer on the given date
        $filter_data = array();
        $filter_data['filter_nach_debit_date_from'] = $given_date;
        $filter_data['filter_nach_debit_date_to']   = $given_date;
        $filter_data['filter_customer_id']          = $customer_id;
        $filter_data = $this->applyMandatoryFiltersForEditableNachSchedule($filter_data);
        $select_data = array( 'nach_schedule_id' );
        $schedules = $this->nach->getNachSchedules($filter_data, $select_data);
        
        // Error message when no schedule could be found for shifting
        if ( empty($schedules) ) {
            $error_summary[] = 'No Active Schedule found to Shift for the CID: ' . $customer_id . ' on ' . 
                               'date: ' . $given_date . '; Nothing could be done!';
        }
        
        // Shift them in a Loop using shiftNachSchedule method
        foreach ($schedules as $key => $schedule) {
            try {
                $errors = $this->shiftNachSchedule((int)$schedule['nach_schedule_id'], $new_date, $comment);
                foreach ($errors as $error) {
                    $error_summary[] = $error;
                }
            } catch (Throwable $e) {
                $error_summary[] = $e->getMessage();
            }
        }
        
        return $error_summary;
    }

    /*
     * @info Public function to get tentative NACH Sheet data, with additional information from other tables.
     *       Return data is grouped at customer level, and then order level. However, raw breakup (upto individual rows 
     *       in the table) is available in the return data structure.
     *       It is primarily used for NACH Schedule display, CSV download, Bank upload Sheet generation etc
     *       
     * @param $filter_data - Array. Check the documentation of NachAction::getNachSchedulesWithAdditionalInfo method 
     *        to get a list of applicable filter keys in the input param $data.
     * @note There is an additional filter key available: 'filter_today_active_checksum'. If this is provided and 
     *       its value is non-empty; then it would override the following filters and apply the following conditions instead:
     * 
     *       $filter_data['filter_order_payment_id']     = 'NULL';
     *       $filter_data['filter_nach_debit_date_from'] = date('Y-m-d');
     *       $filter_data['filter_nach_debit_date_to']   = date('Y-m-d');
     *       $filter_data['filter_deffered_by_customer'] = 0;
     *       $filter_data['filter_status']               = 'NOT_DONE';
     * 
     * This filter is basically used to enforce the case when it is required to fetch only those NACH schedule(s) 
     * which would show up in daily Bank Debit sheet. Moreover, it will also do checksum to ensure limits etc are ensured.
     * 
     * @return: Array (Full row-level data from main NACH table with additional details from other tables)
     * @author: Nishu, May 2019
     */
    public function getNachSchedulesWithAdditionalInfo(array $filter_data) : array {
        
        // If filter_today_active_checksum is provided and it is non-empty (not 0, false, null etc)
        if ( !empty($filter_data['filter_today_active_checksum']) ) {
            $filter_data['filter_order_payment_id']      = 'NULL';
            $filter_data['filter_nach_debit_date_from']  = date('Y-m-d');
            $filter_data['filter_nach_debit_date_to']    = date('Y-m-d');
            $filter_data['filter_deffered_by_customer']  = 0;
            $filter_data['filter_status']                = 'NOT_DONE';
            $filter_data['filter_today_active_checksum'] = true;
        } else {
            $filter_data['filter_today_active_checksum'] = false;
        }

        $result = $this->nach->getNachSchedulesWithAdditionalInfo($filter_data, 
                                                                  $filter_data['filter_today_active_checksum']);

        // Apply checksum only if it is enabled using filter_today_active_checksum
        // Also check if current System time is Good to Edit
        if( !empty($filter_data['filter_today_active_checksum']) && $this->checkIfCurrentTimeIsGoodToEdit() ) {
            
            //Filtering result by checking order balance
            $result = $this->checksumAtOrderLevel($result);

            //Filtering result data for min transaction value limit at customer level
            $result = $this->checksumAtCustomerLevel($result);
            
            // Post filtering, we bulk update the customer_nach_details_id to Current Active one
            $nach_schedule_ids_to_update = array_column($result, 'nach_schedule_id');
            
            $this->nach->updateCustomerNachDetailsIdToDefault($nach_schedule_ids_to_update);
        }
        return $result;
    }

    /*
     * @info: Public method to group the individual schedule data (array) 
     *        by customer_id and nach_debit_date, to get overall debit details for the customer on a specific date.
     * @param: $data array (of arrays - internal array representing a schedule detail)
     * @return: array (of arrays. Internal array represent debit details for a customer. Contains following keys: 
     *                 customer_id, account_name, account_no, ifsc_code, bank_type, umrn_no, lan_no, nach_debit_amount, nach_debit_date)
     * @author: Nishu, March 2019
     */
    public function groupSchedulesByCustomer(array $data) : array {
        $grouped_data = array();
        
        foreach ($data as $value) {
            
            if ( !empty($value['customer_id']) && !empty($value['nach_debit_date']) ) {
                
                $comp_key = $value['customer_id'] . '|' . $value['nach_debit_date'];
                
                if ( empty($grouped_data[$comp_key]) ) {
                    $grouped_data[$comp_key]                     = array();
                    $grouped_data[$comp_key]['customer_id']      = (int)$value['customer_id'];
                    $grouped_data[$comp_key]['nach_debit_date']  = $value['nach_debit_date'];
                    $grouped_data[$comp_key]['account_name']     = $value['account_name'] ?? '';
                    $grouped_data[$comp_key]['account_no']       = $value['account_no'] ?? '';
                    $grouped_data[$comp_key]['ifsc_code']        = $value['ifsc_code'] ?? '';
                    $grouped_data[$comp_key]['bank_type']        = $value['bank_type'] ?? '';
                    $grouped_data[$comp_key]['umrn_no']          = $value['umrn_no'] ?? '';
                    $grouped_data[$comp_key]['lan_no']           = $value['lan_no'] ?? '';
                    $grouped_data[$comp_key]['nach_debit_amount'] = 0;
                }
                
                $grouped_data[$comp_key]['nach_debit_amount'] += round((float)($value['nach_debit_amount'] ?? 0), 2);
            }
        }
        
        return $grouped_data;
    }

    /**
     * @info Get Nach Schedule History (changelog)
     * @param int $nach_schedule_id
     * @return array of Log records
     * @author Nishu, May 2019
     */
    public function getScheduleHistory(int $nach_schedule_id) : array {
        return $this->nach->getNachScheduleLog( $nach_schedule_id );
    }

    /**
     * @info Public method to generate NACH Sheets in Bank format, for upload to Bank.
     *        It will generate sheets for various BankNach classes, and return the file paths in an array
     *        Currently, classes for various bank_type values are: StancBankNach, YesBankNach.
     * @param array $data - Array of array (schedules). Obtained using getNachSchedulesWithAdditionalInfo function.
     *          Internal array must contain 'bank_type' key; else the particular schedule is ignored.       
     * @return array - File paths of various Bank sheets generated
     * @author Nishu, March 2019
     */
    public function generateBankSheet(array $data) : array {
        
        $bank_sheets = array();
        
        // Array of Bank classes - with key as bank_type
        $bank_classes = array('YES_BANK'=>'YesBankNach', 'STANC'=>'StancBankNach');

        // Group data based on bank type
        $data_grouped_by_bank = array();
        foreach ($data as $value) {
            if ( !empty($value['bank_type']) ) {
                $data_grouped_by_bank[$value['bank_type']][] = $value;
            }
        }
        
        // LOOP over bank classes and generate csv
        foreach ($bank_classes as $bank_type => $bank_class) {
            
            if( !empty($data_grouped_by_bank[$bank_type]) ) {
                $obj = new $bank_class($this->registry);
                /** @noinspection PhpUndefinedMethodInspection */
                $bank_sheets[] = $obj->generateNachSheet($data_grouped_by_bank[$bank_type]);
            }
        }

        return $bank_sheets;
    }
    
    /**
     * @info Convert an array of NACH schedule(s), into another array structure, by grouping them on customer basis
     *
     * Generally, this method is called after receiving Raw schedule data
     * from the database tables, using getNachScheduleWithAdditionalInfo function etc
     * 
     * Grouped data basically represents a single row in the bank sheet with total amount, debit date, 
     * debit account details etc. A single row in the Bank sheet can be UNIQUEly identified on a combination of 
     * customer_id, nach_debit_date, umrn_no, and lan_no
     * 
     * @param array $data array of array. Internal array contains a specific NACH schedule row with additional details.
     * 
     * @return array Customerwise grouped data. Key in the array will be a composite of
     *                  customer_id, nach_debit_date, umrn_no, and lan_no
     * Value (internal array) will contain following keys at first level:
     * - total_nach_debit_amount : Sum of all the debit amounts.
     * - nach_debit_date : Date on which the Debit will be done.
     * - customer_id : Customer Id
     * - nach_schedules : Array - contains all the schedules in raw form (rowwise)
     * Additional keys will be: customer_name, account_name, account_no, ifsc_code, umrn_no, lan_no, bank_type
     * 
     * @author Nishu, May 2019
     * @author Madhur, June 2019
     */
    public function groupDataByCustomer(array $data) : array {
        $result = array();

        //Loop over nach schedules
        foreach ($data as $key => $value) {
            
            $customer_id = (int)($value['customer_id'] ?? 0);
            $nach_debit_date = trim($value['nach_debit_date'] ?? '');
            $umrn_no = trim($value['umrn_no'] ?? '');
            $lan_no = trim($value['lan_no'] ?? '');
            
            // Composite key to determine a specific customerwise row in a Bank sheet
            $comp_key = ( $customer_id . 
                          (!empty($nach_debit_date) ? '_' . $nach_debit_date : '') . 
                          (!empty($umrn_no)         ? '_' . $umrn_no         : '') . 
                          (!empty($lan_no)          ? '_' . $lan_no          : '') );
                           
            if ( !isset($result[$comp_key]) ) {
                $result[$comp_key]['nach_schedules']           = array();
                $result[$comp_key]['total_nach_debit_amount']  = 0.00;
                $result[$comp_key]['customer_id']              = $customer_id;
                $result[$comp_key]['nach_debit_date']          = $nach_debit_date;
                $result[$comp_key]['umrn_no']                  = $umrn_no;
                $result[$comp_key]['lan_no']                   = $lan_no;
                $result[$comp_key]['customer_name']            = trim($value['customer_name'] ?? '');
                $result[$comp_key]['account_name']             = trim($value['account_name'] ?? '');
                $result[$comp_key]['account_no']               = trim($value['account_no'] ?? '');
                $result[$comp_key]['ifsc_code']                = trim($value['ifsc_code'] ?? '');
                $result[$comp_key]['bank_type']                = trim($value['bank_type'] ?? '');
            }
            $result[$comp_key]['nach_schedules'][]          = $value;
            $result[$comp_key]['total_nach_debit_amount']   += round((float)($value['nach_debit_amount'] ?? 0.00),2);
        }

        return $result;
    }


    /*
     * @info: Public method to convert an array of NACH schedule(s), into 
     *        another array structure, by grouping them on order basis
     * 
     * @param: $data - array of array. Internal array contains a specific NACH schedule row with additional details.
     * 
     * @return: array - Orderwise grouped data. Key in the array will be order_id
     * Value (internal array) will contain following keys at first level:
     * - order_id : Order Id
     * - order_no : Order No
     * - order_bal : Balance amount of that order (negative means recovery pending)
     * - total_nach_debit_amount : Sum of all the debit amounts for that order.
     * - nach_schedules : Array - contains all the schedules in raw form (rowwise) for that order.
     * 
     * @author: Nishu, May 2019
     * @author: Madhur, June 2019
     */
    public function groupDataByOrder(array $data) : array {
        $result = array();
        
        //Loop over nach schedules
        foreach ($data as $key => $value) {
            
            //Set order_id
            $order_id = (int)($value['order_id'] ?? 0);

            if ( !isset($result[$order_id]) ) {
                $result[$order_id]['order_id']                = $order_id;
                $result[$order_id]['order_no']                = $value['order_no'];
                $result[$order_id]['order_bal']               = $value['order_bal'] ?? '--';
                $result[$order_id]['total_nach_debit_amount'] = 0.00;
                $result[$order_id]['nach_schedules']          = array();
            }
            $result[$order_id]['nach_schedules'][]            = $value;
            $result[$order_id]['total_nach_debit_amount']     += round((float)($value['nach_debit_amount'] ?? 0.00),2);
        }
        
        return $result;
    }


    /**
     * @info Public method to update bank receipt info using given csv data at NACH schedule level,
     * @param array $csv_data
     * @param array $data
     * @param string $merchant_txn_id
     * @param int $user_id
     * @return array : array $response
     * @throws Exception
     * @author Nishu, May 2019
     */
    public function updateBankReceipt(array $csv_data, array &$data, string $merchant_txn_id, int $user_id = 0): array{
        $response = array();
        $response['error_msg'] = '';
        $response['status']    = 1;

        //Data arrays based on bank receipt response statuses
        // Initializing status wise containers
        $nach_data_0_status = array();
        $nach_data_1_status = array();
        $nach_data_2_status = array();

        //Data Empty checks
        if(empty($csv_data)){
            $response['status']    = 0;
            $response['error_msg'] = 'No data passed to NACH Behaviour. Something went wrong. Please contact Administrator!';

        }else{
            //Loop over csv data
            foreach ($csv_data as $key => $value) {
                $umrn_lan     = trim($value[0] ?? '');
                $date        = date('Y-m-d', strtotime(trim($value[1] ?? ''))); // Flexiblity for any format in the csv, parseable by strtotime
                $amount      = round((float)($value[2] ?? 0.0), 2);
                $status      = $value[3] ?? -1;
                
                if ( empty($umrn_lan) || empty($date) || !validateDate($date, 'Y-m-d') || $amount <= 0.0 || 
                    ($status != 0 && $status != 1 & $status != 1) ) {
                    $response['status'] = 0;
                    $response['error_msg'] .= "Row-". ($key + 1) . ": Invalid data in the row. Please check properly! <br>";
                    
                    // Move to next UMRN no, date
                    continue;
                }


                $filter_data = array();
                $filter_data['filter_status']               = array('NOT_DONE', 'BANK_PENDING');
                $filter_data['filter_order_payment_id']     = 'NULL';
                $filter_data['filter_deffered_by_customer'] = 0;
                $filter_data['filter_umrn_lan']             = $umrn_lan;
                $filter_data['filter_date']                 = $date;

                //Get Schedule Amount, by customer's umrn_no ad given date in csv to validate amount
                $nach_data = $this->nach->getNachSchedulesForBankReceipt($filter_data);

                //Sum amount for nach schedules based on filtering of UMRN no. and date
                $nach_amount = array_sum(array_column($nach_data, 'nach_debit_amount'));

                if(empty($nach_data) || abs($amount - $nach_amount) > 0.01){
                    $response['status'] = 0;
                    $response['error_msg'] .= "Row-". ($key + 1) . ": Entered Amount (".$amount.") is different from NACH schedule uploaded Amount = ". $nach_amount ." for UMRN/LAN No: ". $umrn_lan. "<br>";
                    
                    // Move to next UMRN no, date
                    continue;
                }

                //Loop over NACH data received from umrn_no and date bases
                foreach ($nach_data as $nach_row) {
                    // suborderwise row in breakup
                    ${'nach_data_' . $status . '_status'}[$umrn_lan.'_'.$date]['orders'][$nach_row['order_id']]['breakup'][] = $nach_row;

                    // update total amount per order
                    $current_amount = ${'nach_data_' . $status . '_status'}[$umrn_lan.'_'.$date]['orders'][$nach_row['order_id']]['amount'] ?? 0.00;
                    ${'nach_data_' . $status . '_status'}[$umrn_lan.'_'.$date]['orders'][$nach_row['order_id']]['amount'] = $current_amount + $nach_row['nach_debit_amount'];

                    // status, umrn_no, date, order_no
                    ${'nach_data_' . $status . '_status'}[$umrn_lan.'_'.$date]['orders'][$nach_row['order_id']]['status'] = $status;
                    ${'nach_data_' . $status . '_status'}[$umrn_lan.'_'.$date]['orders'][$nach_row['order_id']]['umrn_no'] = $umrn_lan;
                    ${'nach_data_' . $status . '_status'}[$umrn_lan.'_'.$date]['orders'][$nach_row['order_id']]['date'] = $date;
                    ${'nach_data_' . $status . '_status'}[$umrn_lan.'_'.$date]['orders'][$nach_row['order_id']]['order_no'] = $nach_row['order_no'];
                    ${'nach_data_' . $status . '_status'}[$umrn_lan.'_'.$date]['orders'][$nach_row['order_id']]['order_id'] = $nach_row['order_id'];
                    ${'nach_data_' . $status . '_status'}[$umrn_lan.'_'.$date]['orders'][$nach_row['order_id']]['customer_id'] = $nach_row['customer_id'];
                }
            }

            //Means no validation error
            if($response['status'] != 0){

                //Action for bank receipt response for order_payment and schedules
                //-----For Bank Pending response
                $nach_data_0_status = $this->updateSchedulesAndPaymentDueToBankReceipt($nach_data_0_status, $merchant_txn_id, $user_id);
                
                //-----For Bank Success response
                $nach_data_1_status = $this->updateSchedulesAndPaymentDueToBankReceipt($nach_data_1_status, $merchant_txn_id, $user_id);
                
                //-----For Bank Failure response
                $nach_data_2_status = $this->updateSchedulesAndPaymentDueToBankReceipt($nach_data_2_status, $merchant_txn_id, $user_id);

                $data = array_merge($nach_data_0_status, $nach_data_1_status, $nach_data_2_status);
            }
        }
        return $response;
    }

    /*
     * @info: Public method to get Unverified NACH schedules, With following filter(s)
     *          filter_date_from: no value
     *          filter_date_to : current date - 3 days
     *          order payment id is null
     *          deffered_by_customer = 0
     *          status = NOT_DONE, BANK_PENDING
     * @author: Nishu, June 2019
     */
    public function getUnverifiedSchedule() : array {
        //Set Filter data
        $filter_data = array();

        $filter_data['filter_nach_debit_date_to']   = date('Y-m-d', strtotime('- 3 days'));
        $filter_data['filter_order_payment_id']     = 'NULL';
        $filter_data['filter_deffered_by_customer'] = 0;
        $filter_data['filter_status']               = array('NOT_DONE','BANK_PENDING');

        //Set selector data
        $select_data = array(
                         'nach_schedule_id',
                         'suborder_id',
                         'nach_debit_date',
                         'nach_debit_amount',
                         'status',
                         'deffered_by_customer',
                         'customer_nach_details_id',
                         'date_added',
                         'date_modified'
                       );
                       
        return $this->nach->getNachSchedules($filter_data, $select_data, true);
    }

    /**
     * Private method to get last NACH schedule date for given suborder_id
     * @param string $suborder_id
     * @return string $date - Last date
     * @throws Exception
     * @author: Nishu, May 2019
     */
    public function getLastScheduleDateOfSuborder(string $suborder_id) : string {
        $date = '';
        if(!empty($suborder_id)){
            $filter_data = array();
            $filter_data['filter_suborder_id'] = $suborder_id;
            $filter_data['sort']               = array(
                                                   array('field'=> 'nach_debit_date', 'order'=>'DESC')
                                                 );
            $filter_data['limit']              = 1;
            $select_data = array(
                             'nach_debit_date'
                           );
            $nach_schedule_details = $this->nach->getNachSchedules($filter_data, $select_data);
            if(!empty($nach_schedule_details)){
                $date = $nach_schedule_details[0]['nach_debit_date'] ?? '';
            }
        }
        return $date;
    }
    
    
        ////////////////////////// PRIVATE /////////////////////////////////

    /*
     * Private function to prepare NACH schedule(s) data for a suborder.
     * This function is generally used when generating fresh NACH schedules for a suborder.
     * It takes the $suborder_bal to recover; divide it by schedule_days_for_nach; get cron dates, 
     * and return an array with mapping of nach_debit_date(s) with nach_debit_amount
     * 
     * @note : If per day debit amount comes to be less than MIN_SUBORDER_LEVEL_TRANSACTION_LIMIT constant, 
     *         then only one day is considered to debit the complete balance.
     * 
     * @note : Return array can be empty as well, in case there is no positive balance (>= 0.01) to recover.
     * 
     * @param : array $suborder - May contain keys like: 'schedule_days_for_nach', 'nach_schedule_crontab', 
     *          'delivered_date', 'schedule_days_for_nach', and 
     *          'suborder_id' (will be required when Schedule is being created else @throws Exception)
     * @param : float $suborder_bal - Should be positive to get mapping array
     * 
     * @return: array of mapping of nach_debit_date(s) with nach_debit_amount
     * 
     * @author: Nishu, May 2019 
     */
    private function prepareNachSchedules(array $suborder, float $suborder_bal) : array {
        $add_schedule_data = array();

        // rounding suborder_bal to two decimal places
        $suborder_bal = ROUND($suborder_bal, 2);
        
        if ($suborder_bal < 0.01) return $add_schedule_data;

        $suborder['schedule_days_for_nach'] = (int)($suborder['schedule_days_for_nach'] ?? self::DEFAULT_SCHEDULE_DAYS_FOR_NACH);

        // determine tentative debit amount (balance / schedule days)
        $tentative_debit_amount = max(ROUND($suborder_bal / $suborder['schedule_days_for_nach'], 2), 
                                      self::MIN_SUBORDER_LEVEL_TRANSACTION_LIMIT);

        //Get next Coming working cron date to add NACH schedules
        $date_arr = $this->getNextComingCronDates($suborder);

        //$date_arr- LOOP dates array for which NACH schedule 
        foreach ($date_arr as $key => $date) {
            
            // Suborder_id must be available
            if ( empty($suborder['suborder_id']) ) {
                throw new Exception('Suborder_id not provided in NachBehaviour::prepareNachSchedules method!');
            }

            //Prepare data for adding schedule in DB
            $schedule_data = array();
            $schedule_data['suborder_id']        = $suborder['suborder_id'];
            $schedule_data['nach_debit_date']    = $date;
            $schedule_data['nach_debit_amount']  = min($tentative_debit_amount, $suborder_bal);

            //Assign schedule data array, to store multiple schedules into single array 
            $add_schedule_data[] = $schedule_data;
            
            // Adjust suborder_bal for schedules already prepared
            $suborder_bal -= $schedule_data['nach_debit_amount'];
            
            // Break and return if balance goes below 0.01
            if ($suborder_bal < 0.01) return $add_schedule_data;
        }
        
        // If there is any suborder_bal still left, we add it to the first schedule
        if ( $suborder_bal >= 0.01 && isset($add_schedule_data[0]['nach_debit_amount']) ) {
            $add_schedule_data[0]['nach_debit_amount'] += $suborder_bal;
        }            

        //Return multiple shcedule details to be added
        return $add_schedule_data;
    }

    /**
     * @info Private method to add mandatory filter(s) to $filter_data, to fetch Editable NACH Schedule only
     * 
     * Currently, Mandatory filter conditions are: 
     *  - filter_deffered_by_customer = 0
     *  - filter_status = 'NOT_DONE'
     *  - filter_order_payment_id = NULL
     *  - filter_nach_debit_date_from >= CURRENT_DATE()
     * 
     * @param array $filter_data
     * @return array - Modified $filter_data array with mandatory conditions
     * @author Nishu, May 2019
     */
    private function applyMandatoryFiltersForEditableNachSchedule(array $filter_data = array()) : array {
        $filter_data['filter_deffered_by_customer'] = 0;
        $filter_data['filter_status']               = array('NOT_DONE');
        $filter_data['filter_order_payment_id']     = 'NULL';
        $ts_min_editable = strtotime($this->checkIfCurrentTimeIsGoodToEdit() ? 'today' : 'tomorrow');
        $fd_date_from = trim($filter_data['filter_nach_debit_date_from'] ?? '');
        $ts_date_from = (validateDate($fd_date_from, 'Y-m-d') ? (int)strtotime($fd_date_from) : 0);
        $filter_data['filter_nach_debit_date_from'] = date('Y-m-d', max($ts_min_editable, $ts_date_from));

        return $filter_data;
    }

    /**
     * Private method to check whether Current System time is within the 
     * DAILY_NACH_SCHEDULE_EDIT_HOUR_LIMIT and DAILY_NACH_SCHEDULE_EDIT_MINUTE_LIMIT
     * so that the NACH schedule is editable.
     * 
     * @return Boolean. True if editable (within time limit); else False
     */
    private function checkIfCurrentTimeIsGoodToEdit() : bool {
        return ( (int)date('H') < (int)(self::DAILY_NACH_SCHEDULE_EDIT_HOUR_LIMIT) || 
                 ( (int)date('H') === (int)(self::DAILY_NACH_SCHEDULE_EDIT_HOUR_LIMIT) && 
                   (int)date('i') <= (int)(self::DAILY_NACH_SCHEDULE_EDIT_MINUTE_LIMIT) ) 
               );
    }

    /**
     * Private method which is internally called, to check given schedule is editable or not
     * @param int nach_schedule_id
     * @return array Nach schedule details if given $nach_schedule_id is editable
     *               It will contain following keys: nach_schedule_id, suborder_id,
     *               nach_debit_date, nach_debit_amount, status, deffered_by_customer
     *
     * If this schedule is not editable OR does not exist, it returns an empty array.
     * @throws Exception
     */
    private function checkAndGetIfNachScheduleIsEditable(int $nach_schedule_id) : array {

        $filter_data = array();
        $filter_data['filter_nach_schedule_id'] = array($nach_schedule_id);
        $filter_data['limit'] = 1;
        $filter_data = $this->applyMandatoryFiltersForEditableNachSchedule($filter_data);
        $select_data = array('nach_schedule_id', 'suborder_id', 'nach_debit_date', 'nach_debit_amount', 'status', 'deffered_by_customer');
        $nach_schedule = $this->nach->getNachSchedules($filter_data, $select_data);
        return ($nach_schedule[0] ?? array());
    }

    /**
     * Private method to redistribute a NEGATIVE Difference amount into Given Schedules (Split the amount and update the Schedules)
     * @note It is assumed that the schedules provided to this method are EDITABLE; else Erratic data updates can happen.
     * @note This method works only for Negative Difference Amount, else returns the Amount back.
     *
     * Logic: - Average difference amount is distributed in the remaining schedule(s).
     *        - All the balance difference is adjusted in the last schedule, irrespective of average amount.
     *        - If there is still some amount ($difference_amount) pending, that pending value is returned.
     *
     * @param array $remaining_schedule_details - array - Array of array of remaining NACH Schedule(s)
     *         Required keys are: 'nach_schedule_id' and 'nach_debit_amount'
     * @warning Undefined and Erratic behaviour would happen if required keys are invalid/not provided.
     * @param float $difference_amount - difference amount to be redistributed into remaining schedules.
     * @param string $remark - Comment/remark on why redistribution is being done.
     *
     * @return float Pending Negative difference amount
     * @throws Exception
     */
    private function reDistributeNegativeDifferenceAmount(array $remaining_schedule_details, float $difference_amount, string $remark) : float {

        if (empty($remaining_schedule_details) || $difference_amount >= 0.0) return $difference_amount;

        $i = 0; // Counter to identify last schedule
        $schedule_count = count($remaining_schedule_details);

        // Average difference is computed using "ROUND UP/DOWN". eg: 11.363 => 11.37, -11.363 => -11.37
        // This ensures that no difference is generally left at the end due to truncation errors
        $avg_difference = $difference_amount / $schedule_count;
        $avg_difference = round(($avg_difference < 0 ? floor($avg_difference*100) : ceil($avg_difference*100))/100, 2);

        // Loop over schedules
        foreach ($remaining_schedule_details as $key => $schedule_details) {
            $i++;
            if ($difference_amount === 0.00) return $difference_amount;

            $nach_schedule_id = (int)$schedule_details['nach_schedule_id'];
            $old_amount = round((float)$schedule_details['nach_debit_amount'], 2);

            // Determine new amount for the NACH schedule.
            // If last schedule then adjust all the difference amount in this schedule irrespective of avg_difference value
            if ( $i === $schedule_count ) {
                $schedule_details['new_amount'] = round(max($old_amount + $difference_amount, 0), 2);
            } else {
                $schedule_details['new_amount'] = round(max($old_amount + $avg_difference, 0), 2);
            }

            // Adjust the overall difference amount
            $difference_amount -= round($schedule_details['new_amount'] - $old_amount, 2);

            // Change amount in given schedule_id, using edit method (with logging)
            $edit_schedule = array();
            $edit_schedule['nach_debit_amount'] = $schedule_details['new_amount'];
            $this->nach->editNachSchedule($nach_schedule_id, $edit_schedule, $remark);
        }

        return $difference_amount;
    }

    /**
     * @info Private method to apply checksum at Customer level. (It is assumed that data is grouped at customer level already).
     *        It checks if total amount of all schedules of customer wise is less than daily minimum transaction amount.
     *        If yes, get Next coming schedules for the customer. If available, shift them to the Next coming schedule date.
     *
     * @note Mandatory keys in the Internal level array(s): customer_id, nach_schedule_id, nach_debit_amount.
     * This method does not throw exception in case of the above-mentioned keys missing. But the behaviour would be undefined/buggy.
     *
     * @warning It is preferable to apply checksum only on schedules of the Current date, to avoid unncessary shifting.
     *           Because, there is a chance that more schedules for the customer can be created on future date(s), which
     *           can bypass the checksum condition.
     *
     * @param array
     * @return array
     * @throws Exception
     * @author Nishu, May 2019
     */
    private function checksumAtCustomerLevel(array $result) : array {
        
        if( !empty($result) ){
            
            //Set result data, by setting nach_schedule_id as array key
            $result = array_combine(array_column($result, 'nach_schedule_id'), $result);

            $customer_wise_amount   = array();
            $customer_wise_schedule = array();

            //Loop over data to sum of amount at customer level
            foreach ($result as $key => $value) {
                //Get customer_id
                $customer_id = (int)($value['customer_id'] ?? 0);
                
                //check isset value or not
                if(!isset($customer_wise_amount[$customer_id])) {
                    $customer_wise_amount[$customer_id] = 0;
                    $customer_wise_schedule[$customer_id] = array();
                }
                //Amount sum at customer level
                $customer_wise_amount[$customer_id] += round((float)($value['nach_debit_amount'] ?? 0.00), 2);

                //Customer wise schedule grouping
                $customer_wise_schedule[$customer_id][] = $value;
            }

            //Loop over customer level data-- outer loop
            foreach ($customer_wise_amount as $customer_id => $customer_amount) {

                //check for MIN_CUSTOMER_LEVEL_TRANSACTION_LIMIT
                if($customer_amount < self::MIN_CUSTOMER_LEVEL_TRANSACTION_LIMIT){

                    // Get next coming schedule date for this customer (if existing)
                    $filter_data = array();
                    $filter_data['filter_nach_debit_date_from'] = date('Y-m-d', strtotime('tomorrow'));
                    $filter_data['filter_customer_id']          = (int)$customer_id;
                    $filter_data = $this->applyMandatoryFiltersForEditableNachSchedule($filter_data);
                    $filter_data['sort']    = array(array('field'=> 'nach_debit_date', 'order'=>'ASC'));
                    $filter_data['limit']  = 1;
                    $select_data = array( 'nach_schedule_id','nach_debit_date' );
                    $next_coming_schedule = $this->nach->getNachSchedules($filter_data, $select_data);

                    // If next schedule date exists, we shift all the schedules of this customer to the next date
                    if( !empty($next_coming_schedule[0]['nach_debit_date']) ) {

                        $next_coming_schedule_date = $next_coming_schedule[0]['nach_debit_date'];
                        $comment = 'Shifted to ' . $next_coming_schedule_date . ' due to Low Overall Daily Debit amount ' . 
                       '(< ' . self::MIN_CUSTOMER_LEVEL_TRANSACTION_LIMIT . ') for the customer';

                        // Loop over customer wise schedules
                        foreach ($customer_wise_schedule[$customer_id] as $key => $schedule) {
                            $nach_schedule_id = (int)$schedule['nach_schedule_id'];

                            // Check if the schedule is actually editable !!
                            $details = $this->checkAndGetIfNachScheduleIsEditable($nach_schedule_id);
                            if ( !empty($details) ) { // Editable; so shift it.
                                $this->shiftNachSchedule($nach_schedule_id, $next_coming_schedule_date, $comment);
                            }
                            
                            //unset key nach schedule id, as it is unusable now (whether shifted or not)
                            unset($result[$nach_schedule_id]);
                        }
                    }
                }
                 
            }//End of --- outer loop
        }
        return $result;
    }

    /**
     * @info Private method to filter result data (NACH schedules) by checking suborder balance
     *        That checksum is for order level minimum transaction value
     *        This applied only in current date schedules
     *        -->calculate order balance to recover from customer
     *           --> if pending balance is greater then 0 and
     *           --> if pending balance is too low
     *           --> then schedule debit amount is changed to 0 and status as 'DISABLED'
     * @param array
     * @return  array
     * @throws Exception
     * @author Nishu, March 2019
     */
    private function checksumAtOrderLevel($result) {
        $data = array();

        if(!empty($result)) {
            
            $group_data       = array();
            $orderwise_amount = array();

            foreach ($result as $key => $value) {
                $order_id          = (int)($value['order_id'] ?? 0);
                $suborder_id       = ($value['suborder_id'] ?? '');

                if (empty($order_id) || empty($suborder_id)) 
                    throw new Exception('Invalid/empty order_id or suborder_id!');

                if ( !empty($group_data[$order_id][$suborder_id]) ) 
                    throw new Exception('Duplicate suborder_id found: ' . $suborder_id);

                $group_data[$order_id][$suborder_id] = $value;

                if(!isset($orderwise_amount[$order_id])){
                    $orderwise_amount[$order_id] = 0.00;
                }
                $orderwise_amount[$order_id] += round(($value['nach_debit_amount'] ?? 0.00),2);
            }

            if(!empty($group_data)){
                foreach ($group_data as $order_id => $orderwise) {

                    $order_balance = OrderInfo::getOrderBalanceAmount($this->db, $order_id);
                    $rem_bal       = round(abs($order_balance),2); // temp variable to store adjusted balance
                    $order_max_debit_amount  = $orderwise_amount[$order_id] ?? 0.00;

                    foreach ($orderwise as $suborder_id => $suborder_old) {

                        $suborder_new = $suborder_old;
                        $nach_schedule_id = (int)$suborder_new['nach_schedule_id'];
                        // for reports - settng this key - nothing to do with behaviour logics
                        $suborder_new['order_bal']             = round($order_balance, 2);

                        // Check if the schedule is actually editable !!
                        $details = $this->checkAndGetIfNachScheduleIsEditable($nach_schedule_id);
                        if ( empty($details) ) { // Not editable schedule
                            $data[] = $suborder_new;
                            continue;
                        }

                        $edit_schedule = array();
                        $remark = "Schedule is auto-edited during checksum at Order level. Low/Zero Order balance debit(s) are modified/disabled automatically.";

                        // If no order balance, simply disable all
                        if ( $order_balance >= 0.00 ) {

                            // Nothing to recover - change debit amount to 0 and status Disabled

                            $edit_schedule['nach_debit_amount'] = 0.00;
                            $edit_schedule['status']            = 'DISABLED';

                        } elseif ( $order_max_debit_amount <= $order_balance ) { 
                            // nothing to edit, as there is enough balance
                            $data[] = $suborder_new;
                            continue;

                        } else {
                            // We need to adjust debit amount for suborders as balance is low

                            // New debit amount for this suborder canot go above remainig order balance
                            $suborder_new['nach_debit_amount']     = max(min($rem_bal, $suborder_old['nach_debit_amount']),0.00);
                            // adjust rem_bal
                            $rem_bal -= $suborder_new['nach_debit_amount'];

                            // if the new debit amount > 0 then it is good for display and sheet
                            if ($suborder_new['nach_debit_amount'] > 0.00) {
                                $data[] = $suborder_new;
                            }

                            // Prepare edit schedule array
                            if($suborder_new['nach_debit_amount'] != $suborder_old['nach_debit_amount']){
                                $edit_schedule['nach_debit_amount'] = $suborder_new['nach_debit_amount'];
                                if ( $suborder_new['nach_debit_amount'] == 0.00 ) {
                                    $edit_schedule['status']            = 'DISABLED';
                                }
                            }
                        }

                        // If edit to do
                        if ( !empty($edit_schedule) ) {
                            $this->nach->editNachSchedule($nach_schedule_id, $edit_schedule, $remark);
                        }
                    }
                }
            }
        }
        
        return $data;
    }

    /**
     * @info: Private function to insert into oc_order_payment
     *         - to inset into oc_order_payment
     *         - when bank response is success then add oc_order_order_payment with successfull =1
     *         - and when bank response is failure then add oc_order_order_payment with successfull =0
     * @param array $order_data
     * @param string $merchant_txn_id
     * @param int $user_id
     * @return int
     * @author: Nishu, May 2019
     */
    private function updateIntoOrderPayment(array $order_data, string $merchant_txn_id = '', int $user_id = 0) : int {

        if(!empty($order_data)){
            
            // Only success and failure to be updated in order_payment table. Pending to be ignored for order_payment table
            if($order_data['status'] == 1 || $order_data['status'] == 0){
                
                //To insert data in order payment table
                $order_payment_data  = array();

                $order_payment_data['order_id']          = $order_data['order_id'];
                $order_payment_data['order_no']          = $order_data['order_no'];
                $order_payment_data['merchant_txn_id']   = $merchant_txn_id;
                $order_payment_data['payment_mode']      = 'NACH-WholesaleBox Credit';
                $order_payment_data['amount']            = $order_data['amount'];
                $order_payment_data['txn_date_time']     = date('Y-m-d', strtotime($order_data['date']) );
                $order_payment_data['date_added']        = date('Y-m-d H:i:s');
                $order_payment_data['payment_gateway']   = 'wsb_credit_nach';
                $order_payment_data['user_id']           = $user_id;
                
                if ( $order_data['status'] == 1 ) { // status = 0
                    $order_payment_data['successfull']       = 1;
                    $order_payment_data['txn_status']        = 'SUCCESS';
                    $order_payment_data['reference']         = 'Successful Auto NACH Debit';
                } else { // status = 0
                    $order_payment_data['successfull']       = 0;
                    $order_payment_data['txn_status']        = 'FAILED';
                    $order_payment_data['reference']         = 'Failed Auto NACH Debit';
                }
                
                //insert into oc_order_payment
                return (int)OrderPayment::insertOrderPayment($this->db, $order_payment_data);
            }
        }

        return 0;
    }

    /**
     * @info Private method to add enteries into oc_order_payment according to bank response status
     *        - Redistriute amount to next coming schedules for bank failure status
     *        - and also edit NACH schedule(s) status according bank response
     * @param array $data
     * @param string $merchant_txn_id , refrence for oc_receipt
     * @param int $user_id
     * @return array
     * @throws Exception
     * @author Nishu, May 2019
     */
    private function updateSchedulesAndPaymentDueToBankReceipt(array $data, string $merchant_txn_id = '', int $user_id = 0) : array {

        //Loop data
        foreach ($data as $key => $value) {
            //Loop order wise
            foreach ($value['orders'] as $order_id => $order_data) {
                $order_payment_id = $this->updateIntoOrderPayment($order_data, $merchant_txn_id, $user_id);
                $data[$key]['orders'][$order_id]['order_payment_id'] = $order_payment_id;

                //Loop suborder_id/nach_schedule level
                foreach ($order_data['breakup'] as $nach_schedule) {
                    $nach_schedule_id = (int)$nach_schedule['nach_schedule_id'];

                    if($order_data['status'] == 1){
                        $new_status = 'BANK_SUCCESS'; 
                    }elseif($order_data['status'] == 0){
                        $new_status = 'BANK_FAILURE';

                        //Redistibute this amount to next coming schedules
                        $comment = "Redistributed NACH Schedule amount to next schedules, because of BANK_FAILURE response uploaded.";
                        $this->distributeAmountInRemainingSchedules($nach_schedule['suborder_id'],
                                                          'today',
                                                                    (float)$nach_schedule['nach_debit_amount'],
                                                                    $comment,
                                                                    array($nach_schedule_id),
                                                    false);

                    } else {
                        $new_status = 'BANK_PENDING';
                    }

                    //Change status in given schedule_id, using edit method
                    $edit_schedule = array();
                    $edit_schedule['status'] = $new_status;
                    if(!empty($order_payment_id)){
                        $edit_schedule['order_payment_id'] = $order_payment_id;
                    }

                    $remark = "NACH Schedule status is updated due to Bank response sheet uploaded.";
                    
                    //Edit schedule with logging
                    $this->nach->editNachSchedule($nach_schedule_id, $edit_schedule, $remark);
                }
            }
        }
        return $data;
    }
    
    /*
     * Private method to validate input $new_date and $comment for the shift Nach Schedule methods.
     * Validation rules: $new_date cannot be less than Tomorrow System date.
     *                   $new_date cannot be a Bank holiday date.
     *                   Trimmed $comment cannot be empty.
     * 
     * @param $new_date - String - passed by reference
     * @param $comment - String - passed by reference
     * 
     * @return $error_summary - Array of error messages (if any, else empty array)
     * 
     * @note This method does not validate if the NACH schedule itself is editable or not.
     * @author Madhur, 2019
     */
    private function validateInputsForShiftSchedule(string &$new_date, string &$comment) : array {
        
        // Return error summary initialization
        $error_summary = array();
        
        // Comment Validation
        $comment = trim($comment);
        if ( empty($comment) ) {
            $error_summary[] = 'Proper Comment (non empty) is required to shift NACH Schedule(s) !!';
        } 
        
        $new_date = trim($new_date);
        if ( !empty($new_date) ) {
            
            // Check for valid yyyy-mm-dd		
            if ( !validateDate($new_date, 'Y-m-d') ) {
                $error_summary[] = 'Given date: ' .$new_date. ' is not a valid date in YYYY-MM-DD format!';
                return $error_summary;
                
            } else {
                // Check Given new_date must be more than the Current System Date
                if ( strtotime($new_date) < strtotime('today') ) {
                    $error_summary[] = 'Shifting cannot be done to given date: ' . $new_date . '; Old date is not allowed!';
                } elseif ( strtotime($new_date) === strtotime('today')
                           && $this->checkIfCurrentTimeIsGoodToEdit() === false ) {
                    // Check if the Given new_date is Today's date, but the time is not good to edit anymore
                    $error_summary[] = 'Shifting cannot be done to today: ' . $new_date . ', as the time-limit to edit is over!';
                } else { // Check Given new_date must not be bank holiday
                    $bank_holiday = BankHolidayAction::getInstance($this->registry);
                    if ($bank_holiday->checkIfDateIsBankHoliday($new_date)) {
                        $error_summary[] = 'Given date: ' . $new_date . ' is a Bank Holiday; NACH schedule cannot be shifted to this date!';
                    }
                }
            }
        }
        
        return $error_summary;
    }

    /**
     * Method to get EDITABLE Schedule(s) within a given Start Date (inclusive) and End Date (inclusive)
     * @param string $date_from Start Date
     * @param string $date_to End Date
     * @param array $select_data array of fields to select. Optional; defaults to nach_schedule_id only.
     * @param array $skip_nach_schedule_id Optional if some schedule id(s) are to be skipped
     * @return array of NACH schedules
     * @throws Exception
     * @author Madhur, 2019
     */
    private function getEditableNachSchedulesInDateRange(string $date_from,
                                                         string $date_to,
                                                         array  $select_data = array('nach_schedule_id'),
                                                         array  $skip_nach_schedule_id = array()) : array {
        $filter_data = array();
        $filter_data['filter_nach_debit_date_from']  = date('Y-m-d', strtotime($date_from));
        $filter_data['filter_nach_debit_date_to']  = date('Y-m-d', strtotime($date_to));
        $filter_data['filter_skip_nach_schedule_id'] = $skip_nach_schedule_id;
        $filter_data = $this->applyMandatoryFiltersForEditableNachSchedule($filter_data);
        return $this->nach->getNachSchedules($filter_data, $select_data);

    }

    /**
     * Method to get EDITABLE Schedule(s) for a given Suborder, from (inclusive) a given Date.
     *
     * Schedules are in an ascending order of nach_debit_date.
     *
     * @param string $suborder_id Suborder Id
     * @param string $date_from Date to start from (Inclusive)
     * @param array $select_data array of fields to select. Optional; defaults to nach_schedule_id only.
     * @param array $skip_nach_schedule_id Optional if some schedule id(s) are to be skipped
     * @return array of NACH schedules
     * @throws Exception
     * @author Madhur, 2019
     */
    private function getEditableNachSchedulesOfSuborder(string $suborder_id,
                                                        string $date_from,
                                                        array  $select_data = array('nach_schedule_id'),
                                                        array  $skip_nach_schedule_id = array()) : array {
        $filter_data = array();
        $filter_data['filter_suborder_id']           = $suborder_id;
        $filter_data['filter_nach_debit_date_from']  = date('Y-m-d', strtotime($date_from));
        $filter_data['filter_skip_nach_schedule_id'] = $skip_nach_schedule_id;
        $filter_data['sort'] = array(array('field' => 'nach_debit_date', 'order' => 'ASC'));
        $filter_data = $this->applyMandatoryFiltersForEditableNachSchedule($filter_data);
        return $this->nach->getNachSchedules($filter_data, $select_data);
    }

    /**
     * Method to check if a NACH Schedule exists (whether editatble or not),
     * for a given Suborder on a given Date.
     * @param string $suborder_id If suborder_id is empty, then it returns false.
     * @param string $date
     * @return bool
     * @throws Exception
     * @author Madhur, 2019
     */
    private function checkIfNachScheduleExists(string $suborder_id, string $date) : bool {
        if (empty($suborder_id))
            return false;

        $filter_data = array();
        $filter_data['filter_nach_debit_date_from'] = $date;
        $filter_data['filter_nach_debit_date_to']   = $date;
        $filter_data['filter_suborder_id']          = $suborder_id;
        $filter_data['limit']                       = 1;
        $select_data = array('nach_schedule_id');

        return !empty($this->nach->getNachSchedules($filter_data, $select_data));
    }
    
    
    // Overall minimum debit amount limit for a customer on a specific day
    private const MIN_CUSTOMER_LEVEL_TRANSACTION_LIMIT = 200.00;
    
    // Minimum debit amount limit of a suborder during Schedule generation for a day
    private const MIN_SUBORDER_LEVEL_TRANSACTION_LIMIT = 10.00;
    
    // NACH Schedules of Current date cannot be edited if System time > 11:20 am
    private const DAILY_NACH_SCHEDULE_EDIT_HOUR_LIMIT = 11;
    private const DAILY_NACH_SCHEDULE_EDIT_MINUTE_LIMIT = 20;
    
    // NACH Schedule of Current date cannot be generated if System time > 7 am
    private const CURRENT_DATE_NACH_SCHEDULE_GENERATION_HOUR_LIMIT = 7;
    
    // Default NACH generation related cron rules etc
    private const DEFAULT_SCHEDULE_DAYS_FOR_NACH = 20;
    private const DEFAULT_DAYS_BEFORE_NACH_START = 4;
    private const DEFAULT_NACH_SCHEDULE_CRONTAB = '30 11 * * *';
    
    // Objects
    private $registry = null;
    private $db = null;
    /**
     * @var NachAction
     */
    private $nach;

}