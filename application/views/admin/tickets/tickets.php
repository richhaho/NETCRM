<?= message_box('success'); ?>
<?= message_box('error'); ?>
<?php
$answered = 0;
$closed = 0;
$open = 0;
$in_progress = 0;

$progress_tickets_info = $this->tickets_model->get_permission('tbl_tickets');
// 30 days before

for ($iDay = 30; $iDay >= 0; $iDay--) {
    $date = strftime(config_item('date_format'), strtotime('today - ' . $iDay . 'days'));
    $where = array('created >=' => $date . " 00:00:00", 'created <=' => $date . " 23:59:59");

    $tickets_result[$date] = count($this->db->where($where)->get('tbl_tickets')->result());
}

if (!empty($progress_tickets_info)):foreach ($progress_tickets_info as $v_tickets):
    if ($v_tickets->status == 'answered') {
        $answered += count($v_tickets->status);
    }
    if ($v_tickets->status == 'closed') {
        $closed += count($v_tickets->status);
    }
    if ($v_tickets->status == 'open') {
        $open += count($v_tickets->status);
    }
    if ($v_tickets->status == 'in_progress') {
        $in_progress += count($v_tickets->status);
    }
endforeach;
endif;
if ($this->session->userdata('user_type') == 1) {
    $margin = 'margin-bottom:30px';
    ?>
    <div class="col-sm-12 bg-white p0" style="<?= $margin ?>">
        <div class="col-md-4">
            <div class="row row-table pv-lg">
                <div class="col-xs-6">
                    <p class="m0 lead"><?= $answered ?></p>
                    <p class="m0">
                        <small><a
                                href="<?= base_url() ?>admin/tickets/answered"> <?= lang('answered') . ' ' . lang('tickets') ?></a>
                        </small>
                    </p>
                </div>
                <div class="col-xs-6">
                    <p class="m0 lead"><?= $in_progress ?></p>
                    <p class="m0">
                        <small><a
                                href="<?= base_url() ?>admin/tickets/in_progress"><?= lang('in_progress') . ' ' . lang('tickets') ?></a>
                        </small>
                    </p>
                </div>


            </div>
        </div>
        <div class="col-md-3">
            <div class="row row-table pv-lg">
                <div class="col-xs-6">
                    <p class="m0 lead"><?= $open ?></p>
                    <p class="m0">
                        <small><a
                                href="<?= base_url() ?>admin/tickets/open"><?= lang('open') . ' ' . lang('tickets') ?></a>
                        </small>
                    </p>
                </div>
                <div class="col-xs-6">
                    <p class="m0 lead"><?= $closed ?></p>
                    <p class="m0">
                        <small><a
                                href="<?= base_url() ?>admin/tickets/closed"><?= lang('close') . ' ' . lang('tickets') ?></a>
                        </small>
                    </p>
                </div>

            </div>
        </div>
        <div class="col-md-5">
            <div class="row row-table text-center pt">
                <div data-sparkline="" data-bar-color="#23b7e5" data-height="60" data-bar-width="7"
                     data-bar-spacing="6" data-chart-range-min="0"
                     values="<?php
                     if (!empty($tickets_result)) {
                         foreach ($tickets_result as $v_tickets_result) {
                             echo $v_tickets_result . ',';
                         }
                     }
                     ?>">
                </div>

                <span class="easypie-text "><strong><?= lang('last_30_days') ?></strong></span>

            </div>
        </div>
    </div>
<?php }
$status = $this->uri->segment(3);

if (!empty($status)) {
    $m_id = 7;
    if ($status == 'answered') {
        $m_id = 8;
    }
    if ($status == 'open') {
        $m_id = 9;
    }
    if ($status == 'in_progress') {
        $m_id = 10;
    }
    if ($status == 'closed') {
        $m_id = 11;
    }
} else {
    $m_id = 7;
}

