<?php

use Bitrix\Main\Event;
use Bitrix\Crm\Service\Container;
use Bitrix\Main\Diag\Debug;
use Bitrix\Crm\Item;


class SmartProcessTitleHandler
{
    /**
     * @var bool Flag to prevent recursive event handling.
     */
    private static bool $isUpdating = false;


    private const ENTITY_TYPE_ID = 153; // Пример: замените на ваш ID

    /**
     * Registers the event handlers using generic, undocumented events.
     */
    public static function registerHandlers()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();

        $eventManager->addEventHandler(
            'crm',
            'onCrmDynamicItemAdd',
            [__CLASS__, 'handleItemEvent']
        );

        $eventManager->addEventHandler(
            'crm',
            'onCrmDynamicItemUpdate',
            [__CLASS__, 'handleItemEvent']
        );
    }

    /**
     * Handles both add and update events for smart process items.
     *
     * @param Event $event
     */
    public static function handleItemEvent(Event $event)
    {
        Debug::writeToFile(sprintf('handleItemEvent triggered for event: %s', $event->getEventType()), '', '__log.txt');

        /** @var Item $item */
        $item = $event->getParameter('item');
        if (!$item) {
            Debug::writeToFile('Exiting handleItemEvent: no item in event.', '', '__log.txt');
            return;
        }

        if ($item->getEntityTypeId() !== self::ENTITY_TYPE_ID) {
            // Not our smart process, ignore.
            return;
        }

        $id = $item->getId();
        $currentTitle = $item->getTitle();
        Debug::writeToFile(['id' => $id, 'title' => $currentTitle, 'entityTypeId' => $item->getEntityTypeId()], 'handleItemEvent data', '__log.txt');

        $expectedSuffix = sprintf(' (%d)', $id);

        if (str_ends_with($currentTitle, $expectedSuffix)) {
            Debug::writeToFile('Exiting handleItemEvent: title already has correct suffix.', '', '__log.txt');
            return;
        }

        // Remove any old ID suffix and add the correct one.
        $baseTitle = preg_replace('/\s*\(\d+\)$/', '', $currentTitle);
        $newTitle = $baseTitle . $expectedSuffix;

        self::updateItemTitle($item->getEntityTypeId(), $id, $newTitle);
    }

    /**
     * Updates the title of a smart process item.
     *
     * @param int $entityTypeId
     * @param int $id
     * @param string $newTitle
     */
    private static function updateItemTitle(int $entityTypeId, int $id, string $newTitle)
    {
        if (self::$isUpdating) {
            return;
        }

        self::$isUpdating = true;
        Debug::writeToFile(sprintf('Attempting to update item %d with title "%s"', $id, $newTitle), '', '__log.txt');

        try {
            $factory = Container::getInstance()->getFactory($entityTypeId);
            if ($factory) {
                $item = $factory->getItem($id);
                if ($item) {
                    $item->set('TITLE', $newTitle);
                    $saveResult = $item->save();
                    if (!$saveResult->isSuccess()) {
                        Debug::writeToFile($saveResult->getErrorMessages(), 'Save Error', '__log.txt');
                    } else {
                        Debug::writeToFile(sprintf('Item %d successfully updated.', $id), '', '__log.txt');
                    }
                }
            }
        } catch (\Exception $e) {
            Debug::writeToFile($e->getMessage(), 'Exception caught', '__log.txt');
            Debug::writeToFile($e->getTraceAsString(), 'Exception trace', '__log.txt');
        } finally {
            self::$isUpdating = false;
        }
    }
} 