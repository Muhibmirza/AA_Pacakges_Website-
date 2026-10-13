<section class="admin-card">
    <div class="card-head">
        <div><h2>Discount Leads / Subscribers</h2><p class="muted-copy">First-order offer signups collected from the storefront.</p></div>
        <a class="btn btn-outline" href="<?= admin_route('leads') ?>?<?= e(http_build_query(array_filter(['q' => $_GET['q'] ?? '', 'date_from' => $_GET['date_from'] ?? '', 'date_to' => $_GET['date_to'] ?? '', 'export' => 'csv']))) ?>">Export CSV</a>
    </div>
    <form class="table-tools" method="get" action="<?= admin_route('leads') ?>">
        <input name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Search name, email, or phone">
        <input type="date" name="date_from" value="<?= e($_GET['date_from'] ?? '') ?>" aria-label="From date">
        <input type="date" name="date_to" value="<?= e($_GET['date_to'] ?? '') ?>" aria-label="To date">
        <button class="btn btn-outline" type="submit">Filter</button>
        <a class="btn btn-outline" href="<?= admin_route('leads') ?>">Reset</a>
    </form>
    <div class="table-scroll">
        <table class="admin-table">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Submitted</th><th>Updated</th><th class="actions-col">Actions</th></tr></thead>
            <tbody>
            <?php foreach ($leads as $lead): ?>
                <tr>
                    <td><?= e($lead['name'] ?: 'Not provided') ?></td>
                    <td><a href="mailto:<?= e($lead['email']) ?>"><?= e($lead['email']) ?></a></td>
                    <td><a href="tel:<?= e($lead['phone']) ?>"><?= e($lead['phone']) ?></a></td>
                    <td><?= e(date('M d, Y g:i A', strtotime($lead['created_at']))) ?></td>
                    <td><?= e(date('M d, Y g:i A', strtotime($lead['updated_at']))) ?></td>
                    <td class="actions">
                        <form method="post" class="inline-form" onsubmit="return confirm('Delete this discount lead?')">
                            <?= csrf_field() ?><input type="hidden" name="action" value="delete_row"><input type="hidden" name="table" value="discount_leads"><input type="hidden" name="id" value="<?= (int)$lead['id'] ?>"><button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$leads): ?><tr><td colspan="6" class="empty-cell">No discount leads found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
