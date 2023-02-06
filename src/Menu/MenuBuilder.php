<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuBuilder
{
    public function __construct(
        private FactoryInterface $factory,
        RequestStack $requestStack
    ) {
        $this->requestStack = $requestStack;
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Vernehmlassungen', [
            'route' => 'app_consultation',
        ]);

        $menu->addChild('Organisationen', [
            'route' => 'app_organisation_index',
        ]);

        return $menu;
    }
}
