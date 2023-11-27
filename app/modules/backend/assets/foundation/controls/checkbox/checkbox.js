/*
 * Checkbox control
 */

(function($) {

    //
    // Intermediate checkboxes
    //

    $(document).render(function() {
        $('.form-check.is-indeterminate > input').each(function() {
            var $el = $(this),
                checked = $el.data('checked');

            switch (checked) {

                // Unchecked
                case 1:
                    $el.prop('indeterminate', true);
                    break;

                // Checked
                case 2:
                    $el.prop('indeterminate', false);
                    $el.prop('checked', true);
                    break;

                // Unchecked
                default:
                    $el.prop('indeterminate', false);
                    $el.prop('checked', false);
            }
        })
    })

    $(document).on('click', '.form-check.is-indeterminate > input', function() {
        var $el = $(this),
            checked = $el.data('checked');

        if (checked === undefined) {
            checked = $el.is(':checked') ? 1 : 0;
        }

        switch (checked) {

            // Unchecked, going indeterminate
            case 0:
                $el.data('checked', 1);
                $el.prop('indeterminate', true);
                break;

            // Indeterminate, going checked
            case 1:
                $el.data('checked', 2);
                $el.prop('indeterminate', false);
                $el.prop('checked', true);
                break;

            // Checked, going unchecked
            default:
                $el.data('checked', 0);
                $el.prop('indeterminate', false);
                $el.prop('checked', false);
        }
    });

    //
    // Checkbox Ranges
    //

    if ($.oc === undefined) {
        $.oc = {};
    }

    $.oc.checkboxRangeDetail = {
        lastCheckbox: null,
        isLastChecked: true
    };

    $.oc.checkboxRangeRegisterClick = function(ev, containerSelector, checkboxSelector) {
        var el = ev.target,
            detail = $.oc.checkboxRangeDetail;

        var selectCheckboxesIn = function(rows, isChecked) {
            rows.forEach(function(row) {
                row.querySelectorAll(checkboxSelector).forEach(function(el) {
                    el.checked = isChecked;
                    $(el).trigger('change'); // @todo needs triggerNativeChange?
                });
            });
        }

        var selectCheckboxRange = function($el, $prevEl) {
            var $item = $el.closest(containerSelector),
                $prevItem = $prevEl.closest(containerSelector),
                toSelect = [];

            var $nextRow = $item;
            while ($nextRow) {
                if ($nextRow === $prevItem) {
                    selectCheckboxesIn(toSelect, detail.isLastChecked);
                    return;
                }

                toSelect.push($nextRow);
                $nextRow = $nextRow.nextElementSibling;
            }

            toSelect = [];
            var $prevRow = $item;
            while ($prevRow) {
                if ($prevRow === $prevItem) {
                    selectCheckboxesIn(toSelect, detail.isLastChecked);
                    return;
                }

                toSelect.push($prevRow);
                $prevRow = $prevRow.previousElementSibling;
            }
        }

        if (detail.lastCheckbox && ev.shiftKey) {
            selectCheckboxRange(el, detail.lastCheckbox);
        }
        else {
            detail.lastCheckbox = el;
            detail.isLastChecked = el.checked;
        }
    }

})(jQuery);
