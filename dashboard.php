<?php 
include('templates/header.php'); 

$overview = new MonthlyOverview();
$months_array = $overview->getMonthsSorted();
$total_income = $overview->getTotalIncome();
$total_expense = $overview->getTotalExpense();
$net_balance = $overview->getNetBalance();
?>
<style type="text/css">
    #semiDoughnutChart {
    display: block;
    margin: 0 auto;
}

 #monthlyRevenueChart {
        width: 100% !important;
        height: auto !important;
    }

    @media (max-width: 768px) {
        #monthlyRevenueChart {
            height: 250px !important;
        }
    }

    @media (max-width: 576px) {
        #monthlyRevenueChart {
            height: 200px !important;
        }
    }

</style>
<div class="container-fluid">

    <!-- Dashboard Header -->
    <h4 class="mb-4">
        <i class="fas fa-chart-line text-primary me-2"></i> Dashboard Overview
    </h4>

    <?php
    $current_month = $months_array[0] ?? null;
    $current_income = 0;
    $current_expense = 0;

    if ($current_month) {
        $current_data = $overview->monthly_data[$current_month];
        $current_income = array_sum($current_data['income'] ?? []);
        $current_expense = array_sum($current_data['expense'] ?? []);
    }
    ?>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <!-- Overall Income -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Overall Income
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹<?= number_format($total_income, 2) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-rupee-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overall Expenses -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Overall Expenses
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹<?= number_format($total_expense, 2) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Net Balance -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Net Balance (Overall)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹<?= number_format($net_balance, 2) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Month Summary -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Current Month Summary
                            </div>
                            <div class="text-muted mb-1">Income: ₹<?= number_format($current_income, 2) ?></div>
                            <div class="text-muted">Expense: ₹<?= number_format($current_expense, 2) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$last_6_months = array_slice($months_array, 0, 6);
$chart_labels = $chart_income = $chart_expenses = $chart_net = [];

foreach ($last_6_months as $month) {
    $data = $overview->monthly_data[$month];
    $income = array_sum($data['income'] ?? []);
    $expense = array_sum($data['expense'] ?? []);
    $net = $income - $expense;

    $chart_labels[] = $overview->getMonthName($month . "-01");
    $chart_income[] = $income;
    $chart_expenses[] = $expense;
    $chart_net[] = $net;
}
?>

<!-- Chart Section -->
<div class="row">
    <div class="col-12 col-md-6 col-xl-6 mb-4">
    <div class="card card-header-actions h-100 shadow">
        <div class="card-header fw-bold">
            <i class="fas fa-chart-bar text-info me-2"></i> Monthly Revenue (Last 6 Months)
        </div>
        <div class="card-body" style="position: relative; height: 100%;">
            <canvas id="monthlyRevenueChart"></canvas>
        </div>
    </div>
</div>


    <!-- <div class="col-12 col-md-6 col-xl-4 mb-4">
        <div class="card card-header-actions h-100 shadow">
            <div class="card-header fw-bold">
    <i class="fas fa-battery-half text-info me-2"></i> Current Month Spending Progress
</div>
<?php $current_income = array_sum($current_data['income'] ?? []);
$current_expense = array_sum($current_data['expense'] ?? []);
$progress_percentage = ($current_income > 0) ? round(($current_expense / $current_income) * 100, 2) : 0;
$balance_percentage = 100 - $progress_percentage;
 ?>
<div class="card-body text-center">
    <canvas id="semiDoughnutChart" width="150" height="75"></canvas>
    <div class="mt-3">
        <h6 class="mb-1">Expense Used: <strong><?= $progress_percentage ?>%</strong></h6>
        <small class="text-muted">Balance Left: <?= $balance_percentage ?>%</small>
    </div>
</div>


        </div>
    </div> -->
<?php 

// Calculate yearly totals for expenses by category
$year_expense_totals = [];
$year_total_expense = 0;

foreach ($months_array as $month) {
    $monthly_expenses = $overview->monthly_data[$month]['expense'] ?? [];
    foreach ($monthly_expenses as $cat => $amt) {
        if (!isset($year_expense_totals[$cat])) {
            $year_expense_totals[$cat] = 0;
        }
        $year_expense_totals[$cat] += $amt;
        $year_total_expense += $amt;
    }
}

// Calculate average percentage per category for the year
$year_expense_averages = [];
if ($year_total_expense > 0) {
    foreach ($year_expense_totals as $cat => $total_amt) {
        $year_expense_averages[$cat] = round(($total_amt / $year_total_expense) * 100, 2);
    }
}

// Sort categories by total expense descending for better display
arsort($year_expense_totals);

 ?>
    <div class="col-12 col-md-6 col-xl-6 mb-4">
    <div class="card card-header-actions h-100 shadow">
        <div class="card-header fw-bold">
            <i class="fas fa-chart-bar text-info me-2"></i>  (Last 1 year)
        </div>
        
        <div class="card-body" style="position: relative; height: 100%;">
<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Category</th>
                <th>Total Expense (₹)</th>
                <th>Average % of Yearly Expense</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($year_expense_totals as $cat => $total_amt): ?>
            <tr>
                <td><?= htmlspecialchars($cat) ?></td>
                <td>₹<?= number_format($total_amt, 2) ?></td>
                <td><?= $year_expense_averages[$cat] ?? 0 ?>%</td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td><strong>Total Expenses</strong></td>
                <td><strong>₹<?= number_format($year_total_expense, 2) ?></strong></td>
                <td><strong>100%</strong></td>
            </tr>
        </tbody>
    </table>
