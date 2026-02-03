<?php
/** @var \OCP\IL10N $l */
/** @var array $_ */
$sectionChecks = $_['sectionChecks'] ?? [];
$securityOnlyDevices = $_['securityOnlyDevices'] ?? false;
$saveUrl = $_['saveUrl'] ?? '';
?>
<div id="restrict_user_settings" class="section">
	<h2><?php p($l->t('Restrict user settings')); ?></h2>
	<p class="settings-hint"><?php p($l->t('For non-admin users, the checked sections below will be hidden in Settings → User. Admins always see all sections.')); ?></p>

	<form id="restrict_user_settings_form" method="post" action="<?php p($saveUrl); ?>">
		<input type="hidden" name="requesttoken" value="<?php p($_['requesttoken'] ?? ''); ?>" />
		<fieldset>
			<legend><?php p($l->t('Hide these sections for non-admin users')); ?></legend>
			<ul class="restrict-sections-list">
				<?php foreach ($sectionChecks as $item): ?>
				<li>
					<input type="checkbox" id="hide_<?php p($item['id']); ?>"
						name="hidden_sections[]" value="<?php p($item['id']); ?>"
						class="checkbox" <?php if ($item['hidden']) echo 'checked="checked"'; ?> />
					<label for="hide_<?php p($item['id']); ?>"><?php p($item['label']); ?></label>
				</li>
				<?php endforeach; ?>
			</ul>
		</fieldset>

		<fieldset>
			<legend><?php p($l->t('Security section')); ?></legend>
			<p>
				<input type="checkbox" id="security_only_devices" name="security_only_devices" value="1"
					class="checkbox" <?php if ($securityOnlyDevices) echo 'checked="checked"'; ?> />
				<label for="security_only_devices"><?php p($l->t('Show only "Devices & sessions" in Security (hide password, 2FA, etc.)')); ?></label>
			</p>
		</fieldset>

		<input type="submit" class="button primary" value="<?php p($l->t('Save')); ?>" />
	</form>
</div>
