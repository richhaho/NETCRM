<?= message_box('success') ?>
<?= message_box('error');
$edited = can_action('14', 'edited');
$deleted = can_action('14', 'deleted');
?>
<div class="row mb">
    <div class="col-sm-12 mb">
        <div class="pull-left">
            <?= lang('copy_unique_url') ?>
        </div>
        <div class="col-sm-10">
            <input style="width: 100%"
                   value="<?= base_url() ?>frontend/estimates/<?= url_encode($estimates_info->estimates_id); ?>"
                   type="text" id="foo"/>
        </div>
    </div>
    <script type="text/javascript">
        var textBox = document.getElementById("foo");
        textBox.onfocus = function () {
            textBox.select();
            // Work around Chrome's little problem
            textBox.onmouseup = function () {
                // Prevent further mouseup intervention
                textBox.onmouseup = null;
                return false;
            };
        };
    </script>
    <div class="col-sm-8">
        <?php
        $where = array('user_id' => $this->session->userdata('user_id'), 'module_id' => $estimates_info->estimates_id, 'module_name' => 'estimates');
        $check_existing = $this->estimates_model->check_by($where, 'tbl_pinaction');
        if (!empty($check_existing)) {
            $url = 'remove_todo/' . $check_existing->pinaction_id;
            $btn = 'danger';
            $title = lang('remove_todo');
        } else {
            $url = 'add_todo_list/estimates/' . $estimates_info->estimates_id;
            $btn = 'warning';
            $title = lang('add_todo_list');
        }

        $can_edit = $this->estimates_model->can_action('tbl_estimates', 'edit', array('estimates_id' => $estimates_info->estimates_id));
        $can_delete = $this->estimates_model->can_action('tbl_estimates', 'delete', array('estimates_id' => $estimates_info->estimates_id));
        $client_info = $this->estimates_model->check_by(array('client_id' => $estimates_info->client_id), 'tbl_client');
        if (!empty($client_info)) {
            $currency = $this->estimates_model->client_currency_sambol($estimates_info->client_id);
            $client_lang = $client_info->language;
        } else {
            $client_lang = 'english';
            $currency = $this->estimates_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
        }
        unset($this->lang->is_loaded[5]);
        $language_info = $this->lang->load('sales_lang', $client_lang, TRUE, FALSE, '', TRUE);
        ?>

        <?php if (!empty($can_edit) && !empty($edited)) { ?>

            <a data-toggle="modal" data-target="#myModal_lg"
               href="<?= base_url() ?>admin/estimates/insert_items/<?= $estimates_info->estimates_id ?>"
               title="<?= lang('item_quick_add') ?>" class="btn btn-xs btn-primary">
                <i class="fa fa-pencil text-white"></i> <?= lang('add_items') ?></a>

            <?php if ($estimates_info->show_client == 'Yes') { ?>
            <a class="btn btn-xs btn-success"
               href="<?= base_url() ?>admin/estimates/change_status/hide/<?= $estimates_info->estimates_id ?>"
               title="<?= lang('hide_to_client') ?>"><i class="fa fa-eye-slash"></i> <?= lang('hide_to_client') ?>
                </a><?php } else { ?>
            <a class="btn btn-xs btn-warning"
               href="<?= base_url() ?>admin/estimates/change_status/show/<?= $estimates_info->estimates_id ?>"
               title="<?= lang('show_to_client') ?>"><i class="fa fa-eye"></i> <?= lang('show_to_client') ?>
                </a><?php } ?>

            <a data-toggle="modal" data-target="#myModal_large" data-original-title="<?= lang('convert_to_invoice') ?>"
               data-toggle="tooltip" data-placement="top"
               class="btn btn-xs btn-purple <?php
               if ($estimates_info->invoiced == 'Yes' OR $estimates_info->client_id == '0') {
                   echo "disabled";
               }
               ?>" href="<?= base_url() ?>admin/estimates/convert_to_invoice/<?= $estimates_info->estimates_id ?>"
               title="<?= lang('convert_to_invoice') ?>">
                <?= lang('convert_to_invoice') ?></a>
            <span data-toggle="tooltip" data-placement="top" title="<?= lang('clone') . ' ' . lang('estimate') ?>">
            <a data-toggle="modal" data-target="#myModal"
               href="<?= base_url() ?>admin/estimates/clone_estimate/<?= $estimates_info->estimates_id ?>"
               class="btn btn-xs btn-green">
                <i class="fa fa-copy"></i> <?= lang('clone') ?></a>
            </span>
            <?php
        }
        ?>

        <?php if (!empty($can_edit) && !empty($edited)) { ?>
            <div class="btn-group">
                <button class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                    <?= lang('more_actions') ?>
                    <span class="caret"></span></button>
                <ul class="dropdown-menu animated zoomIn">
                    <li>
                        <a href="<?= base_url() ?>admin/estimates/index/email_estimates/<?= $estimates_info->estimates_id ?>"
                           data-toggle="ajaxModal"><?= lang('email_estimate') ?></a></li>
                    <li>
                        <a href="<?= base_url() ?>admin/estimates/index/estimates_history/<?= $estimates_info->estimates_id ?>"><?= lang('estimate_history') ?></a>
                    </li>

                    <?php if ($estimates_info->status == 'expired' || $estimates_info->status == 'sent' || $estimates_info->status == 'cancelled' || $estimates_info->status == 'draft') { ?>
                        <li>
                            <a href="<?= base_url() ?>admin/estimates/change_status/pending/<?= $estimates_info->estimates_id ?>"
                               title="<?= lang('mark_as_sent') ?>"><?= lang('mark_as_pending') ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($estimates_info->status == 'draft') { ?>
                        <li>
                            <a href="<?= base_url() ?>admin/estimates/change_status/draft/<?= $estimates_info->estimates_id ?>"
                               title="<?= lang('unmark_as_draft') ?>"><?= lang('mark_as_draft') ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($estimates_info->status != 'sent' || $estimates_info->status == 'expired') { ?>
                        <li>
                            <a href="<?= base_url() ?>admin/estimates/change_status/sent/<?= $estimates_info->estimates_id ?>"
                               title="<?= lang('mark_as_sent') ?>"><?= lang('mark_as_sent') ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($estimates_info->status == 'pending' || $estimates_info->status == 'sent') { ?>
                        <li>
                            <a href="<?= base_url() ?>admin/estimates/change_status/expired/<?= $estimates_info->estimates_id ?>"
                               title="<?= lang('mark_as_sent') ?>"><?= lang('mark_as_expired') ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($estimates_info->status != 'cancelled') { ?>
                        <li>
                            <a href="<?= base_url() ?>admin/estimates/change_status/cancelled/<?= $estimates_info->estimates_id ?>"
                               title="<?= lang('mark_as_cancelled') ?>"><?= lang('mark_as_cancelled') ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($estimates_info->status == 'cancelled') { ?>
                        <li>
                            <a href="<?= base_url() ?>admin/estimates/change_status/draft/<?= $estimates_info->estimates_id ?>"
                               title="<?= lang('unmark_as_cancelled') ?>"><?= lang('unmark_as_cancelled') ?></a>
                        </li>
                    <?php } ?>
                    <li>
                        <a href="<?= base_url() ?>admin/estimates/change_status/declined/<?= $estimates_info->estimates_id ?>"><?= lang('declined') ?></a>
                    </li>
                    <li>
                        <a href="<?= base_url() ?>admin/estimates/change_status/accepted/<?= $estimates_info->estimates_id ?>"><?= lang('accepted') ?></a>
                    </li>
                </ul>
            </div>
        <?php } ?>
        <?php if ($estimates_info->invoiced == 'Yes') {
            $invoice_info = $this->db->where('invoices_id', $estimates_info->invoices_id)->get('tbl_invoices')->row();
            if (!empty($invoice_info)) {
                ?>
                <a href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $estimates_info->invoices_id ?>"
                   class="btn btn-xs btn-purple">
                    <i class="fa fa-hand-o-right"></i> <?= $invoice_info->reference_no ?></a>
            <?php }
        } ?>
        <?php
        $notified_reminder = count($this->db->where(array('module' => 'estimate', 'module_id' => $estimates_info->estimates_id, 'notified' => 'No'))->get('tbl_reminders')->result());
        ?>
        <a class="btn btn-xs btn-green" data-toggle="modal" data-target="#myModal_lg"
           href="<?= base_url() ?>admin/invoice/reminder/estimate/<?= $estimates_info->estimates_id ?>"><?= lang('reminder') ?>
            <?= !empty($notified_reminder) ? '<span class="badge ml-sm" style="border-radius: 50%">' . $notified_reminder . '</span>' : '' ?>
        </a>

        <?php
        if (!empty($estimates_info->project_id)) {
            $project_info = $this->db->where('project_id', $estimates_info->project_id)->get('tbl_project')->row();
            ?>
            <strong><?= lang('project') ?>:</strong>
            <a
                href="<?= base_url() ?>admin/projects/project_details/<?= $estimates_info->project_id ?>"
                class="">
                <?= $project_info->project_name ?>
            </a>
        <?php } ?>
    </div>
    <div class="col-sm-4 pull-right">
        <a
            href="<?= base_url() ?>admin/estimates/send_estimates_email/<?= $estimates_info->estimates_id . '/' . true ?>"
            data-toggle="tooltip" data-placement="top" title="<?= lang('send_email') ?>"
            class="btn btn-xs btn-primary pull-right">
            <i class="fa fa-envelope-o"></i>
        </a>
        <a onclick="print_estimates('print_estimates')" href="#" data-toggle="tooltip" data-placement="top" title=""
           data-original-title="Print" class="mr-sm btn btn-xs btn-danger pull-right">
            <i class="fa fa-print"></i>
        </a>

        <a href="<?= base_url() ?>admin/estimates/pdf_estimates/<?= $estimates_info->estimates_id ?>"
           data-toggle="tooltip" data-placement="top" title="" data-original-title="PDF"
           class="btn btn-xs btn-success pull-right mr-sm">
            <i class="fa fa-file-pdf-o"></i>
        </a>
        <a data-toggle="tooltip" data-placement="top" title="<?= $title ?>"
           href="<?= base_url() ?>admin/projects/<?= $url ?>"
           class="mr-sm btn pull-right  btn-xs  btn-<?= $btn ?>"><i class="fa fa-thumb-tack"></i></a>
    </div>
</div>
<!-- Start Display Details -->
<?php
if (strtotime($estimates_info->due_date) < time() AND $estimates_info->status == 'draft') {
    $start = strtotime(strftime(config_item('date_format')));
    $end = strtotime($estimates_info->due_date);

    $days_between = ceil(abs($end - $start) / 86400);
    ?>
    <div class="alert bg-danger-light hidden-print">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <i class="fa fa-warning"></i>
        <?= lang('estimate_overdue') . ' ' . lang('by') . ' ' . $days_between . ' ' . lang('days') ?>
    </div>
    <?php
}
?>
<!-- Main content -->
<div class="panel" id="print_estimates">
    <!-- title row -->
    <div class="show_print">
        <div class="col-xs-12">
            <h4 class="page-header">
                <img style="width: 60px;width: 60px;margin-top: -10px;margin-right: 10px;"
                     src="<?= base_url() . config_item('invoice_logo') ?>"><?= config_item('company_name') ?>
            </h4>
        </div><!-- /.col -->
    </div>
    <!-- info row -->
    <div class="panel-body">
        <?php if (!empty($can_edit) && !empty($edited)) { ?>
            <a href="<?= base_url() ?>admin/estimates/index/edit_estimates/<?= $estimates_info->estimates_id ?>"
               class="pull-right btn btn-primary btn-xs"><?= lang('edit_estimate') ?></a>
        <?php } ?>
        <h3 class="mt0 mb-sm"><?= $estimates_info->reference_no ?></h3>
        <hr class="m0">
        <div class="row mb-lg">
            <div class="col-lg-4 col-xs-6 br pv">
                <div class="row">
                    <div class="col-md-2 visible-md visible-lg">
                        <em class="fa fa-truck fa-4x text-muted"></em>
                    </div>
                    <div class="col-md-10">
                        <h4 class="ml-sm"><?= (config_item('company_legal_name_' . $client_lang) ? config_item('company_legal_name_' . $client_lang) : config_item('company_legal_name')) ?></h4>
                        <address></address><?= (config_item('company_address_' . $client_lang) ? config_item('company_address_' . $client_lang) : config_item('company_address')) ?>
                        <br><?= (config_item('company_city_' . $client_lang) ? config_item('company_city_' . $client_lang) : config_item('company_city')) ?>
                        , <?= config_item('company_zip') ?>
                        <br><?= (config_item('company_country_' . $client_lang) ? config_item('company_country_' . $client_lang) : config_item('company_country')) ?>
                        <br/><?= lang('phone') ?> : <?= config_item('company_phone') ?>
                        <br/><?= lang('vat_number') ?> : <?= config_item('company_vat') ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-xs-6 br pv">
                <div class="row">
                    <div class="col-md-2 visible-md visible-lg">
                        <em class="fa fa-plane fa-4x text-muted"></em>
                    </div>
                    <?php
                    if (!empty($client_info)) {
                        $client_name = $client_info->name;
                        $address = $client_info->address;
                        $city = $client_info->city;
                        $zipcode = $client_info->zipcode;
                        $country = $client_info->country;
                        $phone = $client_info->phone;
                    } else {
                        $client_name = '-';
                        $address = '-';
                        $city = '-';
                        $zipcode = '-';
                        $country = '-';
                        $phone = '-';
                    }
                    ?>
                    <div class="col-md-10">
                        <h4><?= $client_name ?></h4>
                        <address></address><?= $address ?>
                        <br> <?= $city ?>, <?= $zipcode ?>
                        <br><?= $country ?>
                        <br><?= lang('phone') ?>: <?= $phone ?>
                        <?php if (!empty($client_info->vat)) { ?>
                            <br><?= lang('vat_number') ?>: <?= $client_info->vat ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="clearfix hidden-md hidden-lg">
                <hr>
            </div>
            <div class="col-lg-4 col-xs-12 pv">
                <div class="clearfix">
                    <p class="pull-left text-uppercase"><?= lang('estimates') . ' ' . lang('no') ?></p>
                    <p class="pull-right mr"><?= $estimates_info->reference_no ?></p>
                </div>
                <div class="clearfix">
                    <p class="pull-left"><?= lang('estimate_date') ?></p>
                    <p class="pull-right mr"><?= strftime(config_item('date_format'), strtotime($estimates_info->estimate_date)); ?></p>
                </div>
                <div class="clearfix"><?php
                    if (strtotime($estimates_info->due_date) < time() AND $estimates_info->status == 'pending' || strtotime($estimates_info->due_date) < time() AND $estimates_info->status == ('draft')) {
                        $danger = 'text-danger';
                    } else {
                        $danger = null;
                    }
                    ?>
                    <p class="pull-left text-uppercase <?= $danger ?>"> <?= lang('valid_until') ?></p>
                    <p class="pull-right mr <?= $danger ?>"><?= strftime(config_item('date_format'), strtotime($estimates_info->due_date)); ?></p>
                </div>
                <?php if (!empty($estimates_info->user_id)) { ?>
                    <div class="clearfix">
                        <p class="pull-left"><?= lang('sales') . ' ' . lang('agent') ?></p>
                        <p class="pull-right mr"><?php

                            $profile_info = $this->db->where('user_id', $estimates_info->user_id)->get('tbl_account_details')->row();
                            if (!empty($profile_info)) {
                                echo $profile_info->fullname;
                            }
                            ?></p>
                    </div>
                <?php } ?>
                <div class="clearfix">
                    <?php
                    if ($estimates_info->status == 'accepted') {
                        $label = 'success';
                    } else {
                        $label = 'danger';
                    }
                    ?>
                    <p class="pull-left "><?= lang('estimate_status') ?></p>
                    <p class="pull-right mr"><span
                            class="label label-<?= $label ?>"><?= lang($estimates_info->status) ?></span></p>
                </div>
                <?php $show_custom_fields = custom_form_label(10, $estimates_info->estimates_id);

                if (!empty($show_custom_fields)) {
                    foreach ($show_custom_fields as $c_label => $v_fields) {
                        if (!empty($v_fields)) {
                            ?>
                            <div class="clearfix">
                                <p class="pull-left"><?= $c_label ?></p>
                                <p class="pull-right mr"><?= $v_fields ?></p>

                            </div>
                        <?php }
                    }
                }
                ?>
            </div>
        </div><!-- /.row -->
        <style type="text/css">
            .dragger {
                background: url(../../../../assets/img/dragger.png) 0px 11px no-repeat;
                cursor: pointer;
            }

            .table > tbody > tr > td {
                vertical-align: initial;
            }
        </style>
        <div class="table-responsive mb-lg " style="margin-top: 25px">
            <table class="table items estimate-items-preview" page-break-inside: auto;>
                <thead style="background: #3a3f51;color: #fff;">
                <tr>
                    <th>#</th>
                    <th><?= lang('items') ?></th>
                    <?php
                    $invoice_view = config_item('invoice_view');
                    if (!empty($invoice_view) && $invoice_view == '2') {
                        ?>
                        <th><?= lang('hsn_code') ?></th>
                    <?php } ?>
                    <?php
                    $qty_heading = lang('qty'];
                    if (isset($estimates_info) && $estimates_info->show_quantity_as == 'hours' || isset($hours_quantity)) {
                        $qty_heading = lang('hours');
                    } else if (isset($estimates_info) && $estimates_info->show_quantity_as == 'qty_hours') {
                        $qty_heading = lang('qty') . '/' . lang('hours');
                    }
                    ?>
                    <th><?php echo $qty_heading; ?></th>
                    <th class="col-sm-1"><?= lang('price') ?></th>
                    <th class="col-sm-2"><?= lang('tax') ?></th>
                    <th class="col-sm-1"><?= lang('total') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $invoice_items = $this->estimates_model->ordered_items_by_id($estimates_info->estimates_id);

                if (!empty($invoice_items)) :
                    foreach ($invoice_items as $key => $v_item) :
                        $item_name = $v_item->item_name ? $v_item->item_name : $v_item->item_desc;
                        $item_tax_name = json_decode($v_item->item_tax_name);
                        ?>
                        <tr class="sortable item" data-item-id="<?= $v_item->estimate_items_id ?>">
                            <td class="item_no dragger pl-lg"><?= $key + 1 ?></td>
                            <td><strong class="block"><?= $item_name ?></strong>
                                <?= nl2br($v_item->item_desc) ?>
                            </td>
                            <?php
                            $invoice_view = config_item('invoice_view');
                            if (!empty($invoice_view) && $invoice_view == '2') {
                                ?>
                                <td><?= $v_item->hsn_code ?></td>
                            <?php } ?>
                            <td><?= $v_item->quantity . '   &nbsp' . $v_item->unit ?></td>
                            <td><?= display_money($v_item->unit_cost) ?></td>
                            <td><?php
                                if (!empty($item_tax_name)) {
                                    foreach ($item_tax_name as $v_tax_name) {
                                        $i_tax_name = explode('|', $v_tax_name);
                                        echo '<small class="pr-sm">' . $i_tax_name[0] . ' (' . $i_tax_name[1] . ' %)' . '</small>' . display_money($v_item->total_cost / 100 * $i_tax_name[1]) . ' <br>';
                                    }
                                }
                                ?></td>
                            <td><?= display_money($v_item->total_cost) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8"><?= lang('nothing_to_display') ?></td>
                    </tr>
                <?php endif ?>
                </tbody>
            </table>
        </div>
        <div class="row" style="margin-top: 35px">
            <div class="col-xs-8">
                <p class="well well-sm mt">
                    <?= $estimates_info->notes ?>
                </p>
            </div>
            <div class="col-sm-4 pv">
                <div class="clearfix">
                    <p class="pull-left"><?= lang('sub_total') ?></p>
                    <p class="pull-right mr">
                        <?= display_money($this->estimates_model->estimate_calculation('estimate_cost', $estimates_info->estimates_id)); ?>
                    </p>
                </div>
                <?php if ($estimates_info->discount_total > 0): ?>
                    <div class="clearfix">
                        <p class="pull-left"><?= lang('discount') ?>
                            (<?php echo $estimates_info->discount_percent; ?>
                            %)</p>
                        <p class="pull-right mr">
                            <?= display_money($this->estimates_model->estimate_calculation('discount', $estimates_info->estimates_id)); ?>
                        </p>
                    </div>
                <?php endif ?>
                <?php
                $tax_info = json_decode($estimates_info->total_tax);
                $tax_total = 0;
                if (!empty($tax_info)) {
                    $tax_name = $tax_info->tax_name;
                    $total_tax = $tax_info->total_tax;
                    if (!empty($tax_name)) {
                        foreach ($tax_name as $t_key => $v_tax_info) {
                            $tax = explode('|', $v_tax_info);
                            $tax_total += $total_tax[$t_key];
                            ?>
                            <div class="clearfix">
                                <p class="pull-left"><?= $tax[0] . ' (' . $tax[1] . ' %)' ?></p>
                                <p class="pull-right mr">
                                    <?= display_money($total_tax[$t_key]); ?>
                                </p>
                            </div>
                        <?php }
                    }
                } ?>
                <?php if ($tax_total > 0): ?>
                    <div class="clearfix">
                        <p class="pull-left"><?= lang('total'] . ' ' . lang('tax') ?></p>
                        <p class="pull-right mr">
                            <?= display_money($tax_total); ?>
                        </p>
                    </div>
                <?php endif ?>
                <?php if ($estimates_info->adjustment > 0): ?>
                    <div class="clearfix">
                        <p class="pull-left"><?= lang('adjustment') ?></p>
                        <p class="pull-right mr">
                            <?= display_money($estimates_info->adjustment); ?>
                        </p>
                    </div>
                <?php endif ?>

                <div class="clearfix">
                    <p class="pull-left"><?= lang('total') ?></p>
                    <p class="pull-right mr">
                        <?php
                        $total_amount = $this->estimates_model->estimate_calculation('total', $estimates_info->estimates_id);
                        echo display_money($total_amount, $currency->symbol);
                        ?>
                    </p>
                </div>

            </div>
        </div>
    </div>
    <?= !empty($invoice_view) && $invoice_view > 0 ? $this->gst->summary($invoice_items) : ''; ?>
</div>

<?php include_once 'assets/js/sales.php'; ?>
<script type="text/javascript">
    $(document).ready(function () {
        init_items_sortable(true);
    });
    function print_estimates(print_estimates) {
        var printContents = document.getElementById(print_estimates).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>