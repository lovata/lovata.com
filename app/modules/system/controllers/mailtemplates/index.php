<div class="control-tabs content-tabs tabs-flush" data-control="tab">
    <ul class="nav nav-tabs">
        <li class="<?= $activeTab == 'templates' ? 'active' : '' ?>">
            <a href="#templates" data-tab-url="<?= Backend::url('system/mailtemplates/index/templates') ?>">
                <?= __("Templates") ?>
            </a>
        </li>
        <li class="<?= $activeTab == 'layouts' ? 'active' : '' ?>">
            <a href="#layouts" data-tab-url="<?= Backend::url('system/mailtemplates/index/layouts') ?>">
                <?= __("Layouts") ?>
            </a>
        </li>
        <li class="<?= $activeTab == 'partials' ? 'active' : '' ?>">
            <a href="#partials" data-tab-url="<?= Backend::url('system/mailtemplates/index/partials') ?>">
                <?= __("Partials") ?>
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane <?= $activeTab == 'templates' ? 'active' : '' ?>">
            <?= $this->listRender('templates') ?>
        </div>
        <div class="tab-pane <?= $activeTab == 'layouts' ? 'active' : '' ?>">
            <?= $this->listRender('layouts') ?>
        </div>
        <div class="tab-pane <?= $activeTab == 'partials' ? 'active' : '' ?>">
            <?= $this->listRender('partials') ?>
        </div>
    </div>
</div>
