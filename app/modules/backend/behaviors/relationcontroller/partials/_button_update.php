<a
    data-control="popup"
    data-size="<?= $this->popupSize ?>"
    data-handler="onRelationButtonUpdate"
    data-request-data="manage_id: '<?= $relationManageId ?>'"
    href="javascript:;"
    class="btn btn-sm btn-secondary"
>
    <i class="octo-icon-settings"></i> <?= e($this->relationGetMessage('buttonUpdate')) ?>
</a>
