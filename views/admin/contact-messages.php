<section class="admin-card">
    <div class="card-head"><div><h2>Contact Messages</h2><p class="muted-copy">Messages submitted through the Contact Us page.</p></div></div>
    <form class="table-tools" method="get" action="<?= admin_route('contact-messages') ?>">
        <input name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Search sender or message">
        <button class="btn btn-outline" type="submit">Search</button>
        <a class="btn btn-outline" href="<?= admin_route('contact-messages') ?>">Reset</a>
    </form>
    <div class="table-scroll">
        <table class="admin-table">
            <thead><tr><th>Sender</th><th>Contact</th><th>Message</th><th>Date</th><th>Status</th><th class="actions-col">Actions</th></tr></thead>
            <tbody>
            <?php foreach ($messages as $item): ?>
                <tr>
                    <td><?= e($item['name']) ?></td>
                    <td><a href="mailto:<?= e($item['email']) ?>"><?= e($item['email']) ?></a><small><?= e($item['phone']) ?></small></td>
                    <td class="message-cell"><?= nl2br(e($item['message'])) ?></td>
                    <td><?= e(date('M d, Y g:i A', strtotime($item['created_at']))) ?></td>
                    <td><span class="status-badge status-<?= $item['status'] === 'new' ? 'pending' : 'processing' ?>"><?= e(ucfirst($item['status'])) ?></span></td>
                    <td class="actions">
                        <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="update_contact_message"><input type="hidden" name="message_id" value="<?= (int)$item['id'] ?>"><select name="status"><?php foreach (['new','read','replied','closed'] as $status): ?><option value="<?= $status ?>" <?= $item['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option><?php endforeach; ?></select><button type="submit">Save</button></form>
                        <form method="post" class="inline-form" onsubmit="return confirm('Delete this contact message?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete_row"><input type="hidden" name="table" value="contact_messages"><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><button type="submit">Delete</button></form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$messages): ?><tr><td colspan="6" class="empty-cell">No contact messages found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
