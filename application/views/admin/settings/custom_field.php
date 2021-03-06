<?php
echo message_box('success');
echo message_box('error');
$created = can_action('130', 'created');
$edited = can_action('130', 'edited');
$deleted = can_action('130', 'deleted');
if (!empty($created) || !empty($edited)){
?>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs" style="margin-top: 1px">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                            data-toggle="tab"><?= lang('custom_field') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create"
                                                            data-toggle="tab"><?= lang('new_field') ?></a></li>
    </ul>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <?php } else { ?>
            <div class="panel panel-custom">
                <header class="panel-heading ">
                    <div class="panel-title"><strong><?= lang('custom_field') ?></strong></div>
                </header>
                <?php } ?>
                <div class="table-responsive">
                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th><?= lang('label') ?></th>
                            <th><?= lang('custom_field_for') ?></th>
                            <th><?= lang('type') ?></th>
                            <th><?= lang('active') ?></th>
                            <?php if (!empty($edited) || !empty($deleted)) { ?>
                                <th><?= lang('action') ?></th>
                            <?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $all_custom_fields = $this->db->get('tbl_custom_field')->result();

                        if (!empty($all_custom_fields)) {
                            foreach ($all_custom_fields as $v_custom_fields):
                                $form_info = $this->db->where('form_id', $v_custom_fields->form_id)->get('tbl_form')->row();
                                if ($v_custom_fields->field_type == 'dropdown') {
                                    $type = lang('dropdowns');
                                } else {
                                    $type = lang($v_custom_fields->field_type);
                                }
                                ?>
                                <tr id="custom_field_<?= $v_custom_fields->custom_field_id ?>">
                                    <td><?= $v_custom_fields->field_label ?></td>
                                    <td><?= lang($form_info->form_name) ?></td>
                                    <td><?= $type ?></td>
                                    <td>
                                        <input data-toggle="toggle" id="<?= $v_custom_fields->custom_field_id ?>"
                                               name="status" value="active" <?php
                                        if (!empty($v_custom_fields->status) && $v_custom_fields->status == 'active') {
                                            echo 'checked';
                                        }
                                        ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                               data-onstyle="success btn-xs status"
                                               data-offstyle="danger btn-xs status" type="checkbox">

                                    </td>
                                    <?php if (!empty($edited) || !empty($deleted)) { ?>
                                        <td>
                                            <?php if (!empty($edited)) { ?>
                                                <?= btn_edit('admin/settings/custom_field/' . $v_custom_fields->custom_field_id) ?>
                                            <?php }
                                            if (!empty($deleted)) { ?>
                                                <?php echo ajax_anchor(base_url("admin/settings/detele_custom_field/" . $v_custom_fields->custom_field_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#custom_field_" . $v_custom_fields->custom_field_id)); ?>
                                            <?php } ?>
                                        </td>
                                    <?php } ?>
                                </tr>
                                <?php
                            endforeach;
                        }
                        ?>

                        </tbody>
                    </table>
                </div>
            </div>
            <?php if (!empty($created) || !empty($edited)) { ?>
                <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
                    <form role="form" enctype="multipart/form-data" id="form" data-parsley-validate="" novalidate=""
                          action="<?php echo base_url(); ?>admin/settings/save_custom_field/<?php
                          if (!empty($field_info)) {
                              echo $field_info->custom_field_id;
                          }
                          ?>" method="post" class="form-horizontal  ">
                        <div class="form-group" id="border-none">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('custom_field_for') ?> <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-5">

                                <select name="form_id" class="form-control select_box" style="width:100%" required>
                                    <?php
                                    $all_form = $this->db->get('tbl_form')->result();
                                    if (!empty($all_form)) {
                                        foreach ($all_form as $v_form) { ?>
                                            <option
                                                value="<?= $v_form->form_id ?>" <?= (!empty($field_info->form_id) && $field_info->form_id == $v_form->form_id ? 'selected' : null) ?>> <?= lang($v_form->form_name) ?> </option>
                                        <?php }

                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('field_label') ?> <span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" value="<?php
                                if (!empty($field_info)) {
                                    echo $field_info->field_label;
                                }
                                ?>" name="field_label" required="">
                            </div>

                        </div>

                        <div class="default_value type">
                            <div class="form-group">
                                <label class="col-lg-3 control-label"><?= lang('default_value') ?> </label>
                                <div class="col-lg-5">
                                    <input type="text" class="form-control" value="<?=
                                    (isset($field_info->field_type) && $field_info->field_type == 'text' || isset($field_info->field_type) && $field_info->field_type == 'email' ? json_decode($field_info->default_value)[0] : null)
                                    ?>" name="default_value[]">
                                </div>

                            </div>
                        </div>
                        <div class="checkbox_type type">
                            <div class="form-group ">
                                <label class="col-lg-3 control-label"><?= lang('default_value') ?> </label>
                                <div class="col-md-4">
                                    <?php
                                    if (!empty($field_info->field_type) && $field_info->field_type == 'checkbox') {
                                        $default_value = json_decode($field_info->default_value)[0];
                                    } else {
                                        $default_value = null;
                                    }
                                    $options = array(
                                        'checked' => lang('checked'),
                                        'unchecked' => lang('unchecked'),
                                    );
                                    echo form_dropdown('default_value[]', $options, $default_value, 'style="width:100%" class="select_box" required'); ?>
                                </div>
                            </div>
                        </div>
                        <!-- End discount Fields -->
                        <div class="form-group terms">
                            <label class="col-lg-3 control-label"><?= lang('help_text') ?> </label>
                            <div class="col-lg-5">
                        <textarea name="help_text" class="form-control"><?php
                            if (!empty($field_info)) {
                                echo $field_info->help_text;
                            }
                            ?></textarea>
                            </div>
                        </div>
                        <div class="form-group" id="border-none">
                            <label for="field-1"
                                   class="col-sm-3 control-label"><?= lang('field_type') ?> <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-5">
                                <?php
                                if (!empty($field_info->field_type)) {
                                    $field_type = $field_info->field_type;
                                } else {
                                    $field_type = null;
                                }
                                $options = array(
                                    'text' => lang('text_field'),
                                    'textarea' => lang('textarea'),
                                    'dropdown' => lang('select'),
                                    'email' => lang('email'),
                                    'date' => lang('date'),
                                    'checkbox' => lang('checkbox'),
                                    'numeric' => lang('numeric'),
                                );
                                echo form_dropdown('field_type', $options, $field_type, 'style="width:100%" id="type" class="select_box" required'); ?>
                            </div>
                        </div>
                        <div class="form-group" id="show_dropdown">
                            <label class="col-lg-3 control-label"><?= lang('option') ?></label>
                            <div class="col-lg-9 show_dropdown" id="option">

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('required') ?></label>
                            <div class="col-lg-6">
                                <div class="checkbox c-checkbox">
                                    <label class="needsclick">
                                        <input type="checkbox" <?php
                                        if (!empty($field_info->required) && $field_info->required == 'on') {
                                            echo "checked=\"checked\"";
                                        }
                                        ?> name="required">
                                        <span class="fa fa-check"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('show_on_table') ?></label>
                            <div class="col-lg-6">
                                <div class="checkbox c-checkbox">
                                    <label class="needsclick">
                                        <input type="checkbox" <?php
                                        if (!empty($field_info->show_on_table) && $field_info->show_on_table == 'on') {
                                            echo "checked=\"checked\"";
                                        }
                                        ?> name="show_on_table">
                                        <span class="fa fa-check"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('show_on_details') ?></label>
                            <div class="col-lg-6">
                                <div class="checkbox c-checkbox">
                                    <label class="needsclick">
                                        <input type="checkbox" <?php
                                        if (!empty($field_info->show_on_details) && $field_info->show_on_details == 'on') {
                                            echo "checked=\"checked\"";
                                        }
                                        ?> name="show_on_details">
                                        <span class="fa fa-check"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('visible_for_admin') ?></label>
                            <div class="col-lg-6">
                                <div class="checkbox c-checkbox">
                                    <label class="needsclick">
                                        <input type="checkbox" <?php
                                        if (!empty($field_info->visible_for_admin) && $field_info->visible_for_admin == 'on') {
                                            echo "checked=\"checked\"";
                                        }
                                        ?> name="visible_for_admin">
                                        <span class="fa fa-check"></span>
                                    </label>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-lg-3 control-label"></label>
                            <div class="col-lg-5">
                                <button type="submit" class="btn btn-sm btn-primary"><?= lang('update') ?></button>
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
    var option = 0;

    $(document).ready(function () {
        $("#type").change(function () {
            $(this).find("option:selected").each(function () {
                if ($(this).attr("value") == "dropdown") {
                    <?php
                    if(!empty($field_info->field_type)){
                    foreach(json_decode($field_info->default_value) as $optionValue){ ?>
                    addOption("<?= $optionValue;?>");
                    <?php }
                    }else{
                    ?>
                    addOption("");
                    addOption("");
                    <?php }?>
                    $('.show_dropdown').show();
                    $('#show_dropdown').show();
                    $(".show_dropdown :input").attr("disabled", false);

                    $('.checkbox_type').hide();
                    $(".checkbox_type :input").attr("disabled", true);
                    $('.default_value').hide();
                    $(".default_value :input").attr("disabled", true);
                }
                else if ($(this).attr("value") == "checkbox") {
                    $('.checkbox_type').show();
                    $(".checkbox_type :input").attr("disabled", false);
                    $('.default_value').hide();
                    $(".default_value :input").attr("disabled", true);
                    $('#show_dropdown').hide();
                    $('.show_dropdown').hide();
                    $(".show_dropdown :input").attr("disabled", true);
                } else {
                    $('#show_dropdown').hide();
                    $('.show_dropdown').hide();
                    $(".show_dropdown :input").attr("disabled", true);
                    $('.default_value').show();
                    $(".default_value :input").attr("disabled", false);
                    $('.checkbox_type').hide();
                    $(".checkbox_type :input").attr("disabled", true);
                }
            });
        }).change();

    });

    function addOption(event, curr_option_id) {
        var optionValue = '';
        if (typeof event !== 'undefined') {
            if (typeof event == 'object') {
                event.preventDefault();
            } else {
                optionValue = event;
            }
        }
        var option_id = ++option;

        var add_new = $('<div class="form-group remCF" id="' + option_id + '">\n\
        <div class="col-lg-6">\n\
        <input type="text" class="form-control" value="' + optionValue + '" id="inputFocus_' + option_id + '" name="default_value[]"/>\n\
        </div>\n\
        <div class="col-lg-2">\n\
        <a href="#" onclick="addOption(event, ' + option_id + ')"><i class="text-success fa fa-plus-circle"></i></a>\n\
        <a href="javascript:void(0);" onclick="removeOption(' + option_id + ')"><i class="text-danger fa fa-minus-circle"></i></a>\n\
        </div></div></div><div id="nextOption_' + option_id + '"> </div>');

        if (typeof curr_option_id !== 'undefined') {
            $('#nextOption_' + curr_option_id + '').prepend(add_new);
        } else {
            $("#option").append(add_new);
        }
        $('#inputFocus_' + option_id + '').focus();
    }

    function removeOption(option_id) {
        $('#' + option_id + '').remove();
    }
</script>
<script type="text/javascript">

    $(document).ready(function () {
        $('.status input[type="checkbox"]').change(function () {
            var id = $(this).attr('id');
            if ($(this).is(":checked")) {
                var status = 'active';
            } else {
                var status = 'deactive';
            }
            var formData = {
                'status': status,
            };
            $.ajax({
                type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                url: '<?= base_url()?>admin/settings/change_field_status/' + id, // the url where we want to POST
                data: formData, // our data object
                dataType: 'json', // what type of data do we expect back from the server
                encode: true,
                success: function (res) {
                    console.log(res);
                    if (res) {
                        location.reload();
                    } else {
                        alert('There was a problem with AJAX');
                    }
                }
            })

        });

    })
    ;
</script>