<?php
session_start();
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../classes/User.php';
require_once '../classes/Login.php';
require_once '../classes/InputSanitizer.php';
require_once '../classes/ServerSideValidation.php';
require_once '../classes/MonthlyOverview.php';
include_once '../classes/Income.php';
require_once '../classes/Expense.php';



// Redirect to login if not authenticated
if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "../views/login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin</title>
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style type="text/css">
        @media (max-width: 768px) {
    #accordionSidebar {
        margin-left: 0px;
        transition: margin 0.3s ease;
    }

    body.sidebar-toggled #accordionSidebar {
        margin-left: 0;
    }
    .custom-css .list-group {
    border-radius: 0 px; 
    }
    .custom-css .card-body {
   
    padding: 0px;
}

}
/*new folting button*/
/* FAB container fixed bottom right */
/* Container fixed to bottom-right */
    /* Overlay that covers entire screen */
  .fab-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(255, 255, 255, 0.7); /* white with 70% opacity */
    z-index: 900;
    display: none;
  }
  .fab-overlay.show {
    display: block;
  }

  /* Container fixed to bottom-right */
  .fab-container {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 1001; /* above overlay */
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  /* Main FAB button */
  .fab-button {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background-color: #007bff;
    color: white;
    font-size: 36px;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    display: flex;
    justify-content: center;
    align-items: center;
    transition: background-color 0.3s;
  }
  .fab-button:hover {
    background-color: #0056b3;
  }

  /* Options container */
  .fab-options {
    display: none;
    flex-direction: column;
    margin-bottom: 10px;
  }
  .fab-options.show {
    display: flex;
  }

  /* Each option styled as a pill button */
  .fab-option {
    background-color: #fff;
    color: #007bff;
    padding: 10px 20px;
    border-radius: 30px;
    text-decoration: none;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    margin-bottom: 10px;
    font-weight: 600;
    width: 140px;
    text-align: center;
    transition: background-color 0.3s, color 0.3s;
  }
  .fab-option:hover {
    background-color: #007bff;
    color: white;
  }


/* Only show FAB on small screens (phones) */
@media (min-width: 768px) {
  .fab-container {
    display: none;
  }
}

    </style>

</head>
<body id="page-top">

<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
            <div class="sidebar-brand-text mx-3">Admin</div>
        </a>

        <hr class="sidebar-divider my-0">

        <li class="nav-item <?= $current_page == 'dashboard.php' ? 'active' : ''; ?>">
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
        </li>

        <hr class="sidebar-divider">

        <div class="sidebar-heading">Interface</div>

        <!-- Blog Category Menu -->
       <?php
    $income_pages = ['income-list.php', 'income-add.php'];
    ?>
    <li class="nav-item <?= in_array($current_page, $income_pages) ? 'active' : ''; ?>">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseIncome"
           aria-expanded="true" aria-controls="collapseIncome">
            <i class="fas fa-fw fa-money-bill-wave"></i>
            <span>Income</span>
        </a>
        <div id="collapseIncome" class="collapse <?= in_array($current_page, $income_pages) ? 'show' : ''; ?>" aria-labelledby="headingIncome" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Income Management:</h6>
                <a class="collapse-item <?= $current_page == 'income-list.php' ? 'active' : ''; ?>" href="income-list.php">Income List</a>
                <a class="collapse-item <?= $current_page == 'income-add.php' ? 'active' : ''; ?>" href="income-add.php">Add Income</a>
            </div>
        </div>
    </li>


        <!-- Programs Menu -->
        <?php
$expense_pages = ['expense-list.php', 'expense-add.php'];
?>
<li class="nav-item <?= in_array($current_page, $expense_pages) ? 'active' : ''; ?>">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseExpense"
       aria-expanded="true" aria-controls="collapseExpense">
        <i class="fas fa-fw fa-receipt"></i>
        <span>Expense</span>
    </a>
    <div id="collapseExpense" class="collapse <?= in_array($current_page, $expense_pages) ? 'show' : ''; ?>" aria-labelledby="headingExpense" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Expense Management:</h6>
            <a class="collapse-item <?= $current_page == 'expense-list.php' ? 'active' : ''; ?>" href="expense-list.php">Expense List</a>
            <a class="collapse-item <?= $current_page == 'expense-add.php' ? 'active' : ''; ?>" href="expense-add.php">Add Expense</a>
        </div>
    </div>
</li>


        <hr class="sidebar-divider d-none d-md-block">

        <!-- Sidebar Toggler -->
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>

    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                <div class="text-dark font-weight-bold">
                    <?= $_SESSION['user']; ?>'s Dashboard!
                </div>

                <ul class="navbar-nav ml-auto">

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- User Dropdown -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $_SESSION['user']; ?></span>
                            <img class="img-profile rounded-circle" src="../assets/img/undraw_profile.svg">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                             aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Profile
                            </a>
                           
                            <a class="dropdown-item" href="../views/activity-log-list.php">
                                <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                Activity Log
                            </a>
                            <a class="dropdown-item" href="../views/master-log-list.php">
                                <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                Master Log
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </li>

                </ul>
            </nav>
            <!-- End of Topbar -->
