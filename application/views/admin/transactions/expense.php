<?= message_box('success'); ?>
<?= message_box('error'); ?>
<?php
$mdate = strftime(config_item('date_format'));
$last_7_days = strftime(config_item('date_format'), strtotime('today - 7 days'));
$all_goal_tracking = $this->transactions_model->get_permission('tbl_goal_tracking');

$all_goal = 0;
$bank_goal = 0;
$complete_achivement = 0;
if (!empty($all_goal_tracking)) {
    foreach ($all_goal_tracking as $v_goal_track) {
        $goal_achieve = $this->transactions_model->get_progress($v_goal_track, true);
        if ($v_goal_track->goal_type_id == 3) {
            if ($v_goal_track->end_date <= $mdate) { // check today is last date or not

                if ($v_goal_track->email_send == 'no') {// check mail are send or not
                    if ($v_goal_track->achievement <= $goal_achieve['achievement']) {
                        if ($v_goal_track->notify_goal_achive == 'on') {// check is notify is checked or not check
                            $this->transactions_model->send_goal_mail('goal_achieve', $v_goal_track);
                        }
                    } else {
                        if ($v_goal_track->notify_goal_not_achive == 'on') {// check is notify is checked or not check
                            $this->transactions_model->send_goal_mail('goal_not_achieve', $v_goal_track);
                        }
                    }
                }
            }
            $all_goal += $v_goal_track->achievement;
            $complete_achivement += $goal_achieve['achievement'];
        }
        if ($v_goal_track->goal_type_id == 4) {
            if ($v_goal_track->end_date <= $mdate) { // check today is last date or not

                if ($v_goal_track->email_send == 'no') {// check mail are send or not
                    if ($v_goal_track->achievement <= $goal_achieve['achievement']) {
                        if ($v_goal_track->notify_goal_achive == 'on') {// check is notify is checked or not check
                            $this->transactions_model->send_goal_mail('goal_achieve', $v_goal_track);
                        }
                    } else {
                        if ($v_goal_track->notify_goal_not_achive == 'on') {// check is notify is checked or not check
                            $this->transactions_model->send_goal_mail('goal_not_achieve', $v_goal_track);
                        }
                    }
                }
            }

            $bank_goal += $v_goal_track->achievement;
            $complete_achivement += $goal_achieve['achievement'];
        }

    }
}
// 30 days before

for ($iDay = 7; $iDay >= 0; $iDay--) {
    $date = strftime(config_item('date_format'), strtotime('today - ' . $iDay . 'days'));
    $this_7_days_deposit[$date] = $this->db->select_sum('amount')->where(array('type' => 'Expense', 'date >=' => $date, 'date <=' => $date))->get('tbl_transactions')->result();
}

$this_7_days_all = $this->db->where(array('goal_type_id' => 3, 'start_date >=' => $last_7_days, 'end_date <=' => $mdate))->get('tbl_goal_tracking')->result();

$this_7_days_bank = $this->db->where(array('goal_type_id' => 4, 'start_date >=' => $last_7_days, 'end_date <=' => $mdate))->get('tbl_goal_tracking')->result();

if (!empty($this_7_days_all)) {
    $this_7_days_all = $this_7_days_all;
} else {
    $this_7_days_all = array();
}
if (!empty($this_7_days_bank)) {
    $this_7_days_bank = $this_7_days_bank;
} else {
    $this_7_days_bank = array();
}


$terget_achievement = array_merge($this_7_days_all, $this_7_days_bank);
$total_terget = 0;
if (!empty($terget_achievement)) {
    foreach ($terget_achievement as $v_terget) {
        $total_terget += $v_terget->achievement;
    }
}
$tolal_goal = $all_goal + $bank_goal;
$curency = $this->transactions_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');