</div>
        </div>
    </div>
</div>
</div>


<!-- Monthly Breakdown Tabs -->
<h4 class="mb-3"><i class="fas fa-calendar-alt me-2 text-secondary"></i> Monthly Breakdown</h4>

<div class="overflow-auto" style="white-space: nowrap;">
    <ul class="nav nav-tabs d-inline-flex" id="monthTabs" role="tablist" style="min-width: max-content;">
        <?php foreach ($months_array as $i => $month): ?>
            <li class="nav-item" role="presentation">
                <button 
                    class="nav-link <?= $i === 0 ? 'active' : '' ?>" 
                    id="tab-<?= $month ?>" 
                    data-bs-toggle="tab" 
                    data-bs-target="#month-<?= $month ?>" 
                    type="button" 
                    role="tab">
                    <?= $overview->getMonthName($month . "-01") ?>
                </button>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<div class="tab-content p-3 border border-top-0 bg-white shadow mb-4" id="monthTabsContent">
    <?php foreach ($months_array as $i => $month): 
        $data = $overview->monthly_data[$month];
        $prev_month = $months_array[$i + 1] ?? null;
        $income_sources = $data['income'] ?? [];
        $expense_categories = $data['expense'] ?? [];
        $income_total = array_sum($income_sources);
        $expense_total = array_sum($expense_categories);
        $balance = $income_total - $expense_total;
        $prev_expense_categories = $prev_month ? ($overview->monthly_data[$prev_month]['expense'] ?? []) : [];
    ?>
    <div class="tab-pane fade <?= $i === 0 ? 'show active' : '' ?>" id="month-<?= $month ?>" role="tabpanel">
        <h5><i class="fas fa-money-bill-wave me-2 text-success"></i> Income Breakdown</h5>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr><th>Source</th><th>Amount (₹)</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($income_sources as $source => $amt): ?>
                        <tr>
                            <td><?= htmlspecialchars($source) ?></td>
                            <td>₹<?= number_format($amt, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td><strong>Total Income</strong></td>
                        <td><strong>₹<?= number_format($income_total, 2) ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h5><i class="fas fa-receipt me-2 text-danger"></i> Expense Breakdown</h5>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Category</th>
                        <th>Amount (₹)</th>
                        <th>Percentage of Income</th>
                        <th>Change</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expense_categories as $cat => $amt): 
                        $category_percentage = ($income_total > 0) ? round(($amt / $income_total) * 100, 2) : 0;
                        $prev_amt = $prev_expense_categories[$cat] ?? 0;
                        $diff = $amt - $prev_amt;
                        $percent = ($prev_amt > 0) ? round(($diff / $prev_amt) * 100, 2) : 0;
                        $arrow = '';
                        $color = 'text-muted';
                        if ($prev_month) {
                            if ($diff > 0) {
                                $arrow = '<i class="fa-solid fa-arrow-up"></i>';
                                $color = 'text-danger';
                            } elseif ($diff < 0) {
                                $arrow = '<i class="fa-solid fa-arrow-down"></i>';
                                $color = 'text-success';
                            }
                        }
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($cat) ?></td>
                        <td>₹<?= number_format($amt, 2) ?></td>
                        <td><?= $category_percentage ?>%</td>
                        <td class="<?= $color ?>">
                            <?= $prev_month ? ($arrow . ' ' . abs($percent) . '%') : '–' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td><strong>Total Expenses</strong></td>
                        <td colspan="3"><strong>₹<?= number_format($expense_total, 2) ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h5><i class="fas fa-equals me-2 text-primary"></i> Net Balance: 
            <span class="text-<?= $balance >= 0 ? 'success' : 'danger' ?>">
                ₹<?= number_format($balance, 2) ?>
            </span>
        </h5>
    </div>
    <?php endforeach; ?>
</div>

<!-- Footer -->
<?php include('templates/footer.php'); ?>

<!-- Chart.js Script -->
<script>
const ctx = document.getElementById('monthlyRevenueChart').getContext('2d');

const monthlyRevenueChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($chart_labels) ?>,
        datasets: [
            {
                label: 'Total Income',
                backgroundColor: 'rgba(40, 167, 69, 0.6)',
                data: <?= json_encode($chart_income) ?>
            },
            {
                label: 'Total Expenses',
                backgroundColor: 'rgba(220, 53, 69, 0.6)',
                data: <?= json_encode($chart_expenses) ?>
            },
            {
                label: 'Net Balance',
                backgroundColor: 'rgba(0, 123, 255, 0.6)',
                data: <?= json_encode($chart_net) ?>
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₹' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

</script>

<!-- Extra Styles -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctxHalf = document.getElementById('semiDoughnutChart').getContext('2d');

    const progressValue = <?= $progress_percentage ?>;

    let color = '#28a745'; // green
    if (progressValue >= 75) color = '#dc3545'; // red
    else if (progressValue >= 50) color = '#ffc107'; // yellow

    new Chart(ctxHalf, {
        type: 'doughnut',
        data: {
            labels: ['Spent', 'Left'],
            datasets: [{
                data: [progressValue, 100 - progressValue],
                backgroundColor: [color, '#e9ecef'],
                borderWidth: 0
            }]
        },
        options: {
            rotation: -90,
            circumference: 180,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });
});
</script>


</div> <!-- end .container-fluid -->


</body>

</html>