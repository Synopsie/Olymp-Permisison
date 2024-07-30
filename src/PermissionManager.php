<?php

/*
 *  ____   __   __  _   _    ___    ____    ____    ___   _____
 * / ___|  \ \ / / | \ | |  / _ \  |  _ \  / ___|  |_ _| | ____|
 * \___ \   \ V /  |  \| | | | | | | |_) | \___ \   | |  |  _|
 *  ___) |   | |   | |\  | | |_| | |  __/   ___) |  | |  | |___
 * |____/    |_|   |_| \_|  \___/  |_|     |____/  |___| |_____|
 *
 * API allowing to save permissions in a simplified way,
 * without going through the plugin.yml or registering classes.
 *
 * @author Synopsie
 * @link https://github.com/Synopsie
 * @version 1.0.0
 *
 */

declare(strict_types=1);

namespace olymp;

use InvalidArgumentException;
use olymp\event\RegisterPermissionEvent;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use ReflectionClass;
use UnitEnum;
use function in_array;
use function is_string;
use function str_replace;
use function str_starts_with;

class PermissionManager {
	private RegistryPermissionCache $registryPermissionCache;

	public function getRegistryPermissionCache() : RegistryPermissionCache {
		return $this->registryPermissionCache ?? $this->registryPermissionCache = new RegistryPermissionCache();
	}

	public function registerPermission(Permission|string $permission, string $name = null, string $defaultGroup = DefaultPermissions::ROOT_OPERATOR) : void {
		$consoleRoot      = DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT_CONSOLE));
		$operatorRoot     = DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT_OPERATOR, '', [$consoleRoot]));
		$permissionsCache = $this->getRegistryPermissionCache();

		if (in_array($name, $permissionsCache->getPermissions(), true)) {
			return;
		}

		if (is_string($permission)) {
			if ($name === null) {
				$name = str_replace('.', '_', $permission);
			}
			$permission = new Permission($permission);
		}

		$ev = new RegisterPermissionEvent($name, $permission, $defaultGroup);
		$ev->call();

		if ($ev->isCancelled()) {
			return;
		}

		$permissionsCache->addPermission($ev->getName(), $ev->getPermission());

		switch ($ev->getDefaultGroup()) {
			case 'everyone':
				DefaultPermissions::registerPermission($ev->getPermission());
				break;
			case DefaultPermissions::ROOT_OPERATOR:
				DefaultPermissions::registerPermission($ev->getPermission(), [$operatorRoot]);
				break;
			case DefaultPermissions::ROOT_CONSOLE:
				DefaultPermissions::registerPermission($ev->getPermission(), [$consoleRoot]);
				break;
			default:
				throw new InvalidArgumentException("Invalid default group: $defaultGroup");
		}
	}

	/**
	 * @param UnitEnum[] $enums
	 */
	public function registerEnumPermission(array $enums) : void {
		foreach ($enums as $enum) {
			if ($enum instanceof UnitEnum) {
				$this->registerPermission($enum->value, $enum->name);
			}
		}
	}

	public function registerPermissionClass(object $class) : void {
		$reflection = new ReflectionClass($class);
		foreach ($reflection->getConstants() as $name => $value) {
			if (is_string($value) && str_starts_with($name, 'PERMISSION_')) {
				$this->registerPermission($value, $name);
			}
		}
	}

	/**
	 * @return Permission[]
	 */
	public function getPermissions() : array {
		return $this->getRegistryPermissionCache()->getPermissions();
	}

	public function getPermission(string $name) : Permission {
		foreach ($this->getPermissions() as $permission) {
			if ($permission->getName() === $name) {
				return $permission;
			}
		}
		throw new InvalidArgumentException("Permission $name not found");
	}

}
