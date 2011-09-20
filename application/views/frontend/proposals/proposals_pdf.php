<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= lang('proposal') ?></title>
    <style type="text/css">
        @font-face {
            font-family: "Source Sans Pro", sans-serif;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #0087C3;
            text-decoration: none;
        }

        body {
            color: #555555;
            background: #FFFFFF;
            font-size: 14px;
            font-family: "Source Sans Pro", sans-serif;
        }

        header {

            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #AAAAAA;
        }

        #logo {
            float: left;
        }

        #company {
            float: right;
            text-align: right;
        }

        #details {
            margin-bottom: 50px;
        }

        #client {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
            float: left;
        }

        #client .to {
            color: #777777;
        }

        h2.name {
            font-size: 1em;
            font-weight: normal;
            margin: 0;
        }

        #invoice {
            float: right;
            text-align: right;
        }

        #invoice h1 {
            color: #0087C3;
            font-size: 1.5em;
            line-height: 1em;
            font-weight: normal;
        }

        #invoice .date {
            font-size: 1.1em;
            color: #777777;
        }

        table {
            width: 100%;
            border-spacing: 0;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 10px;
        }

        table.items th,
        table.items td {
            padding: 8px;
            background: #EEEEEE;
            border-bottom: 1px solid #FFFFFF;
            text-align: left;
        }

        table.items th {
            white-space: nowrap;
            font-weight: normal;
        }

        table.items td {
            text-align: left;
        }

        table.items td h3 {
            color: #57B223;
            font-size: 1em;
            font-weight: normal;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        table.items .no {
            background: #DDDDDD;
        }

        table.items .desc {
            text-align: left;
        }

        table.items .unit {
            background: #DDDDDD;
        }

        table.items .qty {
        }

        table.items td.unit,
        table.items td.qty,
        table.items td.total {
            font-size: 1em;
        }

        table.items tbody tr:last-child td {
            border: none;

        }

        table.items tfoot td {
            padding: 10px 20px;
            background: #FFFFFF;
            border-bottom: none;
            font-size: 1.2em;
            white-space: nowrap;
            border-top: 1px solid #AAAAAA;
        }

        table.items tfoot tr:first-child td {
            border-top: none;
        }

        table.items tfoot tr:last-child td {
            color: #57B223;
            font-size: 1.4em;
            border-top: 1px solid #57B223;

        }

        table.items tfoot tr td:first-child {
            border: none;
            text-align: right;
        }

        #thanks {
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        #notices {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
        }

        #notices .notice {
            font-size: 1em;
            color: #777;
        }

        footer {
            color: #777777;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #AAAAAA;
            padding: 8px 0;
            text-align: center;
        }

        tr.total td, tr th.total, tr td.total {
            text-align: right;
        }

    </style>
</head>
<body>

<?php
if ($proposals_info->module == 'client') {
    $client_info = $this->proposal_model->check_by(array('client_id' => $proposals_info->module_id), 'tbl_client');
    $currency = $this->proposal_model->client_currency_sambol($proposals_info->module_id);
    $client_lang = $client_info->language;
} else if ($proposals_info->module == 'leads') {
    $client_info = $this->proposal_model->check_by(array('leads_id' => $proposals_info->module_id), 'tbl_leads');
    $client_info->name = $client_info->lead_name;
    $client_info->zipcode = null;
    $client_lang = 'english';
    $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
} else {
    $client_lang = 'english';
    $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
}
unset($this->lang->is_loaded[5]);
$language_info = $this->lang->load('sales_lang', $client_lang, TRUE, FALSE, '', TRUE);

?>
<table class="clearfix">
    <tr>
        <td><
            <div id="logo" style="margin-top: 8px;">
                <img style=" width: 70px;" src="<?= base_url() . config_item('invoice_logo') ?>">
            </div>
        </td>
        <td>
            <div id="company">
                <h2 class="name"><?= (config_item('company_legal_name_' . $client_lang) ? config_item('company_legal_name_' . $client_lang) : config_item('company_legal_name')) ?></h2>
                <div><?= (config_item('company_address_' . $client_lang) ? config_item('company_address_' . $client_lang) : config_item('company_address')) ?></div>
                <div><?= (config_item('company_city_' . $client_lang) ? config_item('company_city_' . $client_lang) : config_item('company_city')) ?>
                    , <?= config_item('company_zip') ?></div>
                <div><?= (config_item('company_country_' . $client_lang) ? config_item('company_country_' . $client_lang) : config_item('company_country')) ?></div>
                <div> <?= config_item('company_phone') ?></div>
                <div><a href="mailto:<?= config_item('company_email') ?>"><?= config_item('company_email') ?></a>
                </div>
            </div>
        </td>
    </tr>
