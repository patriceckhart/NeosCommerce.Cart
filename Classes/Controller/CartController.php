<?php
namespace NeosCommerce\Cart\Controller;

/*
 * This file is part of the NeosCommerce.Cart package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
/**
 * @Flow\Scope("session")
 */
class CartController extends ActionController
{

    /**
     * @var array
     */
    protected $settings;

    /**
     * @param array $settings
     * @return void
     */
    public function injectSettings(array $settings) {
        $this->settings = $settings;
    }

    /**
     * @var array
     */
    protected $items = array();

    /**
     * @param array $item
     * @return void
     * @Flow\Session(autoStart = TRUE)
     */
    public function addItemAction($item) {
        $this->items[] = $item;
        $this->redirectToUri('/');
    }

    /**
     * @return void
     */
    public function miniCartAction() {
        $cart = $this->items;
        $cartcount = count($cart);
        $wrap1 = '<div class="cart-minicart">';
        $wrap2 = '</div>';

        if ($cartcount>0) {
            $sum = FALSE;
            $sum = intval($sum);
            foreach ($cart as $dat) {
                $quantity = intval($dat["quantity"]);
                $sum += $quantity;
            }
            //return $this->view->render("../Cart/MiniCart").$wrap1.'Zum Warenkorb ('.$sum.' Artikel). <a href="/cart/deleteCart">Warenkorb löschen</a>'.$wrap2$sum = '0';
            $sum = $sum;
        } else {
            //return $this->view->render("../Cart/MiniCart").$wrap1.'Ihr Warenkorb ist leer.'.$wrap2;
            $sum = '0';
        }
        $this->view->assign('result', $sum);
        //return $this->view->render("../Cart/MiniCart");
    }

    /**
     * @return void
     */
    public function deleteCartAction() {
        $cart = $this->items;
        $cartcount = count($cart);
        for ($i = 0; $i < $cartcount; $i++) {
            unset($this->items[$i]);
        }
        $this->redirectToUri('/');
    }

}
