<?php

declare(strict_types=1);

namespace OCA\RestrictUserSettings\Listener;

use OCA\RestrictUserSettings\AppInfo\Application;
use OCP\AppFramework\Http\Events\BeforeTemplateRenderedEvent;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\IUserSession;
use OCP\Util;

/** @implements IEventListener<BeforeTemplateRenderedEvent> */
class SettingsScriptListener implements IEventListener {

    public function __construct(
        private IUserSession $userSession,
    ) {
    }

    public function handle(Event $event): void {
        if (!$event instanceof BeforeTemplateRenderedEvent) {
            return;
        }
        if (!$event->isLoggedIn()) {
            return;
        }

        $user = $this->userSession->getUser();
        if ($user === null) {
            return;
        }

        Util::addScript(
            Application::APP_ID,
            'settings-restrict'
        );
    }
}
