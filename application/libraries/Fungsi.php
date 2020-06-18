<?php

class Fungsi
{

    protected $ci;

    public function __construct()
    {
        $this->ci = &get_instance();
    }

    function user_login()
    {
        $this->ci->load->model('UserModel');
        $idUser = $this->ci->session->userdata('idUser');
        $user_data = $this->ci->UserModel->getJoinById($idUser);
        return $user_data;
    }

    // PDF
    function createPDF($html, $filename, $paper, $orientation)
    {
        $dompdf = new Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper($paper, $orientation);
        // Render the HTML as PDF
        $dompdf->render();
        // Output the generated PDF to Browser
        $dompdf->stream($filename, array('Attachment' => 0));
    }
}
