<div>
    <!-- Manage Project -->
    <div class="browse-products-container" style="max-width: 960px">
        <div class="loading-indicator-container p-t-lg" id="manageProjectLoader">
            <div class="loading-indicator is-transparent">
                <span></span>
                <div><?= e(trans('system::lang.market.content_loading')) ?></div>
            </div>
        </div>

        <table class="table project-table m-b-0">
            <tbody
                id="manageProject"
                class="manage-project"
                data-handler="onBrowseProject"
                data-view="project/product"
                style="display: none">
                    <tr>
                        <td>
                            <p>
                                <?= __("Project has no plugins or themes. Visit the :link to add some.", ['link' => '<a href="https://octobercms.com/plugins" target="_blank">'.__("October CMS Marketplace").'</a>']) ?>
                            </p>
                        </td>
                    </tr>
                </tbody>
        </table>
    </div>
</div>

<script type="text/template" data-partial="project/product">
    <tr>
        <td style="width: 100px">
            <div class="image text-center"><img src="{{image}}" alt=""></div>
        </td>
        <td>
            <div class="details">
                <h4><a href="{{detailUrl}}">{{name}}</a> by {{author}}</h4>
                <p class="description">{{description}}</p>
            </div>
        </td>
        <td class="controls">
            <div class="card mb-1 ms-auto" style="width:10.5rem">
                <div class="card-body text-center">
                    {{^installed}}
                        <a
                            href="javascript:;"
                            data-control="popup"
                            data-handler="{{handler}}"
                            data-request-data="code: '{{code}}'"
                            class="btn btn-success oc-icon-plus">
                            <?= __("Install") ?>
                        </a>
                    {{/installed}}
                    {{#installed}}
                        <a
                            href="javascript:;"
                            data-control="popup"
                            data-handler="{{handler}}"
                            data-request-data="code: '{{code}}'"
                            class="btn btn-danger oc-icon-trash-o">
                            <?= __("Remove") ?>
                        </a>
                    {{/installed}}
                </div>
            </div>
        </td>
    </tr>
</script>
