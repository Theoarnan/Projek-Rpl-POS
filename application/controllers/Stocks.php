<?php

class Stocks extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        checkNoLogin();
        roleAkses2();
        $this->load->model(['ModelStocks', 'ModelBarang', 'UserModel', 'ModelSupplier']);
    }

    public function index()
    {
        $listStock = $this->ModelStocks->getJoin3();
        $data = array(
            "page" => "Content/Stock/v_all_data_stock",
            "header" => "Data Stock",
            "stocks" => $listStock,
        );
        $this->load->view("layout/dashboard", $data);
    }

    public function stockIn()
    {
        $stk = new stdClass();
        $stk->id_stock = null;
        $stk->type = null;
        $stk->detail = "0";
        $stk->jumlah = null;
        $stk->tanggal = null;
        $stk->barang_id = null;
        $stk->supplier_id = null;
        $stk->user_id = null;

        $barang = $this->ModelBarang->getJoin();
        $supplier = $this->ModelSupplier->getAll();

        $data = array(
            "header" => "ADD STOCK IN",
            "page" => "Content/Stock/stock_in/v_form_stockin",
            "pages" => 'In',
            "stocks" => $stk,
            "barangs" => $barang,
            "suppliers" => $supplier
        );
        $this->load->view("layout/dashboard", $data);
    }

    public function stockOut()
    {
        $stk = new stdClass();
        $stk->id_stock = null;
        $stk->type = null;
        $stk->detail = "0";
        $stk->jumlah = null;
        $stk->tanggal = null;
        $stk->barang_id = null;
        $stk->user_id = null;
        $barang = $this->ModelBarang->getJoin();

        $data = array(
            "header" => "ADD STOCK OUT",
            "page" => "Content/Stock/stock_out/v_form_stockout",
            "pages" => 'Out',
            "stocks" => $stk,
            "barangs" => $barang,

        );
        $this->load->view("layout/dashboard", $data);
    }

    public function proses()
    {
        // Proses Stock In
        if (isset($_POST['In'])) {
            $jumlah = $this->input->post('jumlah', true);
            $id = $this->input->post('barang', true);
            $stock = array(
                "id_stock" => $this->input->post('id_stock'),
                "type" => "in",
                "detail" => $this->input->post('detail'),
                "supplier_id" => $this->input->post('supplier') == '' ? null : $this->input->post('supplier'),
                'jumlah' =>  $this->input->post('jumlah'),
                "tanggal" => $this->input->post('tanggal'),
                "user_id" => $this->session->userdata('idUser'),
                'barang_id' => $this->input->post('barang')
            );
            $this->ModelBarang->updateStock($jumlah, $id);
            $this->ModelStocks->insert($stock);
            if ($this->db->affected_rows() > 0) {
                $this->session->set_flashdata('success', 'Data Stock-in Sukses disimpan');
            }
            redirect("Stocks/stockin");
            // Proses Stock Out
        } else if (isset($_POST['Out'])) {
            $post = $this->input->post(null, true);
            $row_barang =  $this->ModelBarang->getByPrimaryKey($this->input->post('barang', true));
            if ($row_barang->stock_barang < $this->input->post('jumlah')) {
                $this->session->set_flashdata('error', 'Jumlah barang yang dikeluarkan melebihi stock');
                redirect('Stocks/stockout');
            } else {
                $this->ModelStocks->insert($post);
                $this->ModelBarang->kurangStock($post);

                if ($this->db->affected_rows() > 0) {
                    // $this->session->set_flashdata('succes', 'Data berhasil disimpan');
                    $this->session->set_flashdata('succes', 'Data Stock-out Sukses disimpan');
                }
                redirect('Stocks/stockout');
            }
        }
    }
}
