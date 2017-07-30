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
        if ($cartcount>0) {
            $sum = FALSE;
            $sum = intval($sum);
            foreach ($cart as $dat) {
                $quantity = intval($dat["quantity"]);
                $sum += $quantity;
            }
            $this->view->assign('itemscount', $sum);
        } else {
            $this->view->assign('itemscount', '0');
        }
    }

}
