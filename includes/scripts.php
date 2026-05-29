</div>

<script src="node_modules/jquery/dist/jquery.min.js"></script>
<script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="node_modules/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="node_modules/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
<script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script src="node_modules/admin-lte/dist/js/adminlte.min.js"></script>
<script>
  $(function () {
    function escapeRegex(value) {
      return $.fn.dataTable.util.escapeRegex(value);
    }

    function applyExactColumnFilter(table, selector, columnIndex) {
      $(selector).on('change', function () {
        var value = this.value;
        table
          .column(columnIndex)
          .search(value ? '^' + escapeRegex(value) + '$' : '', true, false)
          .draw();
      });
    }

    if ($('#quizTable').length) {
      var quizTable = $('#quizTable').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        order: [[3, 'asc']],
        columnDefs: [
          { targets: [0, 7], orderable: false },
          { targets: 0, searchable: false },
          { targets: 4, className: 'question-column' }
        ],
        language: {
          search: 'Cari:',
          lengthMenu: 'Tampilkan _MENU_ data',
          info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
          infoEmpty: 'Tidak ada data',
          infoFiltered: '(difilter dari _MAX_ total data)',
          zeroRecords: 'Data tidak ditemukan',
          paginate: {
            first: 'Pertama',
            last: 'Terakhir',
            next: 'Berikutnya',
            previous: 'Sebelumnya'
          }
        }
      });

      quizTable.on('draw.dt order.dt search.dt', function () {
        var pageInfo = quizTable.page.info();
        quizTable.column(0, { page: 'current' }).nodes().each(function (cell, index) {
          cell.innerHTML = pageInfo.start + index + 1;
        });
      }).draw();

      applyExactColumnFilter(quizTable, '#filterMode', 1);
      applyExactColumnFilter(quizTable, '#filterLevel', 2);
      applyExactColumnFilter(quizTable, '#filterStatus', 6);
      applyExactColumnFilter(quizTable, '#filterMediaType', 5);

      $('#resetQuizFilter').on('click', function () {
        $('#filterMode, #filterLevel, #filterStatus, #filterMediaType').val('');
        quizTable.columns().search('').draw();
      });
    }

    if ($('#usersTable').length) {
      var usersTable = $('#usersTable').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        order: [[6, 'desc']],
        columnDefs: [
          { targets: [0, 7], orderable: false },
          { targets: 0, searchable: false }
        ],
        language: {
          search: 'Cari:',
          lengthMenu: 'Tampilkan _MENU_ data',
          info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
          infoEmpty: 'Tidak ada data',
          infoFiltered: '(difilter dari _MAX_ total data)',
          zeroRecords: 'Data tidak ditemukan',
          paginate: {
            first: 'Pertama',
            last: 'Terakhir',
            next: 'Berikutnya',
            previous: 'Sebelumnya'
          }
        }
      });

      usersTable.on('draw.dt order.dt search.dt', function () {
        var pageInfo = usersTable.page.info();
        usersTable.column(0, { page: 'current' }).nodes().each(function (cell, index) {
          cell.innerHTML = pageInfo.start + index + 1;
        });
      }).draw();

      applyExactColumnFilter(usersTable, '#filterRole', 3);
      applyExactColumnFilter(usersTable, '#filterUserStatus', 5);

      $('#resetUserFilter').on('click', function () {
        $('#filterRole, #filterUserStatus').val('');
        usersTable.columns().search('').draw();
      });
    }

    if ($('#rewardsTable').length) {
      var rewardsTable = $('#rewardsTable').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        order: [[2, 'asc']],
        columnDefs: [
          { targets: [1, 8], orderable: false },
          { targets: [0, 1], searchable: false }
        ],
        language: {
          search: 'Cari:',
          lengthMenu: 'Tampilkan _MENU_ data',
          info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
          infoEmpty: 'Tidak ada data',
          infoFiltered: '(difilter dari _MAX_ total data)',
          zeroRecords: 'Data tidak ditemukan',
          paginate: {
            first: 'Pertama',
            last: 'Terakhir',
            next: 'Berikutnya',
            previous: 'Sebelumnya'
          }
        }
      });

      rewardsTable.on('draw.dt order.dt search.dt', function () {
        var pageInfo = rewardsTable.page.info();
        rewardsTable.column(0, { page: 'current' }).nodes().each(function (cell, index) {
          cell.innerHTML = pageInfo.start + index + 1;
        });
      }).draw();

      applyExactColumnFilter(rewardsTable, '#filterRewardType', 3);
      applyExactColumnFilter(rewardsTable, '#filterRewardMode', 5);
      applyExactColumnFilter(rewardsTable, '#filterRewardLevel', 6);
      applyExactColumnFilter(rewardsTable, '#filterRewardStatus', 7);

      $('#resetRewardFilter').on('click', function () {
        $('#filterRewardType, #filterRewardMode, #filterRewardLevel, #filterRewardStatus').val('');
        rewardsTable.columns().search('').draw();
      });

      $(document).on('click', '.btn-delete-reward', function () {
        var rewardName = $(this).data('reward-name');

        Swal.fire({
          title: 'Hapus hadiah?',
          text: 'Data "' + rewardName + '" belum benar-benar dihapus karena masih dummy.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, hapus',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#dc3545'
        }).then(function (result) {
          if (result.isConfirmed) {
            Swal.fire('Dummy', 'Konfirmasi hapus berhasil ditampilkan. Data belum dihapus.', 'success');
          }
        });
      });
    }
  });
</script>
<?php include __DIR__ . '/backend_config.php'; ?>
<script type="module" src="assets/js/admin-auth.js"></script>
<script type="module" src="assets/js/admin-backend.js"></script>
<script type="module" src="assets/js/admin-forms.js"></script>
</body>
</html>
