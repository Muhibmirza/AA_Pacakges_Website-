<section class="admin-card">
    <div class="card-head"><h2>Quote Requests</h2></div>
    <div class="table-tools"><input placeholder="Search business, phone, category"><select><option>All statuses</option><option>New</option><option>Contacted</option><option>Quoted</option><option>Closed</option></select><button class="btn btn-outline">Filter</button></div>
    <table class="admin-table">
        <thead><tr><th>Business</th><th>Contact</th><th>Box Type</th><th class="num">Qty</th><th>Printing</th><th>Date</th><th>Status</th><th class="actions-col">Actions</th></tr></thead>
        <tbody>
        <?php foreach ($quotes as $quote): ?>
            <tr><td><?= e($quote['business_name'] ?: 'Individual') ?><small><?= e($quote['email']) ?></small></td><td><?= e($quote['contact_name']) ?><small><?= e($quote['phone']) ?></small></td><td><?= e($quote['box_category']) ?></td><td class="num"><?= (int)$quote['quantity'] ?></td><td><?= $quote['custom_printing'] ? 'Yes' : 'No' ?></td><td><?= e(date('M d, Y', strtotime($quote['created_at']))) ?></td><td><span class="status-badge status-processing"><?= e($quote['status']) ?></span></td><td class="actions"><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="update_quote"><input type="hidden" name="quote_id" value="<?= (int)$quote['id'] ?>"><select name="status"><option <?= $quote['status']==='new'?'selected':'' ?>>new</option><option <?= $quote['status']==='contacted'?'selected':'' ?>>contacted</option><option <?= $quote['status']==='quoted'?'selected':'' ?>>quoted</option><option <?= $quote['status']==='closed'?'selected':'' ?>>closed</option></select><input name="internal_notes" placeholder="Internal notes" value="<?= e($quote['internal_notes']) ?>"><button type="submit">Save</button></form><form method="post" class="inline-form" onsubmit="return confirm('Delete this quote request?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete_row"><input type="hidden" name="table" value="quote_requests"><input type="hidden" name="id" value="<?= (int)$quote['id'] ?>"><button type="submit">Delete</button></form></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
