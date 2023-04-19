<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class MenuBuilder
{
    public function __construct(
        private FactoryInterface $factory,
    ) {
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Vernehmlassungen', [
            'route' => 'app_consultation',
        ]);

        $menu->addChild('Stellungnahmen', [
            'route' => 'app_statement_index',
        ]);

        $menu->addChild('Organisationen', [
            'route' => 'app_organisation_index',
        ]);

        return $menu;
    }
}