if ($this->session->userdata('user_type') == 1) {
    $margin = 'margin-bottom:30px';
    ?>
    <div class="col-sm-12 bg-white p0" style="<?= $margin ?>">
        <div class="col-md-4">
            <div class="row row-table pv-lg">
                <div class="col-xs-6">
                    <p class="m0 lead"><?= display_money($tolal_goal, $curency->symbol) ?></p>
                    <p class="m0">
                        <small><?= lang('achievement') ?></small>
                    </p>
                </div>
                <div class="col-xs-6 ">
                    <p class="m0 lead"><?= display_money($total_terget, $curency->symbol) ?></p>
                    <p class="m0">
                        <small><?= lang('last_weeks') . ' ' . lang('created') ?></small>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row row-table ">
                <div class="col-xs-6">
                    <p class="m0 lead"><?= display_money($complete_achivement, $curency->symbol) ?></p>
                    <p class="m0">
                        <small><?= lang('completed') . ' ' . lang('achievements') ?></small>
                    </p>
                </div>
                <div class="col-xs-6 pt">
                    <div data-sparkline="" data-bar-color="#23b7e5" data-height="60" data-bar-width="8"
                         data-bar-spacing="6" data-chart-range-min="0" values="<?php
                    if (!empty($this_7_days_deposit)) {
                        foreach ($this_7_days_deposit as $v_last_deposit) {
                            echo $v_last_deposit[0]->amount . ',';
                        }
                    }
                    ?>">
                    </div>
                    <p class="m0">
                        <small>
                            <?php
                            if (!empty($this_7_days_deposit)) {
                                foreach ($this_7_days_deposit as $date => $v_last_deposit) {
                                    echo date('d', strtotime($date)) . ' ';
                                }
                            }
                            ?>
                        </small>
                    </p>

                </div>
            </div>

        </div>
        <div class="col-md-4">
            <div class="row row-table ">
                <div class="col-xs-6">
                    <p class="m0 lead">
                        <?php
                        if ($tolal_goal < $complete_achivement) {
                            $pending_goal = 0;
                        } else {
                            $pending_goal = $tolal_goal - $complete_achivement;
                        } ?>
                        <?= display_money($pending_goal, $curency->symbol) ?>
                    </p>
                    <p class="m0">
                        <small><?= lang('pending') . ' ' . lang('achievements') ?></small>
                    </p>
                </div>
                <?php
                if (!empty($tolal_goal)) {
                    if ($tolal_goal <= $complete_achivement) {
                        $total_progress = 100;
                    } else {
                        $progress = ($complete_achivement / $tolal_goal) * 100;
                        $total_progress = round($progress);
                    }
                } else {
                    $total_progress = 0;
                }
                ?>
                <div class="col-xs-6 text-center pt">
                    <div class="inline ">
                        <div class="easypiechart text-success"
                             data-percent="<?= $total_progress ?>"
                             data-line-width="5" data-track-Color="#f0f0f0"
                             data-bar-color="#<?php
                             if ($total_progress == 100) {
                                 echo '8ec165';
                             } elseif ($total_progress >= 40 && $total_progress <= 50) {
                                 echo '5d9cec';
                             } elseif ($total_progress >= 51 && $total_progress <= 99) {
                                 echo '7266ba';
                             } else {
                                 echo 'fb6b5b';
                             }
                             ?>" data-rotate="270" data-scale-Color="false"
                             data-size="50"
                             data-animate="2000">
                                                        <span class="small "><?= $total_progress ?>
                                                            %</span>
                            <span class="easypie-text"><strong><?= lang('done') ?></strong></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php }
