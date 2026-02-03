<?php

declare(strict_types=1);

namespace OCA\RestrictUserSettings\Controller;

use OCA\RestrictUserSettings\AppInfo\Application;
use OCA\RestrictUserSettings\Service\VisibilityConfigService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IGroupManager;
use OCP\IRequest;
use OCP\IUserSession;

class ConfigController extends Controller {

    public function __construct(
        IRequest $request,
        private VisibilityConfigService $visibilityConfig,
        private IUserSession $userSession,
        private IGroupManager $groupManager,
    ) {
        parent::__construct(Application::APP_ID, $request);
    }

    /**
     * Return visibility config for the current user (for use on personal settings page).
     * Restriction is enforced client-side (UI hiding only); backend APIs are not restricted.
     *
     * @NoAdminRequired
     */
    public function getVisibility(): DataResponse {
        $user = $this->userSession->getUser();
        if ($user === null) {
            return new DataResponse(['error' => 'Not logged in'], 401);
        }

        $isAdmin = $this->groupManager->isAdmin($user->getUID());

        return new DataResponse([
            'isAdmin' => $isAdmin,
            'hiddenSections' => $this->visibilityConfig->getHiddenSections(),
            'securityOnlyDevices' => $this->visibilityConfig->getSecurityOnlyDevices(),
        ]);
    }
}
