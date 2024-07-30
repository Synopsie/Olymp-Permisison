<?php
declare(strict_types=1);

namespace olymp\event;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\permission\Permission;

class RegisterPermissionEvent extends Event implements Cancellable {
    use CancellableTrait;

    private string $name;
    private Permission $permission;
    private string $defaultGroup;

    public function __construct(string $name, Permission $permission, string $defaultGroup) {
        $this->name = $name;
        $this->permission = $permission;
        $this->defaultGroup = $defaultGroup;
    }

    public function getPermission() : Permission {
        return $this->permission;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getDefaultGroup() : string {
        return $this->defaultGroup;
    }

    public function setPermission(Permission $permission) : void {
        $this->permission = $permission;
    }

    public function setName(string $name) : void {
        $this->name = $name;
    }

    public function setDefaultGroup(string $defaultGroup) : void {
        $this->defaultGroup = $defaultGroup;
    }

}