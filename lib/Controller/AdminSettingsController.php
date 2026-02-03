<?php

declare(strict_types=1);

namespace OCA\RestrictUserSettings\Controller;

use OCA\RestrictUserSettings\AppInfo\Application;
use OCA\RestrictUserSettings\Service\VisibilityConfigService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\IGroupManager;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\IUserSession;

class AdminSettingsController extends Controller {

    public function __construct(
        IRequest $request,
        private VisibilityConfigService $visibilityConfig,
        private IURLGenerator $urlGenerator,
        private IUserSession $userSession,
        private IGroupManager $groupManager,
    ) {
        parent::__construct(Application::APP_ID, $request);
    }

    /**
     * Save admin form. Expects POST with hidden_sections[] and security_only_devices.
     * Only admins can save; others are redirected. CSRF is enforced by Nextcloud when requesttoken is present in form.
     */
    public function save(): RedirectResponse {
        $user = $this->userSession->getUser();
        if ($user === null || !$this->groupManager->isAdmin($user->getUID())) {
            return new RedirectResponse($this->urlGenerator->linkToRoute('settings.PersonalSettings.index'));
        }

        if ($this->request->getMethod() !== 'POST') {
            return new RedirectResponse($this->urlGenerator->linkToRoute('settings.AdminSettings.index', ['section' => Application::APP_ID]));
        }

        $sections = $this->request->getParam('hidden_sections', []);
        if (!is_array($sections)) {
            $sections = [];
        }
        $securityOnlyDevices = $this->request->getParam('security_only_devices') === '1';

        $this->visibilityConfig->setVisibilityConfig($sections, $securityOnlyDevices);

        $url = $this->urlGenerator->linkToRoute('settings.AdminSettings.index', ['section' => Application::APP_ID]);
        return new RedirectResponse($url);
    }
}
