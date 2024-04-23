<?php if ($relationManageFormWidget->getModel() instanceof \Tailor\Models\EntryRecord): ?>
    <?= $this->makePartial('edit_popup', $_context) ?>
<?php else: ?>
    <?= $this->asExtension('RelationController')->makePartial('manage_form', $_context) ?>
<?php endif ?>
