<?php

declare(strict_types=1);

namespace OCA\RestrictUserSettings\Settings;

use OCA\RestrictUserSettings\AppInfo\Application;
use OCA\RestrictUserSettings\Service\VisibilityConfigService;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\ISettings;
use OCP\Util;

class AdminSettings implements ISettings {

    public const KEY_HIDDEN_SECTIONS = 'hidden_sections';
    public const KEY_SECURITY_ONLY_DEVICES = 'security_only_devices';

    /** Section IDs that can be hidden (must match JS selectors) */
    public const SECTIONS = [
        'personal_info' => 'Personal info',
        'notifications' => 'Notifications',
        'sharing' => 'Sharing',
        'appearance' => 'Appearance & accessibility',
        'availability' => 'Availability flow',
        'privacy' => 'Privacy',
    ];

    public function __construct(
        private VisibilityConfigService $visibilityConfig,
        private IL10N $l10n,
        private IURLGenerator $urlGenerator,
    ) {
    }

    public function getForm(): TemplateResponse {
        $hidden = $this->visibilityConfig->getHiddenSections();
        $securityOnlyDevices = $this->visibilityConfig->getSecurityOnlyDevices();

        $sectionChecks = [];
        foreach (self::SECTIONS as $id => $label) {
            $sectionChecks[$id] = [
                'id' => $id,
                'label' => $this->l10n->t($label),
                'hidden' => in_array($id, $hidden, true),
            ];
        }

        $saveUrl = $this->urlGenerator->linkToRoute('restrict_user_settings.admin_settings.save');

        return new TemplateResponse(
            Application::APP_ID,
            'admin',
            [
                'sectionChecks' => $sectionChecks,
                'securityOnlyDevices' => $securityOnlyDevices,
                'saveUrl' => $saveUrl,
                'requesttoken' => Util::getRequestToken(),
            ]
        );
    }

    public function getSection(): string {
        return Application::APP_ID;
    }

    public function getPriority(): int {
        return 0;
    }
}
