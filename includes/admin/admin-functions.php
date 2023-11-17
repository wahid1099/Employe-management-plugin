<?php
// ... (Previous code remains the same)
// Add admin menu
function add_employee_management_menu() {
    add_menu_page('Employee Management', 'Employee Management', 'manage_options', 'employee-management', 'admin_appointment_page');
}
add_action('admin_menu', 'add_employee_management_menu');

// Admin appointment page
function admin_appointment_page() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_appointment'])) {
        // Handle form submission
        $result = handle_appointment_submission();

        // echo '<script>';
        // if ($result) {
        //     echo 'alert("Appointment added successfully!");';
        // } else {
        //     echo 'alert("Error adding appointment. Please try again.");';
        // }
        // echo '</script>';
    }

    // Display the admin form
    display_admin_appointment_form();

    // Display existing appointments
    display_existing_appointments();
}

// Function to handle form submission
function handle_appointment_submission() {
    global $wpdb;

    // Collect form data
    $name = sanitize_text_field($_POST['name']);
    $address = sanitize_text_field($_POST['address']);
    $date = sanitize_text_field($_POST['date']);
    $employee_email = sanitize_email($_POST['employee_email']);
    $amount = sanitize_text_field($_POST['amount']);
    $status = sanitize_text_field($_POST['status']);
    $payment_status = 'pending';

    // Validate and save data to the database
    if (!empty($name) && !empty($address) && !empty($date) && !empty($employee_email) && !empty($amount) && !empty($status)) {
        $result = $wpdb->insert(
            $wpdb->prefix . 'employee_appointments',
            array(
                'name' => $name,
                'address' => $address,
                'date' => $date,
                'employee_email' => $employee_email,
                'amount' => $amount,
                'status' => $status,
                'payment_status' => $payment_status,
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );

        if ($result === false) {
            $wpdb->show_errors();
        }

        return $result !== false;
    }

    return false;
}


// Function to update appointment status
function update_appointment_status($appointment_id, $status) {
    global $wpdb;

    // Sanitize inputs
    $status = sanitize_text_field($status);

    // Update status in the database
    $wpdb->update(
        $wpdb->prefix . 'employee_appointments',
        array('status' => $status),
        array('id' => $appointment_id),
        array('%s'),
        array('%d')
    );
}


// Function to update payment status
function update_payment_status($appointment_id, $payment_status) {
    global $wpdb;

    // Sanitize inputs
    $payment_status = sanitize_text_field($payment_status);

    // Update payment status in the database
    $wpdb->update(
        $wpdb->prefix . 'employee_appointments',
        array('payment_status' => $payment_status),
        array('id' => $appointment_id),
        array('%s'),
        array('%d')
    );
}


// Function to display the admin appointment form
function display_admin_appointment_form() {
    ?>
   <div class="wrap" style="text-align: center;">
        <h1>Add New Appointment</h1>
        <form method="post" action="" style="max-width: 400px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">

            <label for="name">Name:</label>
            <input type="text" name="name" required style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <label for="address">Address:</label>
            <input type="text" name="address" required style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <label for="date">Date:</label>
            <input type="date" name="date" required style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <label for="employee_email">Employee Email:</label>
            <input type="email" name="employee_email" required style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <label for="amount">Amount:</label>
            <input type="text" name="amount" required style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <label for="status">Status:</label>
            <select name="status" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="ongoing">Ongoing</option>
            </select>

            <br>

            <input type="submit" name="submit_appointment" value="Submit" style="background-color: #0073aa; color: #ffffff; padding: 10px 20px; font-size: 16px; border: none; border-radius: 5px; cursor: pointer;">
        </form>
    </div>
    <?php
}







// Function to display existing appointments in a table
// includes/admin/admin-functions.php

function display_existing_appointments() {
    global $wpdb;

    $appointments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}employee_appointments");


    if ($appointments) {
        ?>
        <h2>Existing Appointments</h2>
        <!-- <input type="text" id="myInput" oninput="myFunction()" placeholder="Search..."> -->
        <input type="text" id="emailSearchInput" placeholder="Search by Email...">

        <table id="appointments-table" class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Date</th>
                    <th>Employee Email</th>
                    <th>Status</th>
                    <th>Payment Status</th>
                    <th>Amount</th>
                    <th>Update Status</th>
                    <th>Update Payment Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($appointments as $appointment) {
                    ?>
                    <tr>
                        <td><?php echo esc_html($appointment->name); ?></td>
                        <td><?php echo esc_html($appointment->address); ?></td>
                        <td><?php echo esc_html($appointment->date); ?></td>
                        <td><?php echo esc_html($appointment->employee_email); ?></td>
                        <td><?php echo esc_html($appointment->status); ?></td>
                        <td><?php echo esc_html($appointment->payment_status); ?></td>
                        <td><?php echo esc_html($appointment->amount); ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="appointment_id" value="<?php echo esc_attr($appointment->id); ?>">
                                <select name="status">
                                    <option value="pending" <?php selected($appointment->status, 'pending'); ?>>Pending</option>
                                    <option value="approved" <?php selected($appointment->status, 'approved'); ?>>Approved</option>
                                    <option value="ongoing" <?php selected($appointment->status, 'ongoing'); ?>>Ongoing</option>
                                </select>
                                <?php wp_nonce_field('update_status_nonce', 'update_status_nonce'); ?>
                                <input type="submit" name="update_status" value="Update" class="my-admin-button" style="background-color: #0073aa; color: #ffffff; padding: 10px 20px; font-size: 16px; border: none; border-radius: 5px; cursor: pointer;">
                            </form>
                        </td>
                        <td>
                            <?php if ($appointment->payment_status === 'approved') : ?>
                                <!-- Show "approved" text instead of the dropdown for approved status -->
                                <button type="button" class="approved-button" disabled>Approved</button>
                            <?php else : ?>
                                <form method="post" action="">
                                    <input type="hidden" name="appointment_id" value="<?php echo esc_attr($appointment->id); ?>">
                                    <select name="payment_status">
                                        <option value="pending" <?php echo ($appointment->payment_status === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="approved" <?php echo ($appointment->payment_status === 'approved') ? 'selected' : ''; ?>>Approved</option>
                                        <!-- Add more options as needed -->
                                    </select>
                                    <input type="submit" name="update_payment_status" class="my-admin-button" value="Update" style="background-color:#910707; color: #ffffff; padding: 10px 20px; font-size: 16px; border: none; border-radius: 5px; cursor: pointer; margin-top: 5px;">
                                </form>
                            <?php endif; ?>
                        </td>
                     

                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <script>
    

    document.addEventListener('DOMContentLoaded', function () {
        // Get the search input element
        var emailSearchInput = document.getElementById('emailSearchInput');

        // Add an input event listener to trigger the search
        emailSearchInput.addEventListener('input', function () {
            var filter = emailSearchInput.value.toUpperCase();

            // Get all table rows
            var rows = document.querySelectorAll('#appointments-table tbody tr');

            // Loop through rows and hide/show based on the search input
            rows.forEach(function (row) {
                var emailCell = row.cells[3].textContent.toUpperCase(); // Assuming email is in the 4th cell (index 3)
                
                if (emailCell.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>


<style>
    /* Add your CSS styles for the search input and table here */
    #myInput {
    padding: 7px;
    margin-bottom: 10px;
}
#appointments-table {
    width: 100%;
    border-collapse: collapse;
}

#appointments-table th, #appointments-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

#appointments-table th {
    background-color: #f2f2f2;
}
.approved-button {
        background-color: #4CAF50; /* Green background color */
        color: white; /* White text color */
        padding: 8px 16px; /* Padding */
        border: none; /* No border */
        border-radius: 5px; /* Rounded corners */
      
        cursor: not-allowed;
    }
</style>








        <?php
    } else {
        echo '<p>No appointments found.</p>';
    }
}

// Process status and amount update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;

    if (isset($_POST['update_status'])) {
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
        update_appointment_status($appointment_id, $status);
    }

    if (isset($_POST['update_payment_status'])) {
        $payment_status = isset($_POST['payment_status']) ? sanitize_text_field($_POST['payment_status']) : '';
        update_payment_status($appointment_id, $payment_status);
    }
}