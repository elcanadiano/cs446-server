<?php $this->load->view('admin/header.php') ?>

<?php if (!isset($query_result) || !$query_result): ?>
	<p>There are no users created. Would you like to create one?</p>
<?php else: ?>
	<table>
		<tr>
			<th>User ID</th>
			<th>Username</th>
			<th>Functions</th>
		</tr>
		<?php foreach ($query_result as $row): ?>
			<tr>
				<td><?= $row->id ?></td>
				<td><?= $row->username ?></td>
				<td>
					<a href="/admin/user/change_password/<?= $row->username ?>">Change Password</a>
				</td>
			</tr>
		<?php endforeach ?>
	</table>
<?php endif ?>

<?php $this->load->view('admin/footer.php') ?>