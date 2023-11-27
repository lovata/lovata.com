<select
    name="referenceSearch"
    class="form-control custom-select"
    autocomplete="off"
    data-placeholder="<?= __("Search all references...") ?>"
    data-handler="<?= $field->searchHandler ?>"
    data-minimum-input-length="2"
    data-ajax--delay="300"
></select>
