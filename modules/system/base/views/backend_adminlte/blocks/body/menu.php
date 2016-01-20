<?php
/**
 * @author   Krzysztof Trzos
 * @package  base
 * @since    1.0.0-alpha
 * @version  1.0.0-alpha
 */
?>

<?php /* @var $menus array */ ?>
<?php /* @var $subMenus array */ ?>

<?php
$routes = [];

//$routes[] = [
//    'title'   => 'MAIN MENU',
//    'classes' => ['header'],
//];
//
//foreach($menus as $menu) {
//    $mainSection = [
//        'title'        => __('backend.mainmenu.'.$menu),
//        'url'          => '#',
//        'classes'      => ['treeview'],
//        'children'     => [],
//        'inner_prefix' => '<i class="fa fa-circle-o"></i>',
//    ];
//
//    if(isset($subMenus[$menu])) {
//        foreach($subMenus[$menu] as $module => $optionsGroup) {
//            $firstOption = reset($optionsGroup);
//
//            $submenu = [
//                'title'        => __('module.'.$module),
//                'url'          => $firstOption['url'],
//                'children'     => [],
//                'inner_prefix' => '<i class="fa fa-angle-left pull-right"></i>',
//            ];
//
//            if(count($optionsGroup) > 1) {
//                foreach($optionsGroup as $name => $option) {
//                    $submenu['children'][] = [
//                        'title'        => __('backend.submenu.'.$menu.'.'.$name),
//                        'url'          => $option['url'],
//                        'inner_prefix' => '<i class="fa fa-caret-right"></i>',
//                    ];
//                }
//            }
//
//            $mainSection['children'][] = $submenu;
//        }
//    }
//
//    $routes[] = $mainSection;
//}

foreach($menus as $menu) {
    $mainSection = [
        'title'    => __('backend.mainmenu.'.$menu),
        'children' => [],
        'classes'  => ['header'],
    ];

    $routes[] = $mainSection;

    if(isset($subMenus[$menu])) {
        foreach($subMenus[$menu] as $module => $optionsGroup) {
            $firstOption = reset($optionsGroup);

            $submenu = [
                'title'        => __('module.'.$module),
                'url'          => $firstOption['url'],
                'classes'      => ['treeview'],
                'children'     => [],
                'inner_prefix' => '<i class="fa fa-circle-o"></i>',
            ];

            if(count($optionsGroup) > 1) {
                foreach($optionsGroup as $name => $option) {
                    $submenu['children'][] = [
                        'title'        => __('backend.submenu.'.$menu.'.'.$name),
                        'url'          => $option['url'],
                        'inner_prefix' => '<i class="fa fa-caret-right"></i>',
                    ];
                }
            }

            $routes[] = $submenu;
        }
    }
}

$menuObject = \ModuleMenu\Menu::factory($routes);
$menuObject->setActiveTrailClass('active');
$menuObject->setSubmenuClass('treeview-menu');
$menuObject->getAttributes()
    ->addToAttribute('class', 'sidebar-menu');

echo $menuObject
    ->generate('main-menu-backend')
    ->render();
?>