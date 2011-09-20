<?php
//echo "<pre>";
//print_r(time());
//exit();
?>
<div class="col-sm-4 col-sm-offset-4 chat-body mt">
    <div class="panel panel-custom mb0">
        <div class="panel-heading">
            <div class="">
                <div class="pull-right chat-icon">
                    <i data-toggle="tooltip" data-placement="top" title="<?= lang('add_more_to_chat') ?>"
                       class="fa fa-plus" aria-hidden="true"></i>
                    <a data-toggle="tooltip" data-placement="top" title="<?= lang('settings') ?>"
                       href="<?= base_url('admin/get_chat_settings/') ?>"> <i class="fa fa-cog" aria-hidden="true"></i></a>
                    <i data-toggle="tooltip" data-placement="top" title="<?= lang('close') ?>" class="fa fa-times"
                       aria-hidden="true"></i>
                </div>
            </div>
        </div>
        <div class="">
            <ul>
                <li>
                    <div class="message chat-message">
                        <div class="avatar"><img class="img-circle" style="width:100%;"
                                                 src="https://lh6.googleusercontent.com/-lr2nyjhhjXw/AAAAAAAAAAI/AAAAAAAARmE/MdtfUmC0M4s/photo.jpg?sz=48">
                        </div>
                        <div class="text text-l"><p>Hello Tom...</p>
                            <p>
                                <small>4:17 PM</small>
                            </p>
                        </div>
                    </div>
                </li>
                <li style="width:100%;">
                    <div class="message-right chat-message">
                        <div class="text text-r"><p>Tell me a joke</p>
                            <p>
                                <small>4:17 PM</small>
                            </p>
                        </div>
                        <div class="avatar"><img class="img-circle" style="width:100%;"
                                                 src="https://lh6.googleusercontent.com/-lr2nyjhhjXw/AAAAAAAAAAI/AAAAAAAARmE/MdtfUmC0M4s/photo.jpg?sz=48">
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="panel-footer p0">
            <input class="mytext" placeholder="Type a message then press enter">
        </div>
    </div>
</div>
<!-- end -->