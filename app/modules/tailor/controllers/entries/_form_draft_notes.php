<div class="form-horizontal">
    <div class="form-group">
        <label class="control-label col-md-2">Draft Name</label>
        <div class="col-md-10">
            <!-- Text -->
            <input
                type="text"
                name="Draft[name]"
                value="<?= $formModel->getDraftName() ?>"
                placeholder="Draft Name"
                class="form-control"
                autocomplete="off"
                maxlength="255"
            />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-2">Draft Notes</label>
        <div class="col-md-10">
            <textarea
                name="Draft[notes]"
                class="form-control field-textarea size-tiny"
                placeholder="Notes about this draft"
                ><?= e($formModel->getDraftNotes()) ?></textarea>
        </div>
    </div>
</div>
