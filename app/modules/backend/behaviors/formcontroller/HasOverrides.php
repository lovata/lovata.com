<?php namespace Backend\Behaviors\FormController;

use Backend;

/**
 * HasOverrides in the controller
 */
trait HasOverrides
{
    /**
     * formGetRedirectUrl returns a URL based on supplied context,
     * relative URLs are treated as backend URLs
     * @param string $context
     * @param \Model $model
     * @return string
     */
    public function formGetRedirectUrl($context = null, $model = null)
    {
    }

    /**
     * formBeforeSave is called before the creation or updating form is saved
     * @param \Model
     */
    public function formBeforeSave($model)
    {
    }

    /**
     * formAfterSave is called after the creation or updating form is saved
     * @param \Model
     */
    public function formAfterSave($model)
    {
    }

    /**
     * formBeforeCreate is called before the creation form is saved
     * @param \Model
     */
    public function formBeforeCreate($model)
    {
    }

    /**
     * formAfterCreate is called after the creation form is saved
     * @param \Model
     */
    public function formAfterCreate($model)
    {
    }

    /**
     * formBeforeUpdate is called before the updating form is saved
     * @param \Model
     */
    public function formBeforeUpdate($model)
    {
    }

    /**
     * formAfterUpdate is called after the updating form is saved
     * @param \Model
     */
    public function formAfterUpdate($model)
    {
    }

    /**
     * formAfterDelete called after the form model is deleted
     * @param \Model
     */
    public function formAfterDelete($model)
    {
    }

    /**
     * formAfterCancel called after the user has cancelled the form
     * @param \Model
     */
    public function formAfterCancel($model)
    {
    }

    /**
     * formCreateModelObject creates a new instance of a form model. This logic can
     * be changed by overriding it in the controller.
     * @return Model
     */
    public function formCreateModelObject()
    {
        return $this->createModel();
    }

    /**
     * formExtendFieldsBefore is called before the form fields are defined
     * @param Backend\Widgets\Form $host The hosting form widget
     * @return void
     */
    public function formExtendFieldsBefore($host)
    {
    }

    /**
     * formExtendFields is called after the form fields are defined
     * @param Backend\Widgets\Form $host The hosting form widget
     * @param array $fields Array of all defined form field objects (\Backend\Classes\FormField)
     * @return void
     */
    public function formExtendFields($host, $fields)
    {
    }

    /**
     * formExtendRefreshData is called before the form is refreshed, should return an array
     * of additional save data.
     * @param Backend\Widgets\Form $host The hosting form widget
     * @param array $saveData Current save data
     * @return array
     */
    public function formExtendRefreshData($host, $saveData)
    {
    }

    /**
     * formExtendRefreshFields is called when the form is refreshed, giving the opportunity
     * to modify the form fields.
     * @param Backend\Widgets\Form $host The hosting form widget
     * @param array $fields Current form fields
     * @return array
     */
    public function formExtendRefreshFields($host, $fields)
    {
    }

    /**
     * formExtendRefreshResults is called after the form is refreshed, should return an
     * array of additional result parameters.
     * @param Backend\Widgets\Form $host The hosting form widget
     * @param array $result Current result parameters.
     * @return array
     */
    public function formExtendRefreshResults($host, $result)
    {
    }

    /**
     * formExtendModel extends the supplied model used by create and update actions, the model can
     * be altered by overriding it in the controller.
     * @param \Model $model
     * @return Model
     */
    public function formExtendModel($model)
    {
    }

    /**
     * formExtendQuery extends the query used for finding the form model. Extra conditions
     * can be applied to the query, for example, $query->withTrashed();
     * @param October\Rain\Database\Builder $query
     * @return void
     */
    public function formExtendQuery($query)
    {
    }
}
