<section class="admin-card">
    <div class="card-head"><h2>Reports</h2><div><button class="btn btn-outline" onclick="window.print()">Print</button><button class="btn btn-outline">CSV</button><button class="btn btn-outline">PDF</button></div></div>
    <div class="table-tools"><input type="date"><input type="date"><select><option>Sales Report</option><option>Product-wise Report</option><option>Customer Report</option></select><button class="btn btn-primary">Run Report</button></div>
    <table class="admin-table"><thead><tr><th>Status</th><th class="num">Orders</th><th class="num">Revenue</th></tr></thead><tbody>
    <?php foreach ($orders as $row): ?><tr><td><span class="status-badge <?= status_class($row['status']) ?>"><?= e($row['status']) ?></span></td><td class="num"><?= (int)$row['count_orders'] ?></td><td class="num"><?= money((float)$row['revenue']) ?></td></tr><?php endforeach; ?>
    </tbody></table>
</section>

