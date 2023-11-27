<script type="text/template" id="<?= $this->getId('popoverTemplate') ?>">
    <form>
        <input type="hidden" name="scopeName" value="{{ scopeName }}" />
        <?php foreach ($extraData as $name => $value): ?>
            <input type="hidden" name="<?= e($name) ?>" value="<?= e($value) ?>" />
        <?php endforeach ?>
        <div class="control-filter-popover control-filter-box-popover">
            <div class="loading-indicator-container">
                <div class="loading-indicator size-small">
                    <span></span>
                </div>
            </div>
        </div>
    </form>
</script>
