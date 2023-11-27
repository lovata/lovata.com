<div class="preview-placeholder" preview-preset="<?= e($preset) ?>">
    <span class="p-header">
        <span class="p-nav">
            <span class="p-item is-active">
                <span class="p-icon"></span>
                <span class="p-text"></span>
            </span>
            <span class="p-item">
                <span class="p-icon"></span>
                <span class="p-text"></span>
            </span>
            <span class="p-item">
                <span class="p-icon"></span>
                <span class="p-text"></span>
            </span>
        </span>
        <span class="p-controlpanel">
            <span class="p-avatar"></span>
        </span>
    </span>
    <span class="p-sidebar">
        <span class="p-link is-active">
            <span class="p-icon"></span>
            <span class="p-text"></span>
        </span>
        <span class="p-link">
            <span class="p-icon"></span>
            <span class="p-text"></span>
        </span>
        <span class="p-link">
            <span class="p-icon"></span>
            <span class="p-text"></span>
        </span>
        <span class="p-link">
            <span class="p-icon"></span>
            <span class="p-text"></span>
        </span>
    </span>
    <span class="p-content">
        <span class="p-form">
            <span class="p-input span-right"></span>
            <span class="p-input span-left"></span>
            <span class="p-input"></span>
            <span class="p-input size-medium"></span>
        </span>
        <span class="p-buttons">
            <span class="p-button is-primary"></span>
            <span class="p-button"></span>
        </span>
    </span>
</div>
<style>
[data-bs-theme="light"] [preview-preset="<?= e($preset) ?>"] {
    --bs-primary: <?= $vars['brand-primary'] ?>;
    --bs-secondary: <?= $vars['brand-secondary'] ?>;
    --oc-selection: <?= $vars['brand-selection'] ?>;

    --oc-mainnav-color: <?= $vars['brand-mainnav-color'] ?>;
    --oc-mainnav-bg: <?= $vars['brand-mainnav-bg'] ?>;

    --oc-sidebar-color: <?= $vars['brand-sidebar-color'] ?>;
    --oc-sidebar-bg: <?= $vars['brand-sidebar-bg'] ?>;
    --oc-sidebar-active-color: <?= $vars['brand-sidebar-active-color'] ?>;
    --oc-sidebar-active-bg: <?= $vars['brand-sidebar-active-bg'] ?>;
    --oc-sidebar-hover-bg: <?= $vars['brand-sidebar-hover-bg'] ?>;
}
[data-bs-theme="dark"] [preview-preset="<?= e($preset) ?>"] {
    --bs-primary: <?= $vars['brand-primary-dark'] ?>;
    --bs-secondary: <?= $vars['brand-secondary-dark'] ?>;
    --oc-selection: <?= $vars['brand-selection-dark'] ?>;

    --oc-mainnav-color: <?= $vars['brand-mainnav-color-dark'] ?>;
    --oc-mainnav-bg: <?= $vars['brand-mainnav-bg-dark'] ?>;

    --oc-sidebar-color: <?= $vars['brand-sidebar-color-dark'] ?>;
    --oc-sidebar-bg: <?= $vars['brand-sidebar-bg-dark'] ?>;
    --oc-sidebar-active-color: <?= $vars['brand-sidebar-active-color-dark'] ?>;
    --oc-sidebar-active-bg: <?= $vars['brand-sidebar-active-bg-dark'] ?>;
    --oc-sidebar-hover-bg: <?= $vars['brand-sidebar-hover-bg-dark'] ?>;
}
</style>