if (!empty($m_id)) {
    $created = can_action($m_id, 'created');
    $edited = can_action($m_id, 'edited');
    $deleted = can_action($m_id, 'deleted');
}
if (!empty($created) || !empty($edited)){
?>
<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs">
                <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                                    data-toggle="tab"><?= lang('tickets') ?></a>
                </li>
                <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#new"
                                                                    data-toggle="tab"><?= lang('new_ticket') ?></a>
                </li>
            </ul>
            <div class="tab-content bg-white">
                <!-- ************** general *************-->
                <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
                    <?php } else { ?>
                    <div class="panel panel-custom">
                        <header class="panel-heading ">
                            <div class="panel-title"><strong><?= lang('tickets') ?></strong></div>
                        </header>
                        <?php } ?>
                        <div class="table-responsive">
                            <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                                <thead>
                                <tr>

                                    <th><?= lang('ticket_code') ?></th>
                                    <th><?= lang('subject') ?></th>
                                    <th class="col-date"><?= lang('date') ?></th>
                                    <?php if ($this->session->userdata('user_type') == '1') { ?>
                                        <th><?= lang('reporter') ?></th>
                                    <?php } ?>
                                    <th><?= lang('department') ?></th>
                                    <th><?= lang('status') ?></th>
                                    <?php $show_custom_fields = custom_form_table(7, null);
                                    if (!empty($show_custom_fields)) {
                                        foreach ($show_custom_fields as $c_label => $v_fields) {
                                            if (!empty($c_label)) {
                                                ?>
                                                <th><?= $c_label ?> </th>
                                            <?php }
                                        }
                                    }
                                    ?>

                                    <th><?= lang('action') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (!empty($all_tickets_info)) {
                                    foreach ($all_tickets_info as $v_tickets_info) {
                                        $can_edit = $this->tickets_model->can_action('tbl_tickets', 'edit', array('tickets_id' => $v_tickets_info->tickets_id));
                                        $can_delete = $this->tickets_model->can_action('tbl_tickets', 'delete', array('tickets_id' => $v_tickets_info->tickets_id));
                                        if ($v_tickets_info->status == 'open') {
                                            $s_label = 'danger';
                                        } elseif ($v_tickets_info->status == 'closed') {
                                            $s_label = 'success';
                                        } else {
                                            $s_label = 'default';
                                        }
                                        $profile_info = $this->db->where(array('user_id' => $v_tickets_info->reporter))->get('tbl_account_details')->row();
                                        $dept_info = $this->db->where(array('departments_id' => $v_tickets_info->departments_id))->get('tbl_departments')->row();
                                        if (!empty($dept_info)) {
                                            $dept_name = $dept_info->deptname;
                                        } else {
                                            $dept_name = '-';
                                        }
                                        if (!empty($ticket_status)) {
                                            if ($ticket_status == $v_tickets_info->status) {
                                                ?>
                                                <tr id="table_ticket_<?= $v_tickets_info->tickets_id ?>">

                                                    <td><span
                                                            class="label label-success"><?= $v_tickets_info->ticket_code ?></span>
                                                    </td>
                                                    <td><a class="text-info"
                                                           href="<?= base_url() ?>admin/tickets/index/tickets_details/<?= $v_tickets_info->tickets_id ?>"><?= $v_tickets_info->subject ?></a>
                                                    </td>
                                                    <td><?= strftime(config_item('date_format'), strtotime($v_tickets_info->created)); ?></td>
                                                    <?php if ($this->session->userdata('user_type') == '1') { ?>

                                                        <td>
                                                            <a class="pull-left recect_task  ">
                                                                <?php if (!empty($profile_info)) {
                                                                    ?>
                                                                    <img style="width: 30px;margin-left: 18px;
                                                         height: 29px;
                                                         border: 1px solid #aaa;"
                                                                         src="<?= base_url() . $profile_info->avatar ?>"
                                                                         class="img-circle">
                                                                    <?= ($profile_info->fullname) ?>
                                                                <?php } else {
                                                                    echo '-';
                                                                } ?>
                                                            </a>
                                                        </td>

                                                    <?php } ?>
                                                    <td><?= $dept_name ?></td>
                                                    <?php
                                                    if ($v_tickets_info->status == 'in_progress') {
                                                        $status = 'In Progress';
                                                    } else {
                                                        $status = $v_tickets_info->status;
                                                    }
                                                    ?>
                                                    <td><span
                                                            class="label label-<?= $s_label ?>"><?= ($status) ?></span>
                                                    </td>
                                                    <?php $show_custom_fields = custom_form_table(7, $v_tickets_info->tickets_id);
                                                    if (!empty($show_custom_fields)) {
                                                        foreach ($show_custom_fields as $c_label => $v_fields) {
                                                            if (!empty($c_label)) {
                                                                ?>
                                                                <td><?= $v_fields ?> </td>
                                                            <?php }
                                                        }
                                                    }
                                                    ?>
                                                    <td>
                                                        <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                            <?= btn_edit('admin/tickets/index/edit_tickets/' . $v_tickets_info->tickets_id) ?>
                                                        <?php }
                                                        if (!empty($can_delete) && !empty($deleted)) { ?>
                                                            <?php echo ajax_anchor(base_url("admin/tickets/delete/delete_tickets/$v_tickets_info->tickets_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_ticket_" . $v_tickets_info->tickets_id)); ?>
                                                        <?php } ?>
                                                        <?= btn_view('admin/tickets/index/tickets_details/' . $v_tickets_info->tickets_id) ?>
                                                        <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                            <div class="btn-group">
                                                                <button class="btn btn-xs btn-success dropdown-toggle"
                                                                        data-toggle="dropdown">
                                                                    <?= lang('change_status') ?>
                                                                    <span class="caret"></span></button>
                                                                <ul class="dropdown-menu animated zoomIn">
                                                                    <?php
                                                                    $status_info = $this->db->get('tbl_status')->result();
                                                                    if (!empty($status_info)) {
                                                                        foreach ($status_info as $v_status) {
                                                                            ?>
                                                                            <li><a data-toggle='modal'
                                                                                   data-target='#myModal'
                                                                                   href="<?= base_url() ?>admin/tickets/change_status/<?= $v_tickets_info->tickets_id ?>/<?= $v_status->status ?>"><?= ucfirst($v_status->status) ?></a>
                                                                            </li>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </ul>
                                                            </div>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        } else { ?>
                                            <tr id="table_ticket_<?= $v_tickets_info->tickets_id ?>">

                                                <td><span
                                                        class="label label-success"><?= $v_tickets_info->ticket_code ?></span>
                                                </td>
                                                <td><a class="text-info"
                                                       href="<?= base_url() ?>admin/tickets/index/tickets_details/<?= $v_tickets_info->tickets_id ?>"><?= $v_tickets_info->subject ?></a>
                                                </td>
                                                <td><?= strftime(config_item('date_format'), strtotime($v_tickets_info->created)); ?></td>
                                                <?php if ($this->session->userdata('user_type') == '1') { ?>

                                                    <td>
                                                        <a class="pull-left recect_task  ">
                                                            <?php if (!empty($profile_info)) {
                                                                ?>
                                                                <img style="width: 30px;margin-left: 18px;
                                                         height: 29px;
                                                         border: 1px solid #aaa;"
                                                                     src="<?= base_url() . $profile_info->avatar ?>"
                                                                     class="img-circle">


                                                                <?=
                                                                ($profile_info->fullname)
                                                                ?>
                                                            <?php } else {
                                                                echo '-';
                                                            } ?>
                                                        </a>
                                                    </td>

                                                <?php } ?>
                                                <td><?= $dept_name ?></td>
                                                <?php
                                                if ($v_tickets_info->status == 'in_progress') {
                                                    $status = 'In Progress';
                                                } else {
                                                    $status = $v_tickets_info->status;
                                                }
                                                ?>
                                                <td><span
                                                        class="label label-<?= $s_label ?>"><?= ucfirst($status) ?></span>
                                                </td>
                                                <?php $show_custom_fields = custom_form_table(7, $v_tickets_info->tickets_id);
                                                if (!empty($show_custom_fields)) {
                                                    foreach ($show_custom_fields as $c_label => $v_fields) {
                                                        if (!empty($c_label)) {
                                                            ?>
                                                            <td><?= $v_fields ?> </td>
                                                        <?php }
                                                    }
                                                }
                                                ?>
                                                <td>
                                                    <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                        <?= btn_edit('admin/tickets/index/edit_tickets/' . $v_tickets_info->tickets_id) ?>
                                                    <?php }
                                                    if (!empty($can_delete) && !empty($deleted)) { ?>
                                                        <?php echo ajax_anchor(base_url("admin/tickets/delete/delete_tickets/$v_tickets_info->tickets_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_ticket_" . $v_tickets_info->tickets_id)); ?>
                                                    <?php } ?>
                                                    <?= btn_view('admin/tickets/index/tickets_details/' . $v_tickets_info->tickets_id) ?>
                                                    <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                        <div class="btn-group">
                                                            <button class="btn btn-xs btn-success dropdown-toggle"
                                                                    data-toggle="dropdown">
                                                                <?= lang('change_status') ?>
                                                                <span class="caret"></span></button>
                                                            <ul class="dropdown-menu animated zoomIn">
                                                                <?php
                                                                $status_info = $this->db->get('tbl_status')->result();
                                                                if (!empty($status_info)) {
                                                                    foreach ($status_info as $v_status) {
                                                                        ?>
                                                                        <li><a data-toggle='modal'
                                                                               data-target='#myModal'
                                                                               href="<?= base_url() ?>admin/tickets/change_status/<?= $v_tickets_info->tickets_id ?>/<?= $v_status->status ?>"><?= ucfirst($v_status->status) ?></a>
                                                                        </li>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </ul>
                                                        </div>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php }
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if (!empty($created) || !empty($edited)) { ?>
                        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="new">
                            <form method="post" data-parsley-validate="" novalidate=""
                                  action="<?= base_url() ?>admin/tickets/create_tickets/<?php
                                  if (!empty($tickets_info)) {
                                      echo $tickets_info->tickets_id;
                                  }
                                  ?>" enctype="multipart/form-data" class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('ticket_code') ?> <span
                                            class="text-danger">*</span></label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control" style="width:260px" value="<?php
                                        $this->load->helper('string');
                                        if (!empty($tickets_info)) {
                                            echo $tickets_info->ticket_code;
                                        } else {
                                            echo strtoupper(random_string('alnum', 7));
                                        }
                                        ?>" name="ticket_code">
                                    </div>
                                </div>
                                <?php $projects = $this->uri->segment(4);
                                if ($projects != 'project_tickets') {
                                    ?>
                                    <input type="hidden" value="<?php echo $this->uri->segment(3) ?>"
                                           class="form-control"
                                           name="status">
                                <?php } ?>

                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('subject') ?> <span
                                            class="text-danger">*</span></label>
                                    <div class="col-lg-5">
                                        <input type="text" value="<?php
                                        if (!empty($tickets_info)) {
                                            echo $tickets_info->subject;
                                        }
                                        ?>" class="form-control" placeholder="Sample Ticket Subject" name="subject"
                                               required>
                                    </div>
                                </div>
                                <?php if ($this->session->userdata('user_type') == '1') {
                                    $type = $this->uri->segment(5);
                                    if (!empty($type) && !is_numeric($type)) {
                                        $ex = explode('_', $type);
                                        if ($ex[0] == 'c') {
                                            $primary_contact = $ex[1];
                                        }
                                    }
                                    ?>
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('reporter') ?> <span
                                                class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-5">
                                            <div class=" ">
                                                <select class="form-control select_box" style="width:100%"
                                                        name="reporter" required>
                                                    <option value=""><?= lang('none') ?></option>
                                                    <?php
                                                    $users = $this->db->get('tbl_users')->result();
                                                    if (!empty($users)) {
                                                        foreach ($users as $v_user):
                                                            $users_info = $this->db->where(array("user_id" => $v_user->user_id))->get('tbl_account_details')->row();
                                                            if (!empty($users_info)) {
                                                                if ($v_user->role_id == 1) {
                                                                    $role = lang('admin');
                                                                } elseif ($v_user->role_id == 2) {
                                                                    $role = lang('client');
                                                                } else {
                                                                    $role = lang('staff');
                                                                }
                                                                ?>
                                                                <option value="<?= $users_info->user_id ?>" <?php
                                                                if (!empty($tickets_info) && $tickets_info->reporter == $users_info->user_id) {
                                                                    echo 'selected';
                                                                } else if (!empty($primary_contact) && $primary_contact == $users_info->user_id) {
                                                                    echo 'selected';
                                                                }
                                                                ?>><?= $users_info->fullname . ' (' . $role . ')'; ?></option>
                                                                <?php
                                                            }
                                                        endforeach;
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('project') ?> <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-5">
                                        <div class=" ">
                                            <select class="form-control select_box" style="width:100%"
                                                    name="project_id">
                                                <option><?= lang('none') ?></option>
                                                <?php
                                                $project = $this->db->get('tbl_project')->result();
                                                $project_id = $this->uri->segment(6);
                                                if (!empty($project)) {
                                                    foreach ($project as $v_project):
                                                        ?>
                                                        <option value="<?= $v_project->project_id ?>" <?php
                                                        if (!empty($tickets_info) && $tickets_info->project_id == $v_project->project_id) {
                                                            echo 'selected';
                                                        }
                                                        if ($projects == 'project_tickets') {
                                                            if (!empty($project_id) && $project_id == $v_project->project_id) {
                                                                echo 'selected';
                                                            }
                                                        }
                                                        ?>><?= $v_project->project_name; ?></option>
                                                        <?php
                                                    endforeach;
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('priority') ?> <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-5">
                                        <div class=" ">
                                            <select name="priority" class="form-control">
                                                <?php
                                                $priorities = $this->db->get('tbl_priorities')->result();
                                                if (!empty($priorities)) {
                                                    foreach ($priorities as $v_priorities):
                                                        ?>
                                                        <option value="<?= $v_priorities->priority ?>" <?php
                                                        if (!empty($tickets_info) && $tickets_info->priority == $v_priorities->priority) {
                                                            echo 'selected';
                                                        }
                                                        ?>><?= lang(strtolower($v_priorities->priority)) ?></option>
                                                        <?php
                                                    endforeach;
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('department') ?> <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-5">
                                        <div class=" ">
                                            <select name="departments_id" class="form-control select_box"
                                                    style="width: 100%" required>
                                                <?php
                                                $all_departments = $this->db->get('tbl_departments')->result();
                                                if (!empty($all_departments)) {
                                                    foreach ($all_departments as $v_dept):
                                                        ?>
                                                        <option value="<?= $v_dept->departments_id ?>" <?php
                                                        if (!empty($tickets_info) && $tickets_info->departments_id == $v_dept->departments_id) {
                                                            echo 'selected';
                                                        }
                                                        ?>><?= $v_dept->deptname ?></option>
                                                        <?php
                                                    endforeach;
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                if (!empty($tickets_info)) {
                                    $tickets_id = $tickets_info->tickets_id;
                                } else {
                                    $tickets_id = null;
                                }
                                ?>
                                <?= custom_form_Fields(7, $tickets_id); ?>


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
                                        if (!empty($tickets_info->upload_file)) {
                                            $uploaded_file = json_decode($tickets_info->upload_file);
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
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('ticket_message') ?> </label>
                                    <div class="col-lg-7">
                        <textarea name="body" class="form-control textarea_" placeholder="<?= lang('message') ?>"><?php
                            if (!empty($tickets_info)) {
                                echo $tickets_info->body;
                            } else {
                                echo set_value('body');
                            }
                            ?></textarea>

                                    </div>
                                </div>
                                <div class="form-group" id="border-none">
                                    <label for="field-1" class="col-sm-3 control-label"><?= lang('permission') ?> <span
                                            class="required">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="checkbox c-radio needsclick">
                                            <label class="needsclick">
                                                <input id="" <?php
                                                if (!empty($tickets_info->permission) && $tickets_info->permission == 'all') {
                                                    echo 'checked';
                                                } elseif (empty($tickets_info)) {
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
                                                if (!empty($tickets_info->permission) && $tickets_info->permission != 'all') {
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
                                if (!empty($tickets_info->permission) && $tickets_info->permission != 'all') {
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
                                                            if (!empty($tickets_info->permission) && $tickets_info->permission != 'all') {
                                                                $get_permission = json_decode($tickets_info->permission);
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

                                                if (!empty($tickets_info->permission) && $tickets_info->permission != 'all') {
                                                    $get_permission = json_decode($tickets_info->permission);

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

                                                            if (!empty($tickets_info->permission) && $tickets_info->permission != 'all') {
                                                                $get_permission = json_decode($tickets_info->permission);

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

                                                            if (!empty($tickets_info->permission) && $tickets_info->permission != 'all') {
                                                                $get_permission = json_decode($tickets_info->permission);
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
                                    <div class="col-lg-6">
                                        <button type="submit" id="file-save-button"
                                                class="btn btn-sm btn-primary"></i> <?= lang('create_ticket') ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php } else { ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>