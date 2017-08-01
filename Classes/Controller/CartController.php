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
     * @param array $item
     * @return void
     * @Flow\Session(autoStart = TRUE)
     */
    public function addItemAction($item) {
        $item['timestamp'] = time();
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
        } else {
            $sum = '0';
        }
        $this->view->assign('result', $sum);
    }

    /**
     * @return void
     */
    public function cartAction() {
        $cart = $this->items;
        $this->view->assign('items', $cart);
    }

    /**
     * @param string $id
     * @return void
     */
    public function removeitemAction($id) {
        $cart = $this->items;
        $key = array_search($id, array_column($cart, 'timestamp'));
        unset($this->items[$key]);
        sort($this->items);
        $this->redirectToUri('/');
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
