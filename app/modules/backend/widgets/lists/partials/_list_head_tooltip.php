<i class="<?= $column->tooltip['icon'] ?? 'icon-info-circle' ?>"
    data-bs-toggle="tooltip"
    data-bs-title="<?= $this->getHeaderTooltipValue($column) ?>"
    <?php if (($column->tooltip['placement'] ?? 'auto') !== 'auto'): ?>
        data-bs-placement="<?= $column->tooltip['placement'] ?>"
    <?php endif ?>
></i>
