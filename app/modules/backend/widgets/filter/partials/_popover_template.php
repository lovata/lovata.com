<script type="text/template" id="<?= $this->getId('popoverTemplate') ?>">
    <form data-request-parent-form="#<?= $this->getId() ?>">
        <input type="hidden" name="scopeName" value="{{ scopeName }}" />
        <div class="control-filter-popover control-filter-box-popover">
            <div class="loading-indicator-container">
                <div class="loading-indicator size-small">
                    <span></span>
                </div>
            </div>
        </div>
    </form>
</script>
