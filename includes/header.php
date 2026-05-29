<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= $pageTitle ?? 'Admin SwaraNusa Quiz'; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="node_modules/@fortawesome/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="node_modules/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="node_modules/@sweetalert2/theme-bootstrap-4/bootstrap-4.min.css">
  <link rel="stylesheet" href="node_modules/admin-lte/dist/css/adminlte.min.css">
  <style>
    #quizTable {
      table-layout: fixed;
    }

    #quizTable th,
    #quizTable td {
      vertical-align: middle;
      white-space: normal;
    }

    #quizTable .question-column,
    #quizTable .question-cell {
      max-width: 360px;
      overflow-wrap: anywhere;
      word-break: normal;
    }

    .layout-fixed .main-sidebar {
      position: fixed;
    }

    .layout-fixed .main-sidebar .sidebar {
      height: calc(100vh - 57px);
      overflow-y: auto;
    }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
