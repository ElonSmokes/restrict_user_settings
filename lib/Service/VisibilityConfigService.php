<?php

declare(strict_types=1);

namespace OCA\RestrictUserSettings\Service;

use OCA\RestrictUserSettings\AppInfo\Application;
use OCA\RestrictUserSettings\Settings\AdminSettings;
use OCP\IConfig;

/**
 * Single source of truth for visibility config (read + write).
 * Used by admin form, config API, and save controller to avoid duplication and key drift.
 */
class VisibilityConfigService {

    private const DEFAULT_HIDDEN_JSON = '["personal_info","notifications","sharing","appearance","availability","privacy"]';
    private const DEFAULT_SECURITY_ONLY_DEVICES = '1';

    public function __construct(
        private IConfig $config,
    ) {
    }

    /**
     * @return list<string>
     */
    public function getHiddenSections(): array {
        $raw = $this->config->getAppValue(
            Application::APP_ID,
            AdminSettings::KEY_HIDDEN_SECTIONS,
            self::DEFAULT_HIDDEN_JSON
        );
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function getSecurityOnlyDevices(): bool {
        $raw = $this->config->getAppValue(
            Application::APP_ID,
            AdminSettings::KEY_SECURITY_ONLY_DEVICES,
            self::DEFAULT_SECURITY_ONLY_DEVICES
        );
        return $raw === '1';
    }

    /**
     * Persist visibility config. Validates section IDs against allowed list.
     *
     * @param list<string> $hiddenSections
     */
    public function setVisibilityConfig(array $hiddenSections, bool $securityOnlyDevices): void {
        $allowed = array_keys(AdminSettings::SECTIONS);
        $filtered = array_values(array_intersect($hiddenSections, $allowed));

        $this->config->setAppValue(
            Application::APP_ID,
            AdminSettings::KEY_HIDDEN_SECTIONS,
            json_encode($filtered)
        );
        $this->config->setAppValue(
            Application::APP_ID,
            AdminSettings::KEY_SECURITY_ONLY_DEVICES,
            $securityOnlyDevices ? '1' : '0'
        );
    }
}
