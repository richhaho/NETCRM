<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of dompdf_helper
 *
 * @author Parimal
 */
function pdf_create($html, $filename = '', $stream = TRUE, $set_paper = '', $attach = null, $folder_name = null)
{

    require_once("dompdf/dompdf_config.inc.php");

    $dompdf = new DOMPDF();
    $dompdf->load_html($html);

    $dompdf->set_paper("a4", 'portrait');

    $dompdf->render();
    if ($stream) {
        $pdf_string = $dompdf->output();
        if (!empty($attach)) {
            if (!empty($folder_name)) {
                $folder = "uploads/" . $folder_name . '/' . $filename . ".pdf";;
            } else {
                $folder = "uploads/" . $filename . ".pdf";;
            }
            file_put_contents($folder, $pdf_string);
        } else {
            $dompdf->stream($filename . ".pdf");
        }
    } else {
        return $dompdf->output();
    }
}
