<?php

namespace App\Menu;

class Menu
{
    public static function createMenu($oUser = null)
    {
        $element = 1;
        $list = 2;
        if (is_null($oUser)) {
            return "";
        }

        $type = \Auth::user()->type();

        if($type->id_typesuser == 1){
            $lMenus = [
                (object) ['type' => $element, 'route' => route('home'), 'icon' => 'bx bx-home bx-sm', 'name' => 'Inicio'],
                (object) ['type' => $element, 'route' => route('requisitions.index'), 'icon' => 'bx bx-file bx-sm', 'name' => 'Requisiciones']
            ];
        }else{
            $lPermissions = collect($oUser->permissionsByRol());
    
            $viewsAccess = $lPermissions->where('level', 'view');
    
            $lMenus = [
                (object) ['type' => $element, 'route' => route('home'), 'icon' => 'bx bx-home bx-sm', 'name' => 'Inicio']
            ];
            foreach($viewsAccess as $view){
                switch ($view->key_code) {
                    case 'autorizador.requisiciones':
                        $lMenus[] = (object) ['type' => $element, 'route' => route('requisitions.index'), 'icon' => 'bx bx-file bx-sm', 'name' => 'Requisiciones'];
                        break;
                    
                    default:
                        # code...
                        break;
                }
            }
        }

        $sMenu = "";
        foreach ($lMenus as $menu) {
            if ($menu == null) {
                continue;
            }
            if ($menu->type == $element) {
                $sMenu = $sMenu . Menu::createMenuElement($menu->route, $menu->icon, $menu->name);
            } else if ($menu->type == $list) {
                $sMenu = $sMenu . Menu::createListMenu($menu->id, $menu->list, $menu->name, $menu->icon);
            }
        }

        return $sMenu;
    }

    private static function createMenuElement($route, $icon, $name)
    {
        return '<li class="nav-item">
                    <a class="nav-link" href="' . $route . '">
                        <i class="' . $icon . ' menu-icon"></i>
                        <span class="menu-title">' . $name . '</span>
                    </a>
                </li>';
    }

    private static function createListMenu($id, $list, $name, $icon)
    {
        $str = '<li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#' . $id . '" aria-expanded="false" aria-controls="' . $id . '">
                        <i class="' . $icon . ' menu-icon"></i>
                            <span class="menu-title">' . $name . '</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="' . $id . '">
                        <ul class="nav flex-column sub-menu">';

        foreach ($list as $l) {
            if (!isset($l['size'])) {
                $str = $str . '<li class="nav-item"> <a class="nav-link" href="' . $l['route'] . '">' . $l['name'] . '</a></li>';
            } else {
                $str = $str . '<li class="nav-item"> <a class="nav-link" href="' . $l['route'] . '" style="font-size:' . $l['size'] . '">' . $l['name'] . '</a></li>';
            }
        }

        $str = $str . '</ul></div></li>';

        return $str;
    }
}