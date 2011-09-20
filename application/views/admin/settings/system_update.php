<?php echo message_box('success'); ?>
<div class="row">
    <div class="col-sm-12" data-spy="scroll" data-offset="0">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <div class="panel-title"><?= lang('update'); ?></div>
            </div>
                <div class="col-md-6 text-center">
                    <div class="alert-css alert-<?php if ($latest_version > $current_version) {
                        echo 'danger';
                    } else {
                        echo 'info';
                    } ?>">
                        <h4 class="bold"><?php echo lang('your_version'); ?></h4>
                        <p class="font-medium bold"><?php echo wordwrap($current_version, 1, '.', true); ?></p>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <div class="alert-css alert-<?php if ($latest_version > $current_version) {
                        echo 'success';
                    } else if ($latest_version == $current_version) {
                        echo 'info';
                    } ?>">
                        <h4 class="bold"><?php echo lang('latest_version'); ?></h4>
                        <p class="font-medium bold"><?php echo wordwrap($latest_version, 1, '.', true); ?></p>
                        <?php echo form_hidden('latest_version', $latest_version); ?>
                    </div>
                </div>
                <div class="clearfix"></div>
                <hr/>
                <div class="col-md-12 text-center">
                    <?php if ($current_version != $latest_version && $latest_version > $current_version) { ?>
                        <div class="alert-css alert-warning">
                            Before performing an update, it is <b>strongly recommended to create a full backup</b>
                            of your current installation <b>(files and database)</b> and review the changelog.
                        </div>
                        <h3 class="bold text-center mbot20"><i class="fa fa-exclamation-circle"
                                                               aria-hidden="true"></i> <?php echo lang('update_available'); ?>
                        </h3>
                        <div class="update_app_wrapper" data-wait-text="<?php echo lang('wait_text'); ?>"
                             data-original-text="<?php echo lang('update_now'); ?>">
                            <?php if (count($update_errors) == 0) { ?>
                                <a href="#" id="update_app"
                                   class="btn btn-success"><?php echo lang('update_now'); ?></a>
                            <?php } ?>
                        </div>
                        <div id="update_messages" class="mtop25 text-left">
                        </div>
                    <?php } else { ?>
                        <h3 class="bold mbot20 text-success"><i class="fa fa-exclamation-circle"
                                                                aria-hidden="true"></i> <?php echo lang('using_latest_version'); ?>
                        </h3>
                    <?php } ?>
                    <?php if (count($update_errors) > 0) { ?>
                        <p class="text-danger">Please fix the errors listed below.</p>
                        <?php foreach ($update_errors as $error) { ?>
                            <div class="alert-css alert-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <?php if (isset($update_info->additional_data)) {
                        echo $update_info->additional_data;
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>