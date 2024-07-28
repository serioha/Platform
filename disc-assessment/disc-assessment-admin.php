<?php
// Add admin menu item
function disc_assessment_admin_menu() {
    add_menu_page( 'DiSC Assessment', 'DiSC Assessment', 'manage_options', 'disc-assessment', 'disc_assessment_admin_page', 'dashicons-chart-bar', 6 );
}
add_action( 'admin_menu', 'disc_assessment_admin_menu' );

// Admin page
function disc_assessment_admin_page() {
    ?>
    <div class="wrap">
        <h1>DiSC Assessment</h1>
        <ul class="nav nav-tabs">
            <li class="nav-item"><a class="nav-link active" href="#tests" data-toggle="tab">Tests</a></li>
            <li class="nav-item"><a class="nav-link" href="#questions" data-toggle="tab">Questions</a></li>
            <li class="nav-item"><a class="nav-link" href="#results" data-toggle="tab">Results</a></li>
            <li class="nav-item"><a class="nav-link" href="#users" data-toggle="tab">Users</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tests">
                <h2>Manage Tests</h2>
                <?php
                // Import/Export button
                echo '<button id="import-test" class="button button-primary">Import Test</button>';
                echo '<button id="export-test" class="button button-primary">Export Test</button>';
                ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Test ID</th>
                            <th>Test Title</th>
                            <th>Test Description</th>
                            <th>Test Shortcode</th>
                            <th>Number of Questions</th>
                            <th>Created On</th>
                            <th>View Results</th>
                            <th>Manage Questions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Display tests table
                        $tests = get_posts( array(
                            'post_type' => 'disc_assessment_test',
                            'posts_per_page' => -1,
                        ) );
                        if ( $tests ) {
                            foreach ( $tests as $test ) {
                                $test_id = $test->ID;
                                $test_title = $test->post_title;
                                $test_description = $test->post_content;
                                $test_shortcode = get_post_meta( $test_id, 'disc_assessment_shortcode', true );
                                $test_questions = get_posts( array(
                                    'post_type' => 'disc_assessment_question',
                                    'meta_key' => 'disc_assessment_test_id',
                                    'meta_value' => $test_id,
                                    'posts_per_page' => -1,
                                ) );
                                $test_questions_count = count( $test_questions );
                                $test_created_on = get_the_date( 'Y-m-d', $test_id );
                                ?>
                                <tr>
                                    <td><?php echo $test_id; ?></td>
                                    <td><?php echo $test_title; ?></td>
                                    <td><?php echo $test_description; ?></td>
                                    <td><?php echo $test_shortcode; ?></td>
                                    <td><?php echo $test_questions_count; ?></td>
                                    <td><?php echo $test_created_on; ?></td>
                                    <td><a href="<?php echo get_permalink( $test_id ); ?>">View Results</a></td>
                                    <td><a href="<?php echo admin_url( 'post.php?post=' . $test_id . '&action=edit' ); ?>">Manage Questions</a></td>
                                    <td>
                                        <a href="#" class="edit-test" data-test-id="<?php echo $test_id; ?>">Edit</a> |
                                        <a href="#" class="delete-test" data-test-id="<?php echo $test_id; ?>">Delete</a> |
                                        <a href="#" class="export-test" data-test-id="<?php echo $test_id; ?>">Export JSON</a>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="9">No tests found.</td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane" id="questions">
                <h2>Manage Questions</h2>
                <!-- Question management form -->
            </div>
            <div class="tab-pane" id="results">
                <h2>Manage Results</h2>
                <!-- Result management form -->
            </div>
            <div class="tab-pane" id="users">
                <h2>Manage Users</h2>
                <!-- User management form -->
            </div>
        </div>
    </div>
    <?php
}

// Import/Export functions
function disc_assessment_import_test() {
    // Get JSON file content
    $json_file = file_get_contents( plugin_dir_path( __FILE__ ) . '.tmp/adapted-and-natural-styles-validated.json' );
    $json_data = json_decode( $json_file, true );

    // Create test post
    $test_post = array(
        'post_type' => 'disc_assessment_test',
        'post_title' => $json_data['title'],
        'post_content' => $json_data['description'],
        'post_status' => 'publish',
    );
    $test_id = wp_insert_post( $test_post );

    // Add shortcode
    update_post_meta( $test_id, 'disc_assessment_shortcode', $json_data['shortcode'] );

    // Create questions
    foreach ( $json_data['questions'] as $question ) {
        $question_post = array(
            'post_type' => 'disc_assessment_question',
            'post_title' => $question['question'],
            'post_content' => $question['description'],
            'post_status' => 'publish',
            'meta_input' => array(
                'disc_assessment_test_id' => $test_id,
                'disc_assessment_answer_weights' => $question['answer_weights'],
            ),
        );
        wp_insert_post( $question_post );
    }

    // Success message
    wp_send_json_success( array( 'message' => 'Test imported successfully.' ) );
}
add_action( 'wp_ajax_disc_assessment_import_test', 'disc_assessment_import_test' );

function disc_assessment_export_test( $test_id ) {
    // Get test data
    $test_title = get_the_title( $test_id );
    $test_description = get_post_field( 'post_content', $test_id );
    $test_shortcode = get_post_meta( $test_id, 'disc_assessment_shortcode', true );
    $test_questions = get_posts( array(
        'post_type' => 'disc_assessment_question',
        'meta_key' => 'disc_assessment_test_id',
        'meta_value' => $test_id,
        'posts_per_page' => -1,
    ) );
    $questions_data = array();
    foreach ( $test_questions as $question ) {
        $question_id = $question->ID;
        $question_title = $question->post_title;
        $question_description = $question->post_content;
        $answer_weights = get_post_meta( $question_id, 'disc_assessment_answer_weights', true );
        $questions_data[] = array(
            'question' => $question_title,
            'description' => $question_description,
            'answer_weights' => $answer_weights,
        );
    }

    // Create JSON data
    $json_data = array(
        'title' => $test_title,
        'description' => $test_description,
        'shortcode' => $test_shortcode,
        'questions' => $questions_data,
    );

    // Export JSON file
    $json_file = json_encode( $json_data );
    $filename = 'disc-assessment-' . $test_id . '.json';
    header( 'Content-type: application/json' );
    header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
    echo $json_file;
    exit;
}
add_action( 'wp_ajax_disc_assessment_export_test', 'disc_assessment_export_test' );

// Add scripts and styles
add_action( 'admin_enqueue_scripts', 'disc_assessment_admin_scripts' );
function disc_assessment_admin_scripts() {
    wp_enqueue_style( 'disc-assessment-admin-style', plugin_dir_url( __FILE__ ) . 'css/admin.css' );
    wp_enqueue_script( 'disc-assessment-admin-script', plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), '1.0', true );
}

// Add AJAX actions
add_action( 'wp_ajax_disc_assessment_delete_test', 'disc_assessment_delete_test' );
add_action( 'wp_ajax_disc_assessment_edit_test', 'disc_assessment_edit_test' );

// Delete test function
function disc_assessment_delete_test() {
    $test_id = $_POST['test_id'];
    wp_delete_post( $test_id );
    wp_send_json_success( array( 'message' => 'Test deleted successfully.' ) );
}

// Edit test function
function disc_assessment_edit_test() {
    $test_id = $_POST['test_id'];
    // Get test data
    $test_title = get_the_title( $test_id );
    $test_description = get_post_field( 'post_content', $test_id );
    $test_shortcode = get_post_meta( $test_id, 'disc_assessment_shortcode', true );
    $test_questions = get_posts( array(
        'post_type' => 'disc_assessment_question',
        'meta_key' => 'disc_assessment_test_id',
        'meta_value' => $test_id,
        'posts_per_page' => -1,
    ) );
    $questions_data = array();
    foreach ( $test_questions as $question ) {
        $question_id = $question->ID;
        $question_title = $question->post_title;
        $question_description = $question->post_content;
        $answer_weights = get_post_meta( $question_id, 'disc_assessment_answer_weights', true );
        $questions_data[] = array(
            'question' => $question_title,
            'description' => $question_description,
            'answer_weights' => $answer_weights,
        );
    }

    // Create JSON data
    $json_data = array(
        'title' => $test_title,
        'description' => $test_description,
        'shortcode' => $test_shortcode,
        'questions' => $questions_data,
    );

    // Send JSON data to the frontend
    wp_send_json_success( $json_data );
}
