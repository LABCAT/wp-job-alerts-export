<?php
/**
 * Admin View: Download Reports Form
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>

<div id="dashboard-widgets" class="wrap columns-2">
    <form method="post" id="mainform" action="" enctype="multipart/form-data">
        <h2>Download Job Views Report</h2>
        <p class="submit">
            <input name="wpjae_job_alerts_export_csv" class="button-primary save-button" type="submit" value="Download" />
            <?php wp_nonce_field( 'wpjae-export-csv' ); ?>
        </p>
    </form>
</div>
