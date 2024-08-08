<?php

/*
 *  ____   __   __  _   _    ___    ____    ____    ___   _____
 * / ___|  \ \ / / | \ | |  / _ \  |  _ \  / ___|  |_ _| | ____|
 * \___ \   \ V /  |  \| | | | | | | |_) | \___ \   | |  |  _|
 *  ___) |   | |   | |\  | | |_| | |  __/   ___) |  | |  | |___
 * |____/    |_|   |_| \_|  \___/  |_|     |____/  |___| |_____|
 *
 * API permettant de sauvegarder les permissions de manière simplifiée,
 *  sans passer par le plugin.yml ni enregistrer de classes.
 *
 * @author Synopsie
 * @link https://github.com/Synopsie
 * @version 1.0.2
 *
 */

declare(strict_types=1);

namespace olymp;

use pocketmine\permission\Permission;

final class RegistryPermissionCache {
	/** @var array<string, Permission>|null */
	private ?array $permissions = null;

	public function addPermission(string $name, Permission $permission) : void {
		$this->permissions[$name] = $permission;
	}

	/**
	 * @return Permission[]
	 */
	public function getPermissions() : array {
		return $this->permissions ?? [];
	}

}
