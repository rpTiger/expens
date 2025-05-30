<?php include('templates/header.php'); ?>
<?php
$expenseObj = new Expense();

$successMessage = '';
if (isset($_SESSION['success'])) {
    $successMessage = $_SESSION['success'];
    unset($_SESSION['success']);
}

$monthlyExpenses = $expenseObj->getExpensesGroupedByMonth();
$monthlyCategoryTotals = $expenseObj->getCategoryTotalsByMonth();

// Get monthly totals
$monthlyTotals = [];
foreach ($monthlyExpenses as $month => $items) {
    $monthlyTotals[$month] = array_sum(array_column($items, 'amount'));
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Expense List by Month</h1>

    <?php if (!empty($successMessage)): ?>
        <div id="flash-success" class="alert alert-success">
            <?= $successMessage ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($monthlyExpenses)) : ?>
        <div class="nav-tabs-wrapper" style="overflow-x: auto; white-space: nowrap;">
            <ul class="nav nav-tabs flex-nowrap" id="expenseTab" role="tablist">
                <?php
                $first = true;
                foreach ($monthlyExpenses as $month => $expenses) {
                    $monthLabel = date('F Y', strtotime($month . '-01'));
                    $tabId = 'tab_' . str_replace(['-', ' '], '_', $month);
                    ?>
                    <li class="nav-item" style="flex: 0 0 auto;">
                        <a class="nav-link <?= $first ? 'active' : '' ?>"
                           id="<?= $tabId ?>-tab"
                           data-toggle="tab"
                           href="#<?= $tabId ?>"
                           role="tab"
                           aria-controls="<?= $tabId ?>"
                           aria-selected="<?= $first ? 'true' : 'false' ?>">
                            <?= $monthLabel ?>
                        </a>
                    </li>
                    <?php $first = false;
                } ?>
            </ul>
        </div>

        <div class="tab-content mt-3" id="expenseTabContent">
            <?php
            $first = true;
            foreach ($monthlyExpenses as $month => $expenses) {
                $tabId = 'tab_' . str_replace(['-', ' '], '_', $month);
                $canvasId = 'pieChart_' . str_replace('-', '_', $month);
                $monthLabel = date('F Y', strtotime($month . '-01'));
                $total = $monthlyTotals[$month] ?? 0;
                $categoryList = array_unique(array_column($expenses, 'category'));
                ?>
                <div class="tab-pane fade <?= $first ? 'show active' : '' ?>" id="<?= $tabId ?>" role="tabpanel">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Spent (<?= $monthLabel ?>)
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                ₹<?= number_format($total, 2) ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header">
                                    <h6 class="font-weight-bold text-primary">Category-wise Expense – <?= $monthLabel ?></h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="<?= $canvasId ?>"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card shadow h-100 custom-css">
                                <div class="card-header">
                                    <h6 class="font-weight-bold text-primary">Category-wise Expense Distribution for <?= $monthLabel ?></h6>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $totalForMonth = $monthlyTotals[$month] ?? 0;
                                    $categories = $monthlyCategoryTotals[$month] ?? [];

                                    if ($totalForMonth > 0 && !empty($categories)) {
                                        echo '<ul class="list-group">';
                                        foreach ($categories as $category => $amount) {
                                            $percent = ($amount / $totalForMonth) * 100;
                                            echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                            echo htmlspecialchars($category);
                                            echo '<span>₹' . number_format($amount, 2) . ' <small class="text-muted">(' . number_format($percent, 1) . '%)</small></span>';
                                            echo '</li>';
                                        }
                                        echo '</ul>';
                                    } else {
                                        echo '<p>No expense data available for this month.</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter and Expenses -->
                    <div class="card card-header-actions mb-4 shadow">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Expense Entries</span>
                            <select class="form-control form-control-sm w-auto" id="filter_<?= $tabId ?>" onchange="filterExpenses('<?= $tabId ?>')">
                                <option value="all">All Categories</option>
                                <?php foreach ($categoryList as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="card-body px-0" id="expenses_<?= $tabId ?>">
                            <?php foreach ($expenses as $row): ?>
                                <?php $catKey = htmlspecialchars($row['category']); ?>
                                <div class="expense-item px-4 py-3" data-category="<?= $catKey ?>">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold">₹<?= number_format($row['amount'], 2) ?> - <?= $catKey ?></div>
                                            <div class="text-xs text-muted"><?= date('d-m-Y', strtotime($row['date'])) ?> | <?= htmlspecialchars($row['note']) ?></div>
                                        </div>
                                        <div>
                                            <a href="edit-expense.php?id=<?= $row['id'] ?>" class="small text-primary">Edit</a>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php $first = false;
            } ?>
        </div>
    <?php else : ?>
        <p>No expense records found.</p>
    <?php endif; ?>
</div>

<?php include('templates/footer.php'); ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    <?php foreach ($monthlyCategoryTotals as $month => $categories):
        $canvasId = 'pieChart_' . str_replace('-', '_', $month);
        $labels = json_encode(array_keys($categories));
        $values = json_encode(array_values($categories));
    ?>
    new Chart(document.getElementById('<?= $canvasId ?>'), {
        type: 'pie',
        data: {
            labels: <?= $labels ?>,
            datasets: [{
                data: <?= $values ?>,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
    callbacks: {
        label: function (tooltipItem) {
            let value = tooltipItem.raw;
            let total = tooltipItem.dataset.data.reduce((a, b) => Number(a) + Number(b), 0);
            
            if (!total || isNaN(value)) {
                return `${tooltipItem.label}: ₹${Number(value).toLocaleString()} (0%)`;
            }

            let percent = (value / total * 100).toFixed(1);
            return `${tooltipItem.label}: ₹${Number(value).toLocaleString()} (${percent}%)`;
        }
    }
}

            }
        }
    });
    <?php endforeach; ?>
});
</script>

<script>
function filterExpenses(tabId) {
    let selectedCategory = document.getElementById('filter_' + tabId).value;
    const expenseContainer = document.getElementById('expenses_' + tabId);
    const items = expenseContainer.querySelectorAll('.expense-item');

    items.forEach(item => {
        if (selectedCategory === 'all' || item.dataset.category === selectedCategory) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Hide flash message after 1 second
setTimeout(function () {
    const alertBox = document.getElementById('flash-success');
    if (alertBox) {
        alertBox.style.transition = "opacity 0.5s";
        alertBox.style.opacity = 0;
        setTimeout(() => alertBox.remove(), 500);
    }
}, 1000);
</script>

</body>
</html>
