<?php

declare(strict_types=1);

namespace OCA\RestrictUserSettings\AppInfo;

use OCA\RestrictUserSettings\Listener\SettingsScriptListener;
use OCP\AppFramework\App;
use OCP\AppFramework\Http\Events\BeforeTemplateRenderedEvent;
use OCP\EventDispatcher\IEventDispatcher;

class Application extends App {

    public const APP_ID = 'restrict_user_settings';

    public function __construct() {
        parent::__construct(self::APP_ID);

        $container = $this->getContainer();
        /** @var IEventDispatcher $dispatcher */
        $dispatcher = $container->query(IEventDispatcher::class);
        $dispatcher->addServiceListener(
            BeforeTemplateRenderedEvent::class,
            SettingsScriptListener::class
        );
    }
}
