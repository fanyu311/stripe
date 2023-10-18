<?php

namespace App\Services;

use Stripe\Stripe;
use App\Entity\Order;
use App\Entity\Product;
use Stripe\PaymentIntent;


class StripeService
{



    // récupérer un table de produit 
    /**
     * @param Product $product
     * @return \Stripe\PaymentIntent
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function paymentIntent(Product $product)
    {

        Stripe::setApiKey('sk_test_51NxTN4AViwvcrKHv8IiwCgac8PtkISCwNL66jp5XT9ntKceAaKL7kmP9LbNMUdISIUtoLoGQmXRgrVVr9WAavop500l83toEO7');

        return PaymentIntent::create([
            'amount' => $product->getPrice() * 100,
            'currency' => Order::DEVISE,
            'payment_method_types' => ['card'],

        ]);
    }

    // on va prendre le prix / currency / description / un tableau avec parametrer avec stripe
    public function paiement(
        $amount,
        $currency,
        $description,
        array $stripeParameter
    ) {
        // il faut toujour mettre le clé prive 
        // déclencher le paiement (触发付款)
        Stripe::setApiKey('sk_test_51NxTN4AViwvcrKHv8IiwCgac8PtkISCwNL66jp5XT9ntKceAaKL7kmP9LbNMUdISIUtoLoGQmXRgrVVr9WAavop500l83toEO7');
        $payment_intent = null;

        // si je bien récupérer le parametre de $stripParameter on continu , sinon on arrete 
        if (isset($stripeParameter['stripeIntentId'])) {
            // lencer le payment
            //PaymentIntent::retrieve是Stripe PHP库中的一个方法，用于检索（Retrieve）已创建的支付意图（Payment Intent）。支付意图是Stripe用于处理付款的对象，它包含了有关付款的详细信息，如金额、货币、支付状态等。您可以使用PaymentIntent::retrieve来获取特定支付意图的详细信息。通常，您需要提供支付意图的唯一标识符，通常是支付意图的ID
            $payment_intent = PaymentIntent::retrieve($stripeParameter['stripeIntentId']);
        }
        //2ème vérification 
        if ($stripeParameter['stripeIntentStatus'] === 'succeeded') {
            //TODO listenerjs

        } else {
            $payment_intent->cancel();
        }

        return $payment_intent;
    }

    // cette fonction utiliser le function de paiement
    /**
     * @param array $stripeParameter
     * @param Product $product
     * @return \Stripe\PaymentIntent|null
     */
    public function stripe(array $stripeParameter, Product $product)
    {
        return $this->paiement(
            $product->getPrice() * 100,
            Order::DEVISE,
            $product->getName(),
            $stripeParameter
        );
    }
}
