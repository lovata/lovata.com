import Helpers from '../../common/js/helpers';

export default new class RequestForm {
  constructor() {
    this.formClass = '.request-form__form';

    if (!$(this.formClass).length) return;

    this.btnSubmitClass = '.request-form__submit';
    this.formAreaClass = '.request-form__area';

    this.classEnable = '_enable';
    this.classAreaEmptyError = '_error-empty';

    this.eventsHandler()
  }

  eventsHandler() {
    let $doc = $(document);

    if (Helpers.isMicrosoftBrowser()) {
      $doc.on('input', this.formAreaClass, (e) => {
        let $area = $(e.currentTarget),
          $btnSubmit = $(this.btnSubmitClass);

        // Check current area for validity
        if ($area.is(':valid')) {
          $area.removeClass(this.classAreaEmptyError);
        } else {
          $area.addClass(this.classAreaEmptyError);
        }

        // Check full form for validity
        if (this.isFormValid()) {
          $btnSubmit.addClass(this.classEnable);
        } else {
          $btnSubmit.removeClass(this.classEnable);
        }
      })
    }
  }

  isFormValid() {
    let result = true;

    $(this.formAreaClass).each((i, item) => {
      if (!$(item).is(':valid')) {
        return result = false;
      }
    });

    return result; 
  }
}
