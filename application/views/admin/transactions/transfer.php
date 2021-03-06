<?= message_box('success'); ?>
<?= message_box('error'); ?>
<?php
$created = can_action('30', 'created');
$edited = can_action('30', 'edited');
$deleted = can_action('30', 'deleted');
if (!empty($created) || !empty($edited)){
?>

<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                            data-toggle="tab"><?= lang('all_transfer') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create"
                                                            data-toggle="tab"><?= lang('new_transfer') ?></a></li>
    </ul>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <?php } else { ?>
            <div class="panel panel-custom">
                <header class="panel-heading ">
                    <div class="panel-title"><strong><?= lang('all_transfer') ?></strong></div>
                </header>
                <?php } ?>
                <div class="table-responsive">
                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th><?= lang('from_account') ?></th>
                            <th><?= lang('to_account') ?></th>
                            <th class="col-currency"><?= lang('amount') ?></th>
                            <th><?= lang('date') ?></th>
                            <th><?= lang('attachment') ?></th>
                            <?php if (!empty($edited) || !empty($deleted)) { ?>
                                <th><?= lang('action') ?></th>
                            <?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (!empty($all_transfer_info)):
                            foreach ($all_transfer_info as $v_transfer) :
                                if ($v_transfer->transfer_id == $v_transfer->transfer_id) {
                                    $can_edit = $this->transactions_model->can_action('tbl_transfer', 'edit', array('transfer_id' => $v_transfer->transfer_id));
                                    $can_delete = $this->transactions_model->can_action('tbl_transfer', 'delete', array('transfer_id' => $v_transfer->transfer_id));

                                    $to_account_info = $this->transactions_model->check_by(array('account_id' => $v_transfer->to_account_id), 'tbl_accounts');
                                    $from_account_info = $this->transactions_model->check_by(array('account_id' => $v_transfer->from_account_id), 'tbl_accounts');
                                    $curency = $this->transactions_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                                    ?>
                                    <tr id="table_transfer_<?=$v_transfer->transfer_id ?>">
                                        <td class="vertical-td"><?php
                                            if (!empty($from_account_info->account_name)) {
                                                echo $from_account_info->account_name;
                                            } else {
                                                echo '-';
                                            }
                                            ?></td>
                                        <td class="vertical-td"><?php
                                            if (!empty($to_account_info->account_name)) {
                                                echo $to_account_info->account_name;
                                            } else {
                                                echo '-';
                                            }
                                            ?></td>
                                        <td><?= display_money($v_transfer->amount, $curency->symbol) ?></td>
                                        <td><?= strftime(config_item('date_format'), strtotime($v_transfer->date)); ?></td>
                                        <td>
                                            <?php
                                            $attachement_info = json_decode($v_transfer->attachement);
                                            if (!empty($attachement_info)) { ?>
                                                <a href="<?= base_url() ?>admin/transactions/download_transfer/<?= $v_transfer->transfer_id ?>"><?= lang('download') ?></a>
                                            <?php } ?>
                                        </td>
                                        <?php if (!empty($edited) || !empty($deleted)) { ?>
                                            <td>
                                                <?php if (!empty($edited) && !empty($can_edit)) { ?>
                                                    <?= btn_edit('admin/transactions/transfer/' . $v_transfer->transfer_id) ?>
                                                <?php }
                                                if (!empty($deleted) && !empty($can_delete)) {
                                                    ?>
                                                    <?php echo ajax_anchor(base_url("admin/transactions/delete_transfer/$v_transfer->transfer_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_transfer_" .$v_transfer->transfer_id)); ?>
                                                <?php } ?>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                    <?php
                                }
                            endforeach;
                        endif;
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if (!empty($created) || !empty($edited)) { ?>
                <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
                    <form role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data" id="form"
                          action="<?php echo base_url(); ?>admin/transactions/save_transfer/<?php
                          if (!empty($transfer_info)) {
                              echo $transfer_info->transfer_id;
                          }
                          ?>" method="post" class="form-horizontal  ">

                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('from_account') ?> <span
                                    class="text-danger">*</span>
                            </label>
                            <div class="col-lg-5">
                                <select class="form-control select_box" style="width: 100%" name="from_account_id"
                                        required <?php
                                if (!empty($transfer_info)) {
                                    echo 'disabled';
                                }
                                ?>>
                                    <option value=""><?= lang('choose_from_account') ?></option>
                                    <?php
                                    $f_account_info = $this->db->get('tbl_accounts')->result();
                                    if (!empty($f_account_info)) {
                                        foreach ($f_account_info as $v_f_account) {
                                            ?>
                                            <option value="<?= $v_f_account->account_id ?>"
                                                <?php
                                                if (!empty($transfer_info->from_account_id)) {
                                                    echo $transfer_info->from_account_id == $v_f_account->account_id ? 'selected' : '';
                                                }
                                                ?>><?= $v_f_account->account_name ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('to_account') ?> <span
                                    class="text-danger">*</span>
                            </label>
                            <div class="col-lg-5">
                                <select class="form-control select_box" style="width: 100%" name="to_account_id"
                                        required <?php
                                if (!empty($transfer_info)) {
                                    echo 'disabled';
                                }
                                ?>>
                                    <option value=""><?= lang('choose_to_account') ?></option>
                                    <?php
                                    $account_info = $this->db->get('tbl_accounts')->result();
                                    if (!empty($account_info)) {
                                        foreach ($account_info as $v_account) {
                                            ?>
                                            <option value="<?= $v_account->account_id ?>"
                                                <?php
                                                if (!empty($transfer_info->to_account_id)) {
                                                    echo $transfer_info->to_account_id == $v_account->account_id ? 'selected' : '';
                                                }
                                                ?>
                                            ><?= $v_account->account_name ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('date') ?></label>
                            <div class="col-lg-5">
                                <div class="input-group">
                                    <input type="text" name="date" class="form-control datepicker" value="<?php
                                    if (!empty($transfer_info->date)) {
                                        echo $transfer_info->date;
                                    } else {
                                        echo strftime(config_item('date_format'));
                                    }
                                    ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                                    <div class="input-group-addon">
                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group terms">
                            <label class="col-lg-3 control-label"><?= lang('notes') ?> </label>
                            <div class="col-lg-5">
                        <textarea name="notes" class="form-control"><?php
                            if (!empty($transfer_info)) {
                                echo $transfer_info->notes;
                            }
                            ?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('amount') ?> <span
                                    class="text-danger">*</span>
                            </label>
                            <div class="col-lg-5">
                                <div class="input-group  ">
                                    <input class="form-control " data-parsley-type="number" type="text" value="<?php
                                    if (!empty($transfer_info)) {
                                        echo $transfer_info->amount;
                                    }
                                    ?>" name="amount" required="" <?php
                                    if (!empty($transfer_info)) {
                                        echo 'disabled';
                                    }
                                    ?>>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('payment_method') ?> </label>
                            <div class="col-lg-5">
                                <select class="form-control select_box" style="width: 100%" name="payment_methods_id">
                                    <option value="0"><?= lang('select_payment_method') ?></option>
                                    <?php
                                    $payment_methods = $this->db->get('tbl_payment_methods')->result();
                                    if (!empty($payment_methods)) {
                                        foreach ($payment_methods as $p_method) {
                                            ?>
                                            <option value="<?= $p_method->payment_methods_id ?>" <?php
                                            if (!empty($transfer_info)) {
                                                echo $transfer_info->payment_methods_id == $p_method->payment_methods_id ? 'selected' : '';
                                            }
                                            ?>><?= $p_method->method_name ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('reference') ?> </label>
                            <div class="col-lg-5">
                                <input class="form-control " type="text" value="<?php
                                if (!empty($transfer_info)) {
                                    echo $transfer_info->reference;
                                }
                                ?>" name="reference">
                                <input class="form-control " type="hidden" value="<?php
                                if (!empty($transfer_info)) {
                                    echo $transfer_info->from_account_id;
                                }
                                ?>" name="old_from_account_id">
                                <input class="form-control " type="hidden" value="<?php
                                if (!empty($transfer_info)) {
                                    echo $transfer_info->amount;
                                }
                                ?>" name="old_amount">
                                <input class="form-control " type="hidden" value="<?php
                                if (!empty($transfer_info)) {
                                    echo $transfer_info->to_account_id;
                                }
                                ?>" name="old_to_account_id">
                                <span class="help-block"><?= lang('reference_example') ?></span>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('attachment') ?></label>

                            <div class="col-sm-5">
                                <div id="comments_file-dropzone" class="dropzone mb15">

                                </div>
                                <div id="comments_file-dropzone-scrollbar">
                                    <div id="comments_file-previews">
                                        <div id="file-upload-row" class="mt pull-left">

                                            <div class="preview box-content pr-lg" style="width:100px;">
                                                    <span data-dz-remove class="pull-right" style="cursor: pointer">
                                    <i class="fa fa-times"></i>
                                </span>
                                                <img data-dz-thumbnail class="upload-thumbnail-sm"/>
                                                <input class="file-count-field" type="hidden" name="files[]"
                                                       value=""/>
                                                <div
                                                    class="mb progress progress-striped upload-progress-sm active mt-sm"
                                                    role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                                    aria-valuenow="0">
                                                    <div class="progress-bar progress-bar-success"
                                                         style="width:0%;"
                                                         data-dz-uploadprogress></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                if (!empty($transfer_info->attachement)) {
                                    $uploaded_file = json_decode($transfer_info->attachement);
                                }
                                if (!empty($uploaded_file)) {
                                    foreach ($uploaded_file as $v_files_image) { ?>
                                        <div class="pull-left mt pr-lg mb" style="width:100px;">
                                                        <span data-dz-remove class="pull-right existing_image"
                                                              style="cursor: pointer"><i
                                                                class="fa fa-times"></i></span>
                                            <?php if ($v_files_image->is_image == 1) { ?>
                                                <img data-dz-thumbnail
                                                     src="<?php echo base_url() . $v_files_image->path ?>"
                                                     class="upload-thumbnail-sm"/>
                                            <?php } else { ?>
                                                <span data-toggle="tooltip" data-placement="top"
                                                      title="<?= $v_files_image->fileName ?>"
                                                      class="mailbox-attachment-icon"><i
                                                        class="fa fa-file-text-o"></i></span>
                                            <?php } ?>

                                            <input type="hidden" name="path[]"
                                                   value="<?php echo $v_files_image->path ?>">
                                            <input type="hidden" name="fileName[]"
                                                   value="<?php echo $v_files_image->fileName ?>">
                                            <input type="hidden" name="fullPath[]"
                                                   value="<?php echo $v_files_image->fullPath ?>">
                                            <input type="hidden" name="size[]"
                                                   value="<?php echo $v_files_image->size ?>">
                                            <input type="hidden" name="is_image[]"
                                                   value="<?php echo $v_files_image->is_image ?>">
                                        </div>
                                    <?php }; ?>
                                <?php }; ?>
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        $(".existing_image").click(function () {
                                            $(this).parent().remove();
                                        });

                                        fileSerial = 0;
                                        // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
                                        var previewNode = document.querySelector("#file-upload-row");
                                        previewNode.id = "";
                                        var previewTemplate = previewNode.parentNode.innerHTML;
                                        previewNode.parentNode.removeChild(previewNode);
                                        Dropzone.autoDiscover = false;
                                        var projectFilesDropzone = new Dropzone("#comments_file-dropzone", {
                                            url: "<?= base_url()?>admin/global_controller/upload_file",
                                            thumbnailWidth: 80,
                                            thumbnailHeight: 80,
                                            parallelUploads: 20,
                                            previewTemplate: previewTemplate,
                                            dictDefaultMessage: '<?php echo lang("file_upload_instruction"); ?>',
                                            autoQueue: true,
                                            previewsContainer: "#comments_file-previews",
                                            clickable: true,
                                            accept: function (file, done) {
                                                if (file.name.length > 200) {
                                                    done("Filename is too long.");
                                                    $(file.previewTemplate).find(".description-field").remove();
                                                }
                                                //validate the file
                                                $.ajax({
                                                    url: "<?= base_url()?>admin/global_controller/validate_project_file",
                                                    data: {file_name: file.name, file_size: file.size},
                                                    cache: false,
                                                    type: 'POST',
                                                    dataType: "json",
                                                    success: function (response) {
                                                        if (response.success) {
                                                            fileSerial++;
                                                            $(file.previewTemplate).find(".description-field").attr("name", "comment_" + fileSerial);
                                                            $(file.previewTemplate).append("<input type='hidden' name='file_name_" + fileSerial + "' value='" + file.name + "' />\n\
                                                                        <input type='hidden' name='file_size_" + fileSerial + "' value='" + file.size + "' />");
                                                            $(file.previewTemplate).find(".file-count-field").val(fileSerial);
                                                            done();
                                                        } else {
                                                            $(file.previewTemplate).find("input").remove();
                                                            done(response.message);
                                                        }
                                                    }
                                                });
                                            },
                                            processing: function () {
                                                $("#file-save-button").prop("disabled", true);
                                            },
                                            queuecomplete: function () {
                                                $("#file-save-button").prop("disabled", false);
                                            },
                                            fallback: function () {
                                                //add custom fallback;
                                                $("body").addClass("dropzone-disabled");
                                                $('.modal-dialog').find('[type="submit"]').removeAttr('disabled');

                                                $("#comments_file-dropzone").hide();

                                                $("#file-modal-footer").prepend("<button id='add-more-file-button' type='button' class='btn  btn-default pull-left'><i class='fa fa-plus-circle'></i> " + "<?php echo lang("add_more"); ?>" + "</button>");

                                                $("#file-modal-footer").on("click", "#add-more-file-button", function () {
                                                    var newFileRow = "<div class='file-row pb pt10 b-b mb10'>"
                                                        + "<div class='pb clearfix '><button type='button' class='btn btn-xs btn-danger pull-left mr remove-file'><i class='fa fa-times'></i></button> <input class='pull-left' type='file' name='manualFiles[]' /></div>"
                                                        + "<div class='mb5 pb5'><input class='form-control description-field'  name='comment[]'  type='text' style='cursor: auto;' placeholder='<?php echo lang("comment") ?>' /></div>"
                                                        + "</div>";
                                                    $("#comments_file-previews").prepend(newFileRow);
                                                });
                                                $("#add-more-file-button").trigger("click");
                                                $("#comments_file-previews").on("click", ".remove-file", function () {
                                                    $(this).closest(".file-row").remove();
                                                });
                                            },
                                            success: function (file) {
                                                setTimeout(function () {
                                                    $(file.previewElement).find(".progress-striped").removeClass("progress-striped").addClass("progress-bar-success");
                                                }, 1000);
                                            }
                                        });

                                    })
                                </script>
                            </div>
                        </div>
                        <div class="form-group" id="border-none">
                            <label for="field-1" class="col-sm-3 control-label"><?= lang('permission') ?> <span
                                    class="required">*</span></label>
                            <div class="col-sm-9">
                                <div class="checkbox c-radio needsclick">
                                    <label class="needsclick">
                                        <input id="" <?php
                                        if (!empty($transfer_info->permission) && $transfer_info->permission == 'all') {
                                            echo 'checked';
                                        } elseif (empty($transfer_info)) {
                                            echo 'checked';
                                        }
                                        ?> type="radio" name="permission" value="everyone">
                                        <span class="fa fa-circle"></span><?= lang('everyone') ?>
                                        <i title="<?= lang('permission_for_all') ?>"
                                           class="fa fa-question-circle" data-toggle="tooltip"
                                           data-placement="top"></i>
                                    </label>
                                </div>
                                <div class="checkbox c-radio needsclick">
                                    <label class="needsclick">
                                        <input id="" <?php
                                        if (!empty($transfer_info->permission) && $transfer_info->permission != 'all') {
                                            echo 'checked';
                                        }
                                        ?> type="radio" name="permission" value="custom_permission"
                                        >
                                        <span class="fa fa-circle"></span><?= lang('custom_permission') ?> <i
                                            title="<?= lang('permission_for_customization') ?>"
                                            class="fa fa-question-circle" data-toggle="tooltip"
                                            data-placement="top"></i>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group <?php
                        if (!empty($transfer_info->permission) && $transfer_info->permission != 'all') {
                            echo 'show';
                        }
                        ?>" id="permission_user_1">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('users') ?>
                                <span
                                    class="required">*</span></label>
                            <div class="col-sm-9">
                                <?php
                                if (!empty($permission_user)) {
                                    foreach ($permission_user as $key => $v_user) {

                                        if ($v_user->role_id == 1) {
                                            $role = '<strong class="badge btn-danger">' . lang('admin') . '</strong>';
                                        } else {
                                            $role = '<strong class="badge btn-primary">' . lang('staff') . '</strong>';
                                        }

                                        ?>
                                        <div class="checkbox c-checkbox needsclick">
                                            <label class="needsclick">
                                                <input type="checkbox"
                                                    <?php
                                                    if (!empty($transfer_info->permission) && $transfer_info->permission != 'all') {
                                                        $get_permission = json_decode($transfer_info->permission);
                                                        foreach ($get_permission as $user_id => $v_permission) {
                                                            if ($user_id == $v_user->user_id) {
                                                                echo 'checked';
                                                            }
                                                        }

                                                    }
                                                    ?>
                                                       value="<?= $v_user->user_id ?>"
                                                       name="assigned_to[]"
                                                       class="needsclick">
                                                        <span
                                                            class="fa fa-check"></span><?= $v_user->username . ' ' . $role ?>
                                            </label>

                                        </div>
                                        <div class="action_1 p
                                                <?php

                                        if (!empty($transfer_info->permission) && $transfer_info->permission != 'all') {
                                            $get_permission = json_decode($transfer_info->permission);

                                            foreach ($get_permission as $user_id => $v_permission) {
                                                if ($user_id == $v_user->user_id) {
                                                    echo 'show';
                                                }
                                            }

                                        }
                                        ?>
                                                " id="action_1<?= $v_user->user_id ?>">
                                            <label class="checkbox-inline c-checkbox">
                                                <input id="<?= $v_user->user_id ?>" checked type="checkbox"
                                                       name="action_1<?= $v_user->user_id ?>[]"
                                                       disabled
                                                       value="view">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('view') ?>
                                            </label>
                                            <label class="checkbox-inline c-checkbox">
                                                <input id="<?= $v_user->user_id ?>"
                                                    <?php

                                                    if (!empty($transfer_info->permission) && $transfer_info->permission != 'all') {
                                                        $get_permission = json_decode($transfer_info->permission);

                                                        foreach ($get_permission as $user_id => $v_permission) {
                                                            if ($user_id == $v_user->user_id) {
                                                                if (in_array('edit', $v_permission)) {
                                                                    echo 'checked';
                                                                };

                                                            }
                                                        }

                                                    }
                                                    ?>
                                                       type="checkbox"
                                                       value="edit" name="action_<?= $v_user->user_id ?>[]">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('edit') ?>
                                            </label>
                                            <label class="checkbox-inline c-checkbox">
                                                <input id="<?= $v_user->user_id ?>"
                                                    <?php

                                                    if (!empty($transfer_info->permission) && $transfer_info->permission != 'all') {
                                                        $get_permission = json_decode($transfer_info->permission);
                                                        foreach ($get_permission as $user_id => $v_permission) {
                                                            if ($user_id == $v_user->user_id) {
                                                                if (in_array('delete', $v_permission)) {
                                                                    echo 'checked';
                                                                };
                                                            }
                                                        }

                                                    }
                                                    ?>
                                                       name="action_<?= $v_user->user_id ?>[]"
                                                       type="checkbox"
                                                       value="delete">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('delete') ?>
                                            </label>
                                            <input id="<?= $v_user->user_id ?>" type="hidden"
                                                   name="action_<?= $v_user->user_id ?>[]" value="view">

                                        </div>


                                        <?php
                                    }
                                }
                                ?>


                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"></label>
                            <div class="col-lg-5">
                                <button type="submit" id="file-save-button" class="btn btn-sm btn-primary"><i
                                        class="fa fa-check"></i> <?= lang('submit') ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php }else{ ?>
        </div>
        <?php } ?>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var maxAppend = 0;
        $("#add_more").click(function () {
            if (maxAppend >= 4) {
                alert("Maximum 5 File is allowed");
            } else {
                var add_new = $('<div class="form-group" style="margin-bottom: 0px">\n\
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('attachment') ?></label>\n\
        <div class="col-sm-4">\n\
        <div class="fileinput fileinput-new" data-provides="fileinput">\n\
<span class="btn btn-default btn-file"><span class="fileinput-new" >Select file</span><span class="fileinput-exists" >Change</span><input type="file" name="attachement[]" ></span> <span class="fileinput-filename"></span><a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a></div></div>\n\<div class="col-sm-2">\n\<strong>\n\
<a href="javascript:void(0);" class="remCF"><i class="fa fa-times"></i>&nbsp;Remove</a></strong></div>');
                maxAppend++;
                $("#add_new").append(add_new);
            }
        });

        $("#add_new").on('click', '.remCF', function () {
            $(this).parent().parent().parent().remove();
        });
        $('a.RCF').click(function () {
            $(this).parent().parent().remove();
        });
    });
</script>