</table>
<?php
if (!empty($client_info)) {
    $client_name = $client_info->name;
    $address = $client_info->address;
    $city = $client_info->city;
    $zipcode = $client_info->zipcode;
    $country = $client_info->country;
    $phone = $client_info->phone;
    $email = $client_info->email;
} else {
    $client_name = '-';
    $address = '-';
    $city = '-';
    $zipcode = '-';
    $country = '-';
    $phone = '-';
    $email = '-';
}
?>

<table id="details" class="clearfix">
    <tr>
        <td><
            <div id="client">
                <h2 class="name"><?= $client_name ?></h2>
                <div class="address"><?= $address ?></div>
                <div class="address"><?= $city ?>, <?= $zipcode ?>
                    ,<?= $country ?></div>
                <div class="address"><?= $phone ?></div>
                <div class="email"><?= $email ?></div>
            </div>
        </td>
        <td>
            <div id="invoice">
                <h1><?= $proposals_info->reference_no ?></h1>
                <div class="date"><?= lang('proposal_date') ?>
                    :<?= strftime(config_item('date_format'), strtotime($proposals_info->proposal_date)); ?></div>
                <div class="date"><?= lang('valid_until') ?>
                    :<?= strftime(config_item('date_format'), strtotime($proposals_info->due_date)); ?></div>
                <div class="date"><?= lang('status') ?>: <?= lang($proposals_info->status) ?></div>
                <?php if (!empty($proposals_info->user_id)) { ?>
                    <div class="date">
                        <?= lang('assigned') ?><?php
                        $profile_info = $this->db->where('user_id', $proposals_info->user_id)->get('tbl_account_details')->row();
                        if (!empty($profile_info)) {
                            echo $profile_info->fullname;
                        }
                        ?>
                    </div>
                <?php } ?>
            </div>
        </td>
    </tr>
</table>

<table class="items" border="0" cellspacing="0" cellpadding="0" page-break-inside: auto;>
    <thead>
    <tr>
        <th class="desc"><?= lang('items') ?></th>
        <?php
        $invoice_view = config_item('invoice_view');
        if (!empty($invoice_view) && $invoice_view == '2') {
            ?>
            <th><?= lang('hsn_code') ?></th>
        <?php } ?>
        <th class="unit"><?= lang('qty') ?></th>
        <th class="desc"><?= lang('price') ?></th>
        <th class="unit"><?= lang('tax') ?></th>
        <th class="total"><?= lang('total') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $invoice_items = $this->proposal_model->ordered_items_by_id($proposals_info->proposals_id);

    if (!empty($invoice_items)) :
        foreach ($invoice_items as $key => $v_item) :
            $item_name = $v_item->item_name ? $v_item->item_name : $v_item->item_desc;
            $item_tax_name = json_decode($v_item->item_tax_name);
            ?>
            <tr>
                <td class="desc"><h3><?= $item_name ?></h3><?= nl2br($v_item->item_desc) ?></td>
                <?php
                $invoice_view = config_item('invoice_view');
                if (!empty($invoice_view) && $invoice_view == '2') {
                    ?>
                    <td><?= $v_item->hsn_code ?></td>
                <?php } ?>
                <td class="unit"><?= $v_item->quantity . '   ' . $v_item->unit ?></td>
                <td class="desc"><?= display_money($v_item->unit_cost) ?></td>
                <td class="unit"><?php
                    if (!empty($item_tax_name)) {
                        foreach ($item_tax_name as $v_tax_name) {
                            $i_tax_name = explode('|', $v_tax_name);
                            echo '<small class="pr-sm">' . $i_tax_name[0] . ' (' . $i_tax_name[1] . ' %)' . '</small>' . display_money($v_item->total_cost / 100 * $i_tax_name[1]) . ' <br>';
                        }
                    }
                    ?></td>
                <td class="total"><?= display_money($v_item->total_cost) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif ?>

    </tbody>
    <tfoot>
    <tr class="total">
        <td colspan="3"></td>
        <td colspan="1"><?= lang('sub_total') ?></td>
        <td><?= display_money($this->proposal_model->proposal_calculation('proposal_cost', $proposals_info->proposals_id)) ?></td>
    </tr>
    <?php if ($proposals_info->discount_total > 0): ?>
        <tr class="total">
            <td colspan="3"></td>
            <td colspan="1"><?= lang('discount') ?>(<?php echo $proposals_info->discount_percent; ?>%)</td>
            <td> <?= display_money($this->proposal_model->proposal_calculation('discount', $proposals_info->proposals_id)) ?></td>
        </tr>
    <?php endif;
    $tax_info = json_decode($proposals_info->total_tax);
    $tax_total = 0;
    if (!empty($tax_info)) {
        $tax_name = $tax_info->tax_name;
        $total_tax = $tax_info->total_tax;
        if (!empty($tax_name)) {
            foreach ($tax_name as $t_key => $v_tax_info) {
                $tax = explode('|', $v_tax_info);
                $tax_total += $total_tax[$t_key];
                ?>
                <tr class="total">
                    <td colspan="3"></td>
                    <td colspan="1"><?= $tax[0] . ' (' . $tax[1] . ' %)' ?></td>
                    <td> <?= display_money($total_tax[$t_key]); ?></td>
                </tr>
            <?php }
        }
    } ?>
    <?php if ($tax_total > 0): ?>
        <tr class="total">
            <td colspan="3"></td>
            <td colspan="1"><?= lang('total'] . ' ' . lang('tax') ?></td>
            <td><?= display_money($tax_total); ?></td>
        </tr>
    <?php endif;
    if ($proposals_info->adjustment > 0): ?>
        <tr class="total">
            <td colspan="3"></td>
            <td colspan="1"><?= lang('adjustment') ?></td>
            <td><?= display_money($proposals_info->adjustment); ?></td>
        </tr>
    <?php endif ?>
    <tr class="total">
        <td colspan="3"></td>
        <td colspan="1"><?= lang('total') ?></td>
        <td><?= display_money($this->proposal_model->proposal_calculation('total', $proposals_info->proposals_id), $currency->symbol); ?></td>
    </tr>
    </tfoot>
