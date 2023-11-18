<?php
// Shortcode for employee dashboard
// Function to format time interval

function employee_dashboard_shortcode() {
    ob_start(); // Start output buffering

    // Assuming the user is logged in
    $user_email = wp_get_current_user()->user_email;

    // Display employee dashboard content if user is logged in
    if ($user_email) {
        $employee_data = get_employee_data_by_email($user_email);
        $payment_data= calculate_payment_amounts($user_email);
         ?>
           <h3>Welcome, <?php echo esc_html(wp_get_current_user()->display_name); ?>!</h3>

            <div class="container">
            <h5>Payment Information</h5>
            <div class="card-container">

            <div class="card due-card">
                <h3 style="color: white;">Due</h3>
                <div class="amount"> $<?php echo esc_html($payment_data['total_payable']); ?></div>
            </div>
                <div class="card paid-card">
                <h3 style="color: white;">Paid</h3>
                <div class="amount">$<?php echo esc_html($payment_data['total_paid']); ?></div>
            
            </div>
             
       
        </div>
         <?php
        if ($employee_data) {
            ?>
            <div class="employee-dashboard">

                <h6>Appointments</h6>
                <div class="filter-container-div">
                <div class="filter-container">
                    <label for="status-filter">Filter by Status:</label>
                    <select id="status-filter">
                    <option value="">All</option>
                    <option value="pending">Pending</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="approved">Approved</option>
                    <option value="paid">Paid</option>
                        </select>
                        </div>
                        </div>
               
               


                <table id="employee-appoiment-table">
                    <thead>
                        <tr>
                            <th>Appoinment Date</th>
                            <th>Appoinment Time</th>
                           
                            <th>Client Name</th>
                            <th>Client Location</th>
                            <th>Ammount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch and display upcoming appointments for the employee
                        $upcoming_appointments =  get_all_appointments($employee_data->employee_email);

                        foreach ($upcoming_appointments as $appointment) {
                            ?>
                            <tr>
                                <td><?php echo esc_html($appointment->date); ?></td>
                                <td style="font-weight: bold;"><?php echo esc_html(format_time_interval($appointment->time_interval)); ?></td>
                                <td><?php echo esc_html($appointment->name); ?></td>
                                <td><?php echo esc_html($appointment->address); ?></td>
                                <td><?php echo esc_html($appointment->amount); ?></td>
                              
                                <td>
                                <?php
                            

                                if ($appointment->status === 'approved') {
                                    echo '<button class="approved-button">Approved</button>';
                                } elseif ($appointment->status === 'pending') {
                                    echo '<button class="pending-button">Pending</button>';    }
                                    elseif ($appointment->status === 'ongoing') {
                                        echo '<button class="pending-button" style="background-color:#021a30">Ongoing</button>';    }
                                        elseif ($appointment->status === 'paid') {
                                            echo '<button class="pending-button" style="background-color:#21b217">Paid</button>';    }
                                ?>
                            </td>

                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                 </table>

              
       </div>
    </div>
            <?php
        } else {
            echo '<p>No employee data found.</p>';
        }
    } else {
        echo '<p>Please log in to view the employee dashboard.</p>';
    }

    return ob_get_clean(); // Return the buffered content
}
add_shortcode('employee_dashboard', 'employee_dashboard_shortcode');



// Function to get employee data by email
function get_employee_data_by_email($email) {
    // Add your code to retrieve employee data from the database based on email
    global $wpdb;

    $table_name = $wpdb->prefix . 'employee_appointments';

    $employee_data = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE employee_email = %s",
            $email
        )
    );

    return $employee_data;
}




// Function to get upcoming appointments for an employee
function get_all_appointments($employee_email) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'employee_appointments';

    $all_appointments = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE employee_email = %s ORDER BY date ASC",
            $employee_email
        )
    );

    return $all_appointments;
}




// Function to calculate total payable and paid amounts for an employee
function calculate_payment_amounts($employee_email) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'employee_appointments';

    // Calculate total payable amount for pending and ongoing appointments
    $total_payable = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT SUM(amount) FROM $table_name WHERE employee_email = %s AND (status = %s OR status = %s OR status = %s)",
            $employee_email,
            'pending',
            'ongoing',
            'approved'
        )
    ) ?? 0;
    
    


    // Calculate total paid amount for approved appointments
    $total_paid = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT SUM(amount) FROM $table_name WHERE employee_email = %s AND status = %s",
            $employee_email,
            'paid'
        )
    ) ?? 0;

    return array(
        'total_payable' => $total_payable,
        'total_paid'    => $total_paid,
    );
}
