<?php
class admin extends controller
{
    public function __construct()
    {
        // cek apakah client sudah login
        if (!isset($_SESSION['login']) or $_SESSION['user_data']['role'] == 2) {
            header('Location: ' . BASEURL . '/auth');
        }
    }
    public function index()
    {
        $data['pageTitle'] = "Admin | Dashboard";
        $data['user'] = $_SESSION['user_data'];
        $this->view('header/admin', $data);
        $this->view('navigasi/main', $data);
        $this->view('admin/dashboard', $data);
        $this->view('footer/main');;
    }
    public function member_menu()
    {
        if (isset($_POST['add'])) {
            $this->model('user_model')->signUp($_POST);
            $this->model('member_model')->addMember($_POST);
        } else {
            $data['member'] = $this->model('member_model')->getAllMember();
            $data['pageTitle'] = "Koperasi | Members";
            $data['user'] = $_SESSION['user_data'];
            $this->view('header/data-tables', $data);
            $this->view('navigasi/main', $data);
            $this->view('admin/member-menu', $data);
            $this->view('footer/data-tables');
        }
    }
    public function delete_member($data)
    {
        $this->model('user_model')->deleteUser($data[0], $data[1], $data[2]);
    }
    public function nonaktif_member($username)
    {
        $this->model('user_model')->nonAktifkanUser($username);
    }
    public function aktif_member($username)
    {
        $this->model('user_model')->aktifkanUser($username);
    }
    function tabungan_member()
    {
        $data['pageTitle'] = "Koperasi | Members";
        $data['tabungan'] = $this->model('tabungan_model')->getAllTabungan();
        $data['penarikan'] = $this->model('tabungan_model')->getAllPenarikan();
        $data['member'] = $this->model('member_model')->getAllMember();
        $data['user'] = $_SESSION['user_data'];

        $this->view('header/data-tables', $data);
        $this->view('navigasi/main', $data);
        $this->view('admin/tabungan', $data);
        $this->view('footer/data-tables');
    }
    function add_tabungan()
    {
        $this->model('tabungan_model')->nabung($_POST);
        header("Location: " . BASEURL . "/admin/tabungan_member");
    }
    function add_penarikan()
    {
        $data = $_POST;
        $saldo = $this->model('tabungan_model')->getSaldo($data['nik']);
        if ($data['jumlah'] > $saldo) {
            flasher::setFlash("Gagal, Saldo kurang", "danger");
            header("Location: " . BASEURL . "/admin/tabungan_member");
        } else {
            $data['jumlah'] <  $this->model('tabungan_model')->narik($data);
        }
    }
    // public function tabungan($params)
    // {

    //     if ($params != null) {
    //         $page = $params[0];
    //         if ($page == 'konfirm') {
    //             $service = $params[1];
    //             $noTransaksi = $params[2];
    //             $jumlah = $params[3];
    //             if ($service == 'nabung') {
    //                 $this->model('tabungan_model')->konfirmasiTabungan($noTransaksi, $jumlah);
    //             } else if ($service == 'narik') {
    //                 $this->model('tabungan_model')->konfirmasiPenarikan($noTransaksi, $jumlah);
    //             }
    //         } else if ($page == 'tarik') {
    //             $data['user'] = $_SESSION['user_data'];
    //             $data['penarikan'] = $this->helper('utils')->getPengajuanPenarikan();
    //             $data['pageTitle'] = "Koperasi | Tabungan";
    //             $this->view('header/data-tables', $data);
    //             $this->view('navigasi/main', $data);
    //             $this->view('admin/konfirmasi-penarikan', $data);
    //             $this->view('footer/data-tables');
    //         }
    //     } else {
    //         $data['user'] = $_SESSION['user_data'];
    //         $data['tabungan'] = $this->helper('utils')->getPengajuanTabungan();
    //         $data['pageTitle'] = "Koperasi | Tabungan";
    //         $this->view('header/data-tables', $data);
    //         $this->view('navigasi/main', $data);
    //         $this->view('admin/konfirmasi-tabungan', $data);
    //         $this->view('footer/data-tables');
    //     }
    // }
}