</table>
<div id="thanks"><?= lang('thanks') ?>!</div>
<div id="notices">
    <div class="notice"><?= $proposals_info->notes ?></div>
</div>
<?php
$invoice_view = config_item('invoice_view');
if (!empty($invoice_view) && $invoice_view > 0) {
    ?>
    <style type="text/css">
        .panel {
            margin-bottom: 21px;
            background-color: #ffffff;
            border: 1px solid transparent;
            -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        }

        .panel-custom .panel-heading {
            border-bottom: 2px solid #2b957a;
        }

        .panel .panel-heading {
            border-bottom: 0;
            font-size: 14px;
        }

        .panel-heading {
            padding: 10px 15px;
            border-bottom: 1px solid transparent;
            border-top-right-radius: 3px;
            border-top-left-radius: 3px;
        }

        .panel-title {
            margin-top: 0;
            margin-bottom: 0;
            font-size: 16px;
        }
    </style>
    <div class="panel panel-custom" style="margin-top: 20px">
        <div class="panel-heading" style="border:1px solid #dde6e9;border-bottom: 2px solid #57B223;">
            <div class="panel-title"><?= lang('tax_summary') ?></div>
        </div>
        <table class="items" border="0" cellspacing="0" cellpadding="0" page-break-inside: auto;>
            <thead>
            <tr>
                <th class="desc"><?= lang('items') ?></th>
                <?php
                $invoice_view = config_item('invoice_view');
                if (!empty($invoice_view) && $invoice_view == '2') {
                    ?>
                    <th><?= lang('hsn_code') ?></th>
                <?php } ?>
                <th class="unit"><?= lang('qty') ?></th>
                <th class="desc"><?= lang('tax') ?></th>
                <th class="unit" style="text-align: right"><?= lang('total_tax') ?></th>
                <th class="total" style="text-align: right"><?= lang('tax_excl_amt') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $total_tax = 0;
            $total_cost = 0;
            if (!empty($invoice_items)) :
                foreach ($invoice_items as $key => $v_item) :
                    $item_tax_name = json_decode($v_item->item_tax_name);
                    $tax_amount = 0;
                    ?>
                    <tr>
                        <td class="desc"><?= $v_item->item_name ?></td>
                        <?php
                        $invoice_view = config_item('invoice_view');
                        if (!empty($invoice_view) && $invoice_view == '2') {
                            ?>
                            <td><?= $v_item->hsn_code ?></td>
                        <?php } ?>
                        <td class="unit"><?= $v_item->quantity . '   ' . $v_item->unit ?></td>
                        <td class="desc"><?php
                            if (!empty($item_tax_name)) {
                                foreach ($item_tax_name as $v_tax_name) {
                                    $i_tax_name = explode('|', $v_tax_name);
                                    $tax_amount += $v_item->total_cost / 100 * $i_tax_name[1];
                                    echo '<small class="pr-sm">' . $i_tax_name[0] . ' (' . $i_tax_name[1] . ' %)' . '</small>' . display_money($v_item->total_cost / 100 * $i_tax_name[1]) . ' <br>';
                                }
                            }
                            $total_cost += $v_item->total_cost;
                            $total_tax += $tax_amount;
                            ?></td>
                        <td class="unit" style="text-align: right"><?= display_money($tax_amount) ?></td>
                        <td class="total" style="text-align: right"><?= display_money($v_item->total_cost) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif ?>

            </tbody>
            <tfoot>
            <tr class="total">
                <td colspan="3"></td>
                <td><?= lang('total') ?></td>
                <td><?= display_money($total_tax) ?></td>
                <td><?= display_money($total_cost) ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<footer>
    <?= config_item('proposal_footer') ?>
</footer>
</body>
</html>
