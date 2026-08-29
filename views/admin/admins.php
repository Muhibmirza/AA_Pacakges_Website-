<section class="admin-card">
    <div class="card-head"><h2>Admins</h2><button class="btn btn-primary">Add Admin</button></div>
    <table class="admin-table"><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th><th class="actions-col">Actions</th></tr></thead><tbody>
    <?php foreach ($admins as $admin): ?><tr><td><?= e($admin['name']) ?></td><td><?= e($admin['email']) ?></td><td><?= e($admin['role']) ?></td><td><span class="status-badge <?= $admin['is_active'] ? 'status-delivered' : 'status-cancelled' ?>"><?= $admin['is_active'] ? 'Active' : 'Inactive' ?></span></td><td><?= e(date('M d, Y', strtotime($admin['created_at']))) ?></td><td class="actions"><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="update_admin_status"><input type="hidden" name="admin_id" value="<?= (int)$admin['id'] ?>"><label class="checkbox-line"><input type="checkbox" name="is_active" value="1" <?= $admin['is_active'] ? 'checked' : '' ?>> Active</label><button type="submit">Save</button></form></td></tr><?php endforeach; ?>
    </tbody></table>
</section>
