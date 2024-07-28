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
            <li class="nav-item"><a class="nav-link active" href="#questions" data-toggle="tab">Questions</a></li>
            <li class="nav-item"><a class="nav-link" href="#results" data-toggle="tab">Results</a></li>
            <li class="nav-item"><a class="nav-link" href="#users" data-toggle="tab">Users</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="questions">
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
