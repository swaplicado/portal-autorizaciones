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

        if ($type->id_typesuser == 1) {
            $lMenus = [
                (object) ['type' => $element, 'route' => route('home'), 'icon' => 'bx bx-home bx-sm', 'name' => 'Inicio'],
                // (object) ['type' => $element, 'route' => route('requisitions.index'), 'icon' => 'bx bx-file bx-sm', 'name' => 'Autoriza requisiciones'],
                (object) ['type' => $element, 'route' => route('dps.pending'), 'icon' => 'bx bx-file bx-sm', 'name' => 'OC por autorizar'],
                (object) ['type' => $element, 'route' => route('dps.index'), 'icon' => 'bx bx-file bx-sm', 'name' => 'Todas las OC'],
                (object) ['type' => $element, 'route' => route('rm.pending'), 'icon' => 'bx bx-file bx-sm', 'name' => 'Reqs. por autorizar'],
                (object) ['type' => $element, 'route' => route('rm.index'), 'icon' => 'bx bx-file bx-sm', 'name' => 'Todas las reqs.'],
                (object) ['type' => $element, 'route' => route('rm.myrm'), 'icon' => 'bx bx-file bx-sm', 'name' => 'Mis reqs.'],
                (object) ['type' => $element, 'route' => route('index'), 'icon' => 'bx bx-file bx-sm', 'name' => 'Manuales'],
            ];
        } else {
            $lPermissions = collect($oUser->permissionsByRol());
            $lDirectPermissions = $oUser->permissions();
            $lPermissions = $lPermissions->merge($lDirectPermissions);
            // quitar repetidos
            $lPermissions = $lPermissions->unique('key_code');
            $viewsAccess = $lPermissions->where('level', 'view');

            $lMenus = [
                (object) ['type' => $element, 'route' => route('home'), 'icon' => 'bx bx-home bx-sm', 'name' => 'Inicio']
            ];
            foreach ($viewsAccess as $view) {
                switch ($view->key_code) {
                    // case 'autorizador.requisiciones':
                    //     $lMenus[] = (object) ['type' => $element, 'route' => route('requisitions.index'), 'icon' => 'bx bx-file bx-sm', 'name' => 'Autoriza requisiciones'];
                    //     break;
                    case 'autorizador.dps':
                        $lMenus[] = (object) ['type' => $element, 'route' => route('dps.pending'), 'icon' => 'bx bx-file bx-sm', 'name' => 'OC por autorizar'];
                        $lMenus[] = (object) ['type' => $element, 'route' => route('dps.index'), 'icon' => 'bx bx-file bx-sm', 'name' => 'Todas las OC'];
                        break;
                    case 'autorizador.rm':
                        $lMenus[] = (object) ['type' => $element, 'route' => route('rm.pending'), 'icon' => 'bx bx-file bx-sm', 'name' => 'Requisiciones por autorizar'];
                        $lMenus[] = (object) ['type' => $element, 'route' => route('rm.index'), 'icon' => 'bx bx-file bx-sm', 'name' => 'Todas las requisiciones'];
                        break;
                    case 'user.own_rm':
                        $lMenus[] = (object) ['type' => $element, 'route' => route('rm.myrm'), 'icon' => 'bx bx-file bx-sm', 'name' => 'Mis requisiciones'];
                        
                    default:
                        # code...
                        break;
                }
            }
            $lMenus[] = (object) ['type' => $element, 'route' => route('index'), 'icon' => 'bx bx-file bx-sm', 'name' => 'Manuales'];
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