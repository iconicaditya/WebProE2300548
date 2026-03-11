<?php
$flashMessage = ems_get_flash();
if (!empty($flashMessage)):
?>
    <div class="container mt-3">
        <div class="alert alert-<?php echo ems_e($flashMessage['type']); ?> alert-dismissible fade show" role="alert">
            <?php echo ems_e($flashMessage['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>
