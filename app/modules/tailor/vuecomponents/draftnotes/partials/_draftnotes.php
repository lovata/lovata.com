<div class="draftnotes">
    <input type="text" name="Draft[notes]" v-model="state.initial.draftNotes" placeholder="<?= __('Draft name: draft notes') ?>">
    <input type="hidden" name="Draft[name]" v-bind:value="draftName">
</div>
