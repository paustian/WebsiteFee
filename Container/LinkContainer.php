<?php
/**
 * Created by PhpStorm.
 * User: paustian
 * Date: 10/21/17
 * Time: 8:22 PM
 */
namespace Paustian\WebsiteFeeModule\Container;

use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Core\LinkContainer\LinkContainerInterface;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;

class LinkContainer implements LinkContainerInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var PermissionApiInterface
     */
    private $permissionApi;

    /**
     * constructor.
     *
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     * @param PermissionApiInterface $permissionApi
     **/
    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        PermissionApiInterface $permissionApi
    )
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->permissionApi = $permissionApi;
    }

    /**
     * get Links of any type for this extension
     * required by the interface
     *
     * @param string $type
     * @return array
     */
    public function getLinks($type = LinkContainerInterface::TYPE_ADMIN)
    {
        if (LinkContainerInterface::TYPE_ADMIN == $type) {
            return $this->getAdmin();
        }
        if (LinkContainerInterface::TYPE_ACCOUNT == $type) {
            return $this->getAccount();
        }
        if (LinkContainerInterface::TYPE_USER == $type) {
            return $this->getUser();
        }

        return [];
    }

    /**
     * get the Admin links for this extension
     *
     * @return array
     */
    private function getAdmin()
    {
        $links = [];
        $links[] = array(
            'url' => $this->router->generate('paustianwebsitefeemodule_admin_edit'),
            'text' => $this->translator->__('Create Subscription'),
            'icon' => 'plus');
        $links[] = array(
            'url' => $this->router->generate('paustianwebsitefeemodule_admin_modify'),
            'text' => $this->translator->__('Modify Subscription'),
            'icon' => 'list');
        $links[] = array(
            'url' => $this->router->generate('paustianwebsitefeemodule_admin_modifytrans'),
            'text' => $this->translator->__('View/Delete Transactions'),
            'icon' => 'list');
        $links[] = array(
            'url' => $this->router->generate('paustianwebsitefeemodule_admin_modifyerrs'),
            'text' => $this->translator->__('View/Delete Errors'),
            'icon' => 'list');
        return $links;
    }

    private function getUser()
    {
        $links = [];

        return $links;
    }

    private function getAccount()
    {
        $links = [];

        return $links;
    }

    /**
     * set the BundleName as required by the interface
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'PaustianWebsiteFeeModule';
    }
}