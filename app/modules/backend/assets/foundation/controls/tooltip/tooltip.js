/*
 * Implement the tooltip control automatically
 *
 * Usage:
 *
 *   <a
 *       href="javascript:;"
 *       data-toggle="tooltip"
 *       data-placement="left"
 *       title="Tooltip content">
 *       Some link
 *   </a>
 *
 * Require:
 *  - bootstrap/tooltip
 */

(function($){

    $(document).render(function(){
        $('[data-control="tooltip"], [data-bs-toggle="tooltip"], [data-toggle="tooltip"]').tooltip()
    })

})(jQuery);
