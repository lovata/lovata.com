<div class="layout responsive-sidebar">
    <div class="layout-cell">

        <div class="layout">
            <?php if ($breadcrumbContent = Block::placeholder('breadcrumb')): ?>
                <!-- Breadcrumb -->
                <nav class="control-breadcrumb breadcrumb-flush">
                    <?= $breadcrumbContent ?>
                </nav>
            <?php endif ?>

            <!-- Content -->
            <div class="layout-row">
                <div class="padded-container layout">
                    <?= Block::placeholder('form-contents') ?>
                </div>
            </div>
        </div>

    </div>
    <div class="layout-cell w-300 form-sidebar control-scrollpanel">
        <div class="layout-relative">
            <div class="layout-absolute">
                <div class="control-scrollbar" data-control="scrollbar">
                    <div class="padded-container">
                        <?= Block::placeholder('form-sidebar') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
