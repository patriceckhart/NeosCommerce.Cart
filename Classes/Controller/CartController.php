<?php
namespace NeosCommerce\Cart\Controller;

/*
 * This file is part of the NeosCommerce.Cart package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;

/**
 * @Flow\Scope("singleton")
 */
class CartController extends ActionController
{

    /**
     * @Flow\Inject
     * @var \NeosCommerce\Cart\Domain\Model\Cart
     */
    protected $cart;

    /**
     * @Flow\Inject
     * @var \Neos\Flow\I18n\Translator
     */
    protected $translator;

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
     * @Flow\Inject
     * @var \NeosCommerce\Cart\Domain\Repository\CountryRepository
     */
    protected $countryRepository;

    /**
     * @Flow\Inject
     * @var \NeosCommerce\Cart\Domain\Repository\DeliveryRepository
     */
    protected $deliveryRepository;

    /**
     * @param array $item
     * @return void
     */
    public function addItemAction($item) {
        $this->cart->addItem($item);
        $nodeUri = $item['nodeUri'];
        //$this->addFlashMessage($this->translator->translateById('added', $sourceName = 'NodeTypes/Article', $packageKey = 'NeosCommerce.Cart'));
        $this->addFlashMessage("Artikel hinzugefügt.");
        $this->redirectToUri($nodeUri);
    }

    /**
     * @param array $item
     * @return void
     */
    public function updateItemAction($item) {
        $this->cart->updateItem($item);
        $linkToCart = $this->settings['linkToCart'];
        $this->redirectToUri($linkToCart);
    }

    /**
     * @return void
     */
    public function miniCartAction() {
        $sum = $this->cart->miniCart();
        $linkToCart = $this->settings['linkToCart'];
        $this->view->assign('result', $sum);
        $this->view->assign('linkToCart', $linkToCart);
    }

    /**
     * @return void
     */
    public function cartAction() {
        $items = $this->cart->cart();

        $taxinclusive = $this->settings['taxinclusive'];
        $this->view->assign('taxinclusive', $taxinclusive);

        $cartcount = count($items);
        if ($cartcount>0) {
            $sumExcl = FALSE;
            $sumExcl = floatval($sumExcl);
            foreach ($items as $dat) {
                $fullpriceExcl = floatval($dat["fullprice"]);
                $sumExcl += $fullpriceExcl;
            }
            $this->view->assign('subtotal', $sumExcl);
        } else {
            $sumExcl = 0;
        }

        $sumTax = FALSE;
        $sumTax = floatval($sumTax);
        foreach ($items as $datTax) {
            $tax = floatval($datTax["taxvalue"]);
            $sumTax += $tax;
        }

        $taxinclusive = $this->settings['taxinclusive'];

        if ($this->request->hasArgument('delivery')) {
            $delivery = $this->request->getArgument('delivery');
        } else {
            $delivery = $this->settings['standardDelivery'];
        }

        $this->view->assign('delivery', $delivery);

        $deliveries = '\NeosCommerce\Cart\Domain\Model\Delivery';
        $query = $this->persistenceManager->createQueryForType($deliveries);
        $result = $query->matching($query->equals('Persistence_Object_Identifier', $delivery))->execute()->getFirst();
        $deliverycosts = $result->getCosts();
        $this->view->assign('deliverycosts', $deliverycosts);

        $sumExcl = $sumExcl+$deliverycosts;


        if($taxinclusive==true) {
            $this->view->assign('fullprice', $sumExcl);
        } else {
            $this->view->assign('fullprice', 'non');
        }

        $this->view->assign('taxvalue', $sumTax);

        $this->view->assign('items', $items);

        $this->view->assign('countries', $this->countryRepository->findAll());
        $this->view->assign('deliveries', $this->deliveryRepository->findAll());

    }

    /**
     * @return void
     */
    public function deleteCartAction() {
        $this->cart->deleteCart();
        $linkToCart = $this->settings['linkToCart'];
        $this->redirectToUri($linkToCart);
    }

    /**
     * @param string $id
     * @return void
     */
    public function removeItemAction($id) {
        $this->cart->removeItem($id);
        $linkToCart = $this->settings['linkToCart'];
        $this->redirectToUri($linkToCart);
    }

    /**
     * @return void
     */
    public function checkoutAction() {

        $orderEmailAddress = $this->settings['orderEmailAddress'];
        $senderEmailAddress = $this->settings['senderEmailAddress'];
        $confirmationSubject = $this->settings['confirmationSubject'];
        $orderSubject = $this->settings['orderSubject'];

        /* Confirmation */
        $cart = $this->cart->cart();
        $cartcount = count($cart);
        $mailoutput = "";

        for ($i = 0; $i < $cartcount; $i++) {
            $innerarray = $cart[$i];
            $quantity = $innerarray['quantity'];
            $title = $innerarray['articleTitle'];
            $description = $innerarray['articleDescription'];
            $price = $innerarray['articlePrice'];
            $articlenumber = $innerarray['articleNumber'];
            $tax = $innerarray['tax'];
            $taxvalue = $innerarray['taxvalue'];

            //$priceraw = str_replace(',', '.', $price);
            //$subtotal = $priceraw - $taxvalue;

            $priceraw = $price;
            $subtotal = $priceraw - $taxvalue;


            $mailoutput .= '
            <tr>
		<td>'.$quantity.'</td>
      <td>'.$title.'</td>
      <td>EUR '.$price.'</td>
    </tr>
            ';

        }



        $email = "me@patric.at";

        $confirmationbody = 'Bestätigung: <br />'.$mailoutput;
        $orderbody = 'Bestellung: <br />'.$mailoutput;


        /*$confirmation = new \Neos\SwiftMailer\Message();
        $confirmation->setFrom(array($senderEmailAddress))
            ->setTo(array($email))
            ->setSubject($confirmationSubject)
            ->setBody($confirmationbody, 'text/html')
            ->send();

        $order = new \Neos\SwiftMailer\Message();
        $order->setFrom(array($senderEmailAddress))
            ->setTo(array($orderEmailAddress))
            ->setSubject($orderSubject)
            ->setBody($orderbody, 'text/html')
            ->send();*/

        $this->cart->deleteCart();
        $linkToCart = $this->settings['linkToCart'];
        $this->redirectToUri($linkToCart);

    }

}
