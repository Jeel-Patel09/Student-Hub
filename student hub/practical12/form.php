<?php if ($errors): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= e($action) ?>" enctype="multipart/form-data" class="card card-body shadow-sm">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" value="<?= e($data['title']) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control" required><?= e($data['description']) ?></textarea>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Event Date</label>
            <input type="date" name="event_date" class="form-control" value="<?= e($data['event_date']) ?>" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Venue</label>
            <input type="text" name="venue" class="form-control" value="<?= e($data['venue']) ?>" required>
        </div>
        <div class="col-md-2 mb-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-select" required>
                <option value="">-- Select --</option>
                <?php foreach (CATEGORIES as $c): ?>
                    <option value="<?= e($c) ?>" <?= $data['category'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <?php foreach (STATUSES as $s): ?>
                    <option value="<?= e($s) ?>" <?= $data['status'] === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Poster (JPG, PNG or WEBP, max 2 MB)</label>
        <?php if (!empty($data['poster'])): ?>
            <div class="mb-2">
                <img src="uploads/<?= e($data['poster']) ?>" alt="Current poster" style="max-height:120px" class="border rounded">
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" name="remove_poster" value="1" id="rm">
                    <label class="form-check-label" for="rm">Remove current poster</label>
                </div>
            </div>
        <?php endif; ?>
        <input type="file" name="poster" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
        <?php if (!empty($data['poster'])): ?>
            <div class="form-text">Choosing a new file replaces the current poster.</div>
        <?php endif; ?>
    </div>

    <div>
        <button class="btn btn-primary"><?= e($button) ?></button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>