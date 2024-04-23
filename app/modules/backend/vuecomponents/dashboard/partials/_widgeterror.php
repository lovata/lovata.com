<div class="widget-error">
    <span class="ph ph-warning"></span>
    <p>
        <?= e(trans('backend::lang.dashboard.widget_loading_error')) ?>
        <template v-if="store.state.canCreateAndEdit"> 
            <a href="javascript:;" @click.stop.prevent="$emit('configure')"><?= e(trans('backend::lang.dashboard.widget_click_here')) ?></a>
            <?= e(trans('backend::lang.dashboard.widget_configure_prompt')) ?>
        </template>
    </p>
</div>