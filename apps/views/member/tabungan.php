     <?php $utils = new utils; ?>
     <!-- Begin Page Content -->
     <div class="container-fluid">
         <!-- Page Heading -->
         <h1 class="h3 mb-2 text-gray-800">Tabungan</h1>
         <!-- Message -->

         <div><?= flasher::flash(); ?></div>
         <!-- DataTales Example -->
         <div class="card text-center">
             <div class="card-header">
                 <ul class="nav nav-tabs card-header-tabs">
                     <li class="nav-item active">
                         <a class="nav-link active" href="#tabungan" data-toggle="tab">Tabungan</a>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link" href="#penarikan" data-toggle="tab">Penarikan</a>
                     </li>
                 </ul>
             </div>
             <div class="card-body">
                 <!-- Menu Tabungan -->
                 <div class="tab-content">
                     <div class="tab-pane in active" id="tabungan">
                         <div class="card shadow mb-4">
                             <div class="card-header py-3">
                                 <h6 class="float-left mr-3 m-0 font-weight-bold text-primary">Catatan Tabungan</h6>
                                 <br> <small class="float-left">Saldo Terakhir: <?= utils::rupiahFormat(json_decode(file_get_contents('tabungan.json'), true)['tabungan']) ?></small>
                             </div>
                             <div class="card-body">
                                 <div class="table-responsive">
                                     <table class="table table-bordered" id="tabunganTable" width="100%" cellspacing="0">
                                         <thead>
                                             <tr>
                                                 <th>Nomer Transaksi</th>
                                                 <th>NIK</th>
                                                 <th>Nama Lengkap</th>
                                                 <th>Username</th>
                                                 <th>Tanggal Nabung</th>
                                                 <th>Jumlah</th>
                                                 <th>Saldo Sebelumnya</th>
                                                 <th>Saldo Sekarang [Setelah Penarikan] </th>

                                             </tr>
                                         </thead>
                                         <tbody>
                                             <?php foreach ($data['tabungan'] as $tabungan) : ?>
                                                 <tr>
                                                     <td><?= $tabungan['nomer_transaksi'] ?></td>
                                                     <td><?= $tabungan['nik'] ?></td>
                                                     <td><?= $tabungan['nama_lengkap'] ?></td>
                                                     <td><?= $tabungan['account'] ?></td>
                                                     <td><?= date('d M Y', $tabungan['tgl_nabung']) ?></td>
                                                     <td><?= utils::rupiahFormat((int) $tabungan['jumlah']) ?></td>
                                                     <td><?= utils::rupiahFormat((int) $tabungan['saldo_sebelumnya']) ?></td>
                                                     <td><?= utils::rupiahFormat((int) $tabungan['saldo_sebelumnya']) ?></td>

                                                 </tr>
                                             <?php endforeach ?>
                                         </tbody>
                                     </table>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <!-- Ahir menu tabungan -->
                     <div id="penarikan" class="tab-pane fade">
                         <div class="card shadow mb-4">
                             <div class="card-header py-3">
                                 <h6 class="float-left mr-3 m-0 font-weight-bold text-primary">Catatan Penarikan Tabungan</h6>
                                 <br> <small class="float-left">Saldo Terakhir: <?= utils::rupiahFormat(json_decode(file_get_contents('tabungan.json'), true)['tabungan']) ?></small>
                                 <!-- Button trigger modal -->
                                 <!-- <button class='btn btn-xs btn-primary' data-toggle="modal" data-target="#nabungModal">Nabung</button>
                                 <button class='btn btn-xs btn-warning' data-toggle="modal" data-target="#tarikModal">Tarik Tabungan</button> -->
                                 <p id="buttons">Cetak Atau Export Data</p>
                             </div>
                             <div class="card-body">
                                 <div class="table-responsive">
                                     <table class="table table-bordered" id="penarikanTable" width="100%" cellspacing="0">
                                         <thead>
                                             <tr>
                                                 <th>Nomer Transaksi</th>
                                                 <th>NIK</th>
                                                 <th>Nama Lengkap</th>
                                                 <th>Username</th>
                                                 <th>Tanggal Pengambilan</th>
                                                 <th>Jumlah</th>
                                                 <th>Saldo Sebelumnya</th>

                                                 <th>Sisa Saldo</th>
                                             </tr>
                                         </thead>
                                         <tbody>
                                             <?php foreach ($data['penarikan'] as $penarikan) : ?>
                                                 <tr>
                                                     <td><?= $penarikan['nomer_transaksi'] ?></td>
                                                     <td><?= $penarikan['nik'] ?></td>
                                                     <td><?= $penarikan['nama_lengkap'] ?></td>
                                                     <td><?= $penarikan['account'] ?></td>
                                                     <td><?= date('d M Y', $penarikan['tgl_penarikan']) ?></td>
                                                     <td><?= utils::rupiahFormat((int) $penarikan['jumlah']) ?></td>
                                                     <td><?= utils::rupiahFormat((int) $penarikan['saldo_sebelumnya']) ?></td>

                                                     <td><?= utils::rupiahFormat((int) $penarikan['sisa_saldo']) ?></td>
                                                 </tr>
                                             <?php endforeach ?>
                                         </tbody>
                                     </table>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <!-- /.container-fluid -->

     </div>
     <!-- End of Main Content -->

     </div>
     <!-- End of Content Wrapper -->

     </div>
     <!-- End of Page Wrapper -->

     <!-- Scroll to Top Button-->
     <a class="scroll-to-top rounded" href="#page-top">
         <i class="fas fa-angle-up"></i>
     </a>

     <!-- Script Untuk validasi inputan angka -->
     <script src="<?= BASEURL . '/apps/helper/validator.js' ?>"></script>