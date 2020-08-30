<?php

declare(strict_types=1);
namespace Paustian\QuickcheckModule\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Translation\Bundle\EditInPlace\Activator as EditInPlaceActivator;
use Zikula\MenuModule\ExtensionMenu\ExtensionMenuInterface;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;

class ExtensionMenu implements ExtensionMenuInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var PermissionApiInterface
     */
    private $permissionApi;


    public function __construct(
        FactoryInterface $factory,
        PermissionApiInterface $permissionApi
    ) {
        $this->factory = $factory;
        $this->permissionApi = $permissionApi;
    }

    public function get(string $type = self::TYPE_ADMIN): ?ItemInterface
    {
        if (self::TYPE_ADMIN === $type) {
            return $this->getAdmin();
        }
        return null;
    }

    private function getAdmin(): ?ItemInterface
    {
        if (!$this->permissionApi->hasPermission($this->getBundleName() . '::', '::', ACCESS_ADMIN)) {
            return null;
        }

        $menu = $this->factory->createItem('websiteFeeMain');

        //WebsiteFee functions
        $menu->addChild('Create Subscription', [
            'route' => 'paustianwebsitefeemodule_admin_edit',
        ])->setAttribute('icon', 'fas fa-plus');


        $menu->addChild('Modify Subscription', [
            'route' => 'paustianwebsitefeemodule_admin_modify',
        ])->setAttribute('icon', 'fas fa-list');

        $menu->addChild('View/Delete Transactions', [
            'route' => 'paustianwebsitefeemodule_admin_modifytrans',
        ])->setAttribute('icon', 'fas fa-list');

        $menu->addChild('View/Delete Errors', [
            'route' => 'paustianwebsitefeemodule_admin_modifyerrs',
        ])->setAttribute('icon', 'fas fa-list');

        $menu->addChild('Test Subscription Locally', [
            'route' => 'paustianwebsitefeemodule_subscribe_testsubscribe',
        ])->setAttribute('icon', 'fas fa graduation-cap');

        return 0 === $menu->count() ? null : $menu;
    }


    public function getBundleName(): string
    {
        return 'PaustianWebsiteFeeModule';
    }
}