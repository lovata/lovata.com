<?php namespace Lovata\FeedbackForCrm\Classes\Event\Record;

use Lovata\FeedbackForCrm\Classes\Event\Settings\AmoCrmSettingsModelHandler;
use Lovata\FeedbackForCrm\Classes\Queue\SendLeadsToAmoCRMQueue;
use Martin\Forms\Models\Record;
use Queue;

/**
 * Class RecordModelHandler
 * @package Lovata\FeedbackForCrm\Classes\Event\Record
 * @author  Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class RecordModelHandler
{
    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        if (\System\Classes\PluginManager::instance()->hasPlugin('Martin.Forms')
            && !\System\Classes\PluginManager::instance()->isDisabled('Martin.Forms')) {
            \Martin\Forms\Models\Record::extend(function ($obRecord) {
                /** @var Record $obRecord */
                $obRecord->bindEvent('model.afterCreate', function () use ($obRecord) {
                    $this->afterCreate($obRecord);
                });
            });
        }
    }

    /**
     * After create settings.
     * @param Record $obRecord
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function afterCreate($obRecord)
    {
        $arQueueData = [
            'data'           => $obRecord->form_data_arr,
            'form'           => AmoCrmSettingsModelHandler::FORM_MAGIC_FORMS,
            'pipeline_value' => $obRecord->group == '(Empty)' ? '' : $obRecord->group,
        ];

        Queue::pushOn(SendLeadsToAmoCRMQueue::QUEUE_NAME, SendLeadsToAmoCRMQueue::class, $arQueueData);
    }
}
