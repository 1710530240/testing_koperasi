<?php
class tabungan_model
{
    private $DB, $utils;
    public function __construct()
    {
        $this->utils = new utils;
        $this->DB = new database;
    }
    public function getLogTabungan()
    {
        $member = $this->utils->getMember();
        $this->DB->query('SELECT * FROM simpanan_sukarela join member on(member.nik=:nik AND simpanan_sukarela.anggota=member.nik) ORDER BY simpanan_sukarela.tgl_nabung DESC');
        $this->DB->bind('nik', $member['nik']);
        return $this->DB->resultSet();
    }
    public function getLogPenarikan()
    {
        $member = $this->utils->getMember();
        $this->DB->query('SELECT * FROM penarikan join member on(member.nik=:nik AND penarikan.anggota=member.nik) ORDER BY penarikan.tgl_penarikan DESC');
        $this->DB->bind('nik', $member['nik']);
        return $this->DB->resultSet();
    }
    function getAllTabungan()
    {
        $this->DB->query('SELECT *, tabungan.saldo FROM simpanan_sukarela join member JOIN tabungan on(simpanan_sukarela.anggota=member.nik and simpanan_sukarela.anggota = tabungan.anggota) ORDER BY simpanan_sukarela.tgl_nabung DESC');
        return $this->DB->resultSet();
    }
    function getAllPenarikan()
    {
        $this->DB->query('SELECT *, tabungan.saldo FROM penarikan join member JOIN tabungan on(penarikan.anggota=member.nik and penarikan.anggota = tabungan.anggota) ORDER BY penarikan.tgl_penarikan DESC');
        return $this->DB->resultSet();
    }
    public function nabung($data)
    {
        //    Persiapan
        $tgl = time();
        $jumlah = $data['jumlah'];
        $anggota = $data['nik'];
        $saldoSebelumnya = $this->getSaldo($anggota);
        // insert ke tabel simpanan sukarela sebagai log tabungan
        $this->DB->query("INSERT INTO simpanan_sukarela(`anggota`,`tgl_nabung`, `jumlah`,  `saldo_sebelumnya`) VALUES( :anggota, :tgl,:jumlah,:saldo_sebelumnya)");
        $this->DB->bind('anggota', $anggota);
        $this->DB->bind('tgl', $tgl);
        $this->DB->bind('jumlah', $jumlah);
        $this->DB->bind('saldo_sebelumnya', $saldoSebelumnya);
        $this->DB->execute();

        // update saldo member
        $this->updateSaldo($anggota, $saldoSebelumnya, $jumlah, true);
    }
    function updateSaldo($nik, $saldoSebelumnya, $jumlah, $isNabung)
    {
        if ($isNabung == 1) {
            if ($saldoSebelumnya != 0) {
                $saldoTerbaru = $saldoSebelumnya + $jumlah;
                $this->DB->query("UPDATE tabungan set saldo=:saldo WHERE anggota=:nik");
                $this->DB->bind('saldo', $saldoTerbaru);
                $this->DB->bind('nik', $nik);
            } else {
                $this->DB->query("INSERT INTO tabungan(`anggota`, `saldo`) VALUES (:nik, :saldo)");
                $this->DB->bind('saldo', $jumlah);
                $this->DB->bind('nik', $nik);
            }
        } else {
            if ($saldoSebelumnya != 0) {
                $saldoTerbaru = $saldoSebelumnya - $jumlah;
                $this->DB->query("UPDATE tabungan set saldo=:saldo WHERE anggota=:nik");
                $this->DB->bind('saldo', $saldoTerbaru);
                $this->DB->bind('nik', $nik);
            }
        }
        $this->DB->execute();
        if ($this->DB->rowCount() > 0) {
            flasher::setFlash('Berhasil', 'success');
            header('Location:' . BASEURL . '/admin/tabungan_member');
        }
    }
    function getTabunganTerakhir()
    {
        $member = $this->utils->getMember();
        $this->DB->query('SELECT * FROM simpanan_sukarela join member on(member.nik=:nik AND simpanan_sukarela.anggota=member.nik) ORDER BY simpanan_sukarela.tgl_nabung DESC LIMIT 1');
        $this->DB->bind('nik', $member['nik']);
        return $this->DB->single();
    }
    function getPenarikanTerakhir()
    {
        $member = $this->utils->getMember();
        $this->DB->query('SELECT * FROM penarikan join member on(member.nik=:nik AND penarikan.anggota=member.nik) ORDER BY penarikan.tgl_penarikan DESC LIMIT 1');
        $this->DB->bind('nik', $member['nik']);
        return $this->DB->single();
    }

    function getSaldo($nik)
    {
        $this->DB->query("SELECT * FROM tabungan WHERE anggota=:nik");
        $this->DB->bind('nik', $nik);
        $tabungan = $this->DB->single();
        $saldo = empty($tabungan) ? 0 : $tabungan['saldo'];
        return $saldo;
    }

    function tarikTabungan($data)
    {
        // Persiapan
        $tgl = time();
        $jumlah = (int) $data["jumlah"];
        // Ambil data user dan member yang login
        $member = $this->utils->getMember();
        // Ambil data tabungan sebelumnya]
        $saldoTerakhir = $this->utils->getSaldoTerbesar($member['nik']);
        if ($saldoTerakhir != false) {
            $saldoTerakhir = (int) $saldoTerakhir['saldo'];
        }
        if ($saldoTerakhir == false || $saldoTerakhir == 0) {
            flasher::setFlash('Gagal, Anda Belum pernah menabung', 'danger');
            header('Location:' . BASEURL . '/member/tabungan');
        } else if ($saldoTerakhir < $jumlah) {
            flasher::setFlash('Gagal, Saldo Anda tidak cukup', 'danger');
            header('Location:' . BASEURL . '/member/tabungan');
            exit;
        }
        //  Query ke DB
        $this->DB->query('INSERT INTO penarikan(`anggota`, `tgl_penarikan`, `jumlah`,`saldo_sebelumnya`,`sisa_saldo`) values(:anggota, :tgl, :jumlah,:saldoSebelumnya,:sisaSaldo)');
        $this->DB->bind('anggota', $member['nik']);
        $this->DB->bind('tgl', $tgl);
        $this->DB->bind('jumlah', $jumlah);
        $this->DB->bind('saldoSebelumnya', (int) $saldoTerakhir);
        $this->DB->bind('sisaSaldo', (int) $saldoTerakhir);
        $this->DB->execute();

        if ($this->DB->rowCount() > 0) {
            flasher::setFlash('Berhasil, Tinggal tunggu konfirmasi dari admin', 'success');
            header('Location:' . BASEURL . '/member/tabungan');
        }
    }
    function narik($data)
    {
        //    Persiapan
        $tgl = time();
        $jumlah = $data['jumlah'];
        $anggota = $data['nik'];
        $saldoSebelumnya = $this->getSaldo($anggota);
        // insert ke tabel simpanan sukarela sebagai log tabungan
        $this->DB->query("INSERT INTO penarikan(`anggota`,`tgl_penarikan`, `jumlah`,  `saldo_sebelumnya`) VALUES( :anggota, :tgl,:jumlah,:saldo_sebelumnya)");
        $this->DB->bind('anggota', $anggota);
        $this->DB->bind('tgl', $tgl);
        $this->DB->bind('jumlah', $jumlah);
        $this->DB->bind('saldo_sebelumnya', $saldoSebelumnya);
        $this->DB->execute();

        // update saldo member
        $this->updateSaldo($anggota, $saldoSebelumnya, $jumlah, false);
    }
}