$created = can_action('31', 'created');
$edited = can_action('31', 'edited');
$deleted = can_action('31', 'deleted');
$expense_category = $this->db->get('tbl_expense_category')->result();
$id = $this->uri->segment(5);
if (!empty($created) || !empty($edited)){
?>
<div class="row">
    <div class="col-sm-12">
        <div class="btn-group pull-right btn-with-tooltip-group" data-toggle="tooltip"
             data-title="<?php echo lang('filter_by'); ?>">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-filter" aria-hidden="true"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-left" style="width:300px;<?php if (!empty($search_by) && $search_by == 'category') { echo 'display:block';}?>">
                <li class="<?php
                if (empty($search_by)) {
                    echo 'active';
                } ?>"><a
                        href="<?= base_url() ?>admin/transactions/expense"><?php echo lang('all'); ?></a>
                </li>
                <li class="divider"></li>
                <?php if (count($expense_category) > 0) { ?>
                    <?php foreach ($expense_category as $v_category) {
                        ?>
                        <li class="<?php if (!empty($id)) {
                            if ($search_by == 'category') {
                                if ($id == $v_category->expense_category_id) {
                                    echo 'active';
                                }
                            }
                        } ?>">
                            <a href="<?= base_url() ?>admin/transactions/expense/category/<?php echo $v_category->expense_category_id; ?>"><?php echo $v_category->expense_category; ?></a>
                        </li>
                    <?php }
                    ?>
                    <div class="clearfix"></div>
                    <li class="divider"></li>
                <?php } ?>
            </ul>
        </div>
        <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs">
                <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                                    data-toggle="tab"><?= lang('all_expense') ?></a>
                </li>
                <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create"
                                                                    data-toggle="tab"><?= lang('new_expense') ?></a>
                </li>
                <li><a style="background-color: #1797be;color: #ffffff"
                       href="<?= base_url() ?>admin/transactions/import/Expense"><?= lang('import') . ' ' . lang('expense') ?></a>
                </li>
            </ul>
            <div class="tab-content bg-white">
                <!-- ************** general *************-->
                <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
                    <?php } else { ?>
                    <div class="panel panel-custom">
                        <header class="panel-heading ">
                            <div class="panel-title"><strong><?= lang('all_expense') ?></strong></div>
                        </header>
                        <?php } ?>
                        <div class="table-responsive">
                            <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th><?= lang('name') . '/' . lang('title') ?></th>
                                    <th><?= lang('date') ?></th>
                                    <th><?= lang('account_name') ?></th>
                                    <th class="col-currency"><?= lang('amount') ?></th>
                                    <th><?= lang('status') ?></th>
                                    <?php $show_custom_fields = custom_form_table(2, null);
                                    if (!empty($show_custom_fields)) {
                                        foreach ($show_custom_fields as $c_label => $v_fields) {
                                            if (!empty($c_label)) {
                                                ?>
                                                <th><?= $c_label ?> </th>
                                            <?php }
                                        }
                                    }
                                    ?>
                                    <th><?= lang('attachment') ?></th>
                                    <th><?= lang('action') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (!empty($all_expense_info)):
                                    foreach ($all_expense_info as $v_expense) :
                                        if ($v_expense->type == 'Expense'):
                                            $can_edit = $this->transactions_model->can_action('tbl_transactions', 'edit', array('transactions_id' => $v_expense->transactions_id));
                                            $can_delete = $this->transactions_model->can_action('tbl_transactions', 'delete', array('transactions_id' => $v_expense->transactions_id));

                                            $account_info = $this->transactions_model->check_by(array('account_id' => $v_expense->account_id), 'tbl_accounts');
                                            $curency = $this->transactions_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                                            ?>
                                            <tr id="table_expense_<?= $v_expense->transactions_id ?>">
                                                <td>
                                                    <a data-toggle="modal" data-target="#myModal"
                                                       href="<?= base_url() ?>admin/transactions/view_expense/<?= $v_expense->transactions_id ?>">
                                                        <?= ($v_expense->name ? $v_expense->name : '-'); ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <a data-toggle="modal" data-target="#myModal"
                                                       href="<?= base_url() ?>admin/transactions/view_expense/<?= $v_expense->transactions_id ?>">
                                                        <?= strftime(config_item('date_format'), strtotime($v_expense->date)); ?>
                                                    </a>
                                                </td>
                                                <td class="vertical-td"><?php
                                                    if (!empty($account_info->account_name)) {
                                                        echo $account_info->account_name;
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?></td>
                                                <td><?= display_money($v_expense->amount, $curency->symbol) ?></td>
                                                <td><?php
                                                    $status = $v_expense->status;
                                                    $text = '';
                                                    if ($v_expense->project_id != 0) {
                                                        if ($v_expense->billable == 'No') {
                                                            $status = 'not_billable';
                                                            $label = 'primary';
                                                            $title = lang('not_billable');
                                                            $action = '';
                                                        } else {
                                                            $status = 'billable';
                                                            $label = 'success';
                                                            $title = lang('billable');
                                                            $action = '';
                                                            if ($v_expense->invoices_id != 0) {
                                                                $payment_status = $this->invoice_model->get_payment_status($v_expense->invoices_id);
                                                                $text = '<a href="' . base_url() . 'admin/invoice/manage_invoice/invoice_details/' . $v_expense->invoices_id . '"> <p class="text-sm m0 p0"><span class="text-dark">' . lang('invoiced') . '</span><span class="text-danger">' . ' ' . $payment_status . '</span></p></a>';
                                                            } else {
                                                                $text = '<p class="text-sm m0 p0"><span class="text-danger">' . lang('not_invoiced') . '</span></p>';
                                                            }
                                                        }
                                                    } else {
                                                        if ($v_expense->status == 'non_approved') {
                                                            $label = 'danger';
                                                            $title = lang('get_approved');
                                                            $action = 'approved';
                                                        } elseif ($v_expense->status == 'unpaid') {
                                                            $label = 'warning';
                                                            $title = lang('get_paid');
                                                            $action = 'paid';
                                                        } else {
                                                            $label = 'success';
                                                            $title = '';
                                                            $action = '';
                                                        }
                                                    }
                                                    $check_head = $this->db->where('department_head_id', $this->session->userdata('user_id'))->get('tbl_departments')->row();
                                                    $role = $this->session->userdata('user_type');
                                                    if ($role == 1 || !empty($check_head)) {
                                                        ?>
                                                        <a data-toggle="tooltip" data-placement="top"
                                                           title="<?= $title ?>"
                                                           class="label label-<?= $label ?>"
                                                           href="
                                               <?php
                                                           if (!empty($action)) {
                                                               echo base_url() ?>admin/transactions/set_status/<?= $action . '/' . $v_expense->transactions_id;
                                                           } else {
                                                               echo '#';
                                                           }
                                                           ?>">
                                                            <?= lang($status) ?>

                                                        </a>

                                                        <?= $text ?>

                                                    <?php } else { ?>
                                                        <span class="label label-<?= $label ?>">
                                                <?= lang($status); ?>
                                            </span>
                                                        <?= $text ?>
                                                    <?php } ?>
                                                </td>
                                                <?php $show_custom_fields = custom_form_table(2, $v_expense->transactions_id);
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
                                                    <?php
                                                    $attachement_info = json_decode($v_expense->attachement);
                                                    if (!empty($attachement_info)) { ?>
                                                        <a href="<?= base_url() ?>admin/transactions/download/<?= $v_expense->transactions_id ?>"><?= lang('download') ?></a>
                                                    <?php } ?>
                                                </td>
                                                <td class="">
                                                    <a data-toggle="modal" data-target="#myModal"
                                                       class="btn btn-info btn-xs"
                                                       href="<?= base_url() ?>admin/transactions/view_expense/<?= $v_expense->transactions_id ?>">
                                                        <span class="fa fa-list-alt"></span>
                                                    </a>
                                                    <?php if (!empty($edited) || !empty($deleted)) { ?>
                                                        <?php if (!empty($edited) && !empty($can_edit)) { ?>
                                                            <?= btn_edit('admin/transactions/expense/' . $v_expense->transactions_id) ?>
                                                        <?php }
                                                        if (!empty($deleted) && !empty($can_delete)) {
                                                            ?>
                                                            <?php echo ajax_anchor(base_url("admin/transactions/delete_expense/$v_expense->transactions_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_expense_" .$v_expense->transactions_id)); ?>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php
                                        endif;
                                    endforeach;
                                endif;
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if (!empty($created) || !empty($edited)) { ?>
                        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
                            <form role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data"
                                  id="form"
                                  action="<?php echo base_url(); ?>admin/transactions/save_expense/<?php
                                  if (!empty($expense_info)) {
                                      echo $expense_info->transactions_id;
                                  }
                                  ?>" method="post" class="form-horizontal  ">
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('name') . '/' . lang('title') ?></label>
                                    <div class="col-lg-5">
                                        <input type="text" required
                                               placeholder="<?= lang('enter') . ' ' . lang('name') . '/' . lang('title') . ' ' . lang('for_personal') ?>"
                                               name="name" class="form-control" value="<?php
                                        if (!empty($expense_info->name)) {
                                            echo $expense_info->name;
                                        } ?>">
                                    </div>
                                </div>
                                <?php $project_id = $this->uri->segment(5);
                                if (!empty($expense_info->project_id)) {
                                    $project_id = $expense_info->project_id;
                                }
                                $project = $this->db->where('project_id', $project_id)->get('tbl_project')->row();
                                if (!empty($project)) {
                                    ?>
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('project') ?></label>
                                        <div class="col-lg-5">
                                            <select class="form-control select_box" style="width: 100%"
                                                    name="project_id">
                                                <option
                                                    value="<?php echo $project_id; ?>"><?= $project->project_name ?></option>
                                            </select>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('account') ?> <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-5">
                                        <select class="form-control select_box" style="width: 100%" name="account_id"
                                                required <?php
                                        if (!empty($expense_info) && $expense_info->account_id != '0') {
                                            echo 'disabled';
                                        }
                                        ?>>
                                            <?php
                                            $account_info = $this->db->get('tbl_accounts')->result();
                                            if (!empty($account_info)) {
                                                foreach ($account_info as $v_account) {
                                                    ?>
                                                    <option value="<?= $v_account->account_id ?>"
                                                        <?php
                                                        if (!empty($expense_info)) {
                                                            echo $expense_info->account_id == $v_account->account_id ? 'selected' : '';
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
                                            if (!empty($expense_info->date)) {
                                                echo $expense_info->date;
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
                            if (!empty($expense_info)) {
                                echo $expense_info->notes;
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
                                            <input class="form-control " data-parsley-type="number" type="text"
                                                   value="<?php
                                                   if (!empty($expense_info)) {
                                                       echo $expense_info->amount;
                                                   }
                                                   ?>" name="amount" required="" <?php
                                            if (!empty($expense_info)) {
                                                echo 'disabled';
                                            }
                                            ?>>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($expense_info)) { ?>
                                    <input class="form-control " type="hidden"
                                           value="<?php echo $expense_info->amount; ?>"
                                           name="amount">
                                <?php } ?>
                                <div class="more_option">
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('deposit_category') ?> </label>
                                        <div class="col-lg-5">
                                            <select <?php
                                            if (!empty($project)) {
                                                echo 'required=""';
                                            }
                                            ?> class="form-control select_box" style="width: 100%" name="category_id">
                                                <option value="0"><?= lang('none') ?></option>
                                                <?php
                                                $category_info = $this->db->get('tbl_expense_category')->result();
                                                if (!empty($category_info)) {
                                                    foreach ($category_info as $v_category) {
                                                        ?>
                                                        <option value="<?= $v_category->expense_category_id ?>"
                                                            <?php
                                                            if (!empty($expense_info->category_id)) {
                                                                echo $expense_info->category_id == $v_category->expense_category_id ? 'selected' : '';
                                                            }
                                                            ?>
                                                        ><?= $v_category->expense_category ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('paid_by') ?> </label>
                                        <div class="col-lg-5">
                                            <select class="form-control select_box" style="width: 100%" name="paid_by">
                                                <?php $all_client = $this->db->get('tbl_client')->result();
                                                if (!empty($project)) {
                                                    $client_name = $this->db->where('client_id', $project->client_id)->get('tbl_client')->row();
                                                    ?>
                                                    <option
                                                        value="<?= $project->client_id ?>"><?= $client_name->name ?></option>
                                                <?php } else { ?>
                                                    <option value="0"><?= lang('select_payer') ?></option>
                                                    <?php if (!empty($all_client)) {
                                                        foreach ($all_client as $v_client) {
                                                            ?>
                                                            <option value="<?= $v_client->client_id ?>"
                                                                <?php
                                                                if (!empty($expense_info)) {
                                                                    echo $expense_info->paid_by == $v_client->client_id ? 'selected' : '';
                                                                }
                                                                ?>
                                                            ><?= ucfirst($v_client->name); ?></option>
                                                            <?php
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('payment_method') ?> </label>
                                        <div class="col-lg-5">
                                            <select class="form-control select_box" style="width: 100%"
                                                    name="payment_methods_id">
                                                <option value="0"><?= lang('select_payment_method') ?></option>
                                                <?php
                                                $payment_methods = $this->db->get('tbl_payment_methods')->result();
                                                if (!empty($payment_methods)) {
                                                    foreach ($payment_methods as $p_method) {
                                                        ?>
                                                        <option value="<?= $p_method->payment_methods_id ?>" <?php
                                                        if (!empty($expense_info)) {
                                                            echo $expense_info->payment_methods_id == $p_method->payment_methods_id ? 'selected' : '';
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
                                            if (!empty($expense_info)) {
                                                echo $expense_info->reference;
                                            }
                                            ?>" name="reference">
                                            <span class="help-block"><?= lang('reference_example') ?></span>
                                        </div>
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
                                        if (!empty($expense_info->attachement)) {
                                            $uploaded_file = json_decode($expense_info->attachement);
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
                                <?php
                                if (!empty($expense_info)) {
                                    $transactions_id = $expense_info->transactions_id;
                                } else {
                                    $transactions_id = null;
                                }
                                ?>
                                <?= custom_form_Fields(2, $transactions_id); ?>
                                <input class="form-control " type="hidden" value="<?php
                                if (!empty($expense_info)) {
                                    echo $expense_info->account_id;
                                }
                                ?>" name="old_account_id">
                                <div class="form-group" id="border-none">
                                    <label for="field-1" class="col-sm-3 control-label"><?= lang('permission') ?> <span
                                            class="required">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="checkbox c-radio needsclick">
                                            <label class="needsclick">
                                                <input id="" <?php
                                                if (!empty($expense_info->permission) && $expense_info->permission == 'all') {
                                                    echo 'checked';
                                                } elseif (empty($expense_info)) {
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
                                                if (!empty($expense_info->permission) && $expense_info->permission != 'all') {
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
                                <?php if (!empty($project_id)): ?>

                                    <div class="form-group">
                                        <label for="field-1"
                                               class="col-sm-3 control-label"><?= lang('billable') ?>
                                            <span class="required">*</span></label>
                                        <div class="col-sm-8">
                                            <input data-toggle="toggle" name="billable" value="Yes" <?php
                                            if (!empty($expense_info) && $expense_info->billable == 'Yes') {
                                                echo 'checked';
                                            }
                                            ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                                   data-onstyle="success" data-offstyle="danger" type="checkbox">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="field-1"
                                               class="col-sm-3 control-label"><?= lang('visible_to_client') ?>
                                            <span class="required">*</span></label>
                                        <div class="col-sm-8">
                                            <input data-toggle="toggle" name="client_visible" value="Yes" <?php
                                            if (!empty($expense_info) && $expense_info->client_visible == 'Yes') {
                                                echo 'checked';
                                            }
                                            ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                                   data-onstyle="success" data-offstyle="danger" type="checkbox">
                                        </div>
                                    </div>
                                <?php endif ?>

                                <div class="form-group <?php
                                if (!empty($expense_info->permission) && $expense_info->permission != 'all') {
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
                                                            if (!empty($expense_info->permission) && $expense_info->permission != 'all') {
                                                                $get_permission = json_decode($expense_info->permission);
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

                                                if (!empty($expense_info->permission) && $expense_info->permission != 'all') {
                                                    $get_permission = json_decode($expense_info->permission);

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

                                                            if (!empty($expense_info->permission) && $expense_info->permission != 'all') {
                                                                $get_permission = json_decode($expense_info->permission);

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

                                                            if (!empty($expense_info->permission) && $expense_info->permission != 'all') {
                                                                $get_permission = json_decode($expense_info->permission);
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
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var maxAppend = 0;
        $("#add_more").click(function () {
            var add_new = $('<div class="form-group" style="margin-bottom: 0px">\n\
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('attachment') ?></label>\n\
        <div class="col-sm-4">\n\
        <div class="fileinput fileinput-new" data-provides="fileinput">\n\
<span class="btn btn-default btn-file"><span class="fileinput-new" >Select file</span><span class="fileinput-exists" >Change</span><input type="file" name="attachement[]" ></span> <span class="fileinput-filename"></span><a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a></div></div>\n\<div class="col-sm-2">\n\<strong>\n\
<a href="javascript:void(0);" class="remCF"><i class="fa fa-times"></i>&nbsp;Remove</a></strong></div>');
            maxAppend++;
            $("#add_new").append(add_new);
        });

        $("#add_new").on('click', '.remCF', function () {
            $(this).parent().parent().parent().remove();
        });
        $('a.RCF').click(function () {
            $(this).parent().parent().remove();
        });
    });
</script>