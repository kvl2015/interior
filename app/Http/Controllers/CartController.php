<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;

use App\Http\Resources\Page as PageResource;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactRequest;
use Illuminate\Support\Facades\Cache;
use MetaTag;
use \Concardis\Payengine\Lib\Payengine;
use \Concardis\Payengine\Lib\Internal\Config\MerchantConfiguration;
use \Concardis\Payengine\Lib\Models\Request\Customer as CustomerRequest;
use \Concardis\Payengine\Lib\Models\Request\Customers\Persona as PersonaRequest;
use \Concardis\Payengine\Lib\Models\Request\Customers\Address as AddressRequest;
use \Concardis\Payengine\Lib\Models\Request\Orders\Payment\Payment;


class CartController extends Controller
{
    public function view(Request $request) {
        $cart = $request->session()->get('cart');

        $amounts = array();
        foreach ($cart as $item) {
            @$amounts['summary'] += $item['price']*$item['qty']; 
            @$amounts['subtotal'] += $item['price']*$item['qty'];   
        }
        $amounts['shipping'] = 0;
        $amounts['discount'] = 0;
        $amounts['grand'] = $amounts['summary'] + $amounts['shipping'] - $amounts['discount'];

        //print_r($cart);exit;
        return view('cart.view', compact('cart', 'amounts'));
    }

    public function updateCart(Request $request) {
        $items = json_decode($request->get('items'));
        $arrCart = array();
        $amounts = array();
        foreach ($items as $key => $item) {
            $product = \App\Product:: where('id', $item->product_id)->first();
            if ($product) {
                $price = 0;
                $arrCart[$key]['product'] = $product;
                if ($product->price > 0) {
                    //$item->price = $product->price;
                    $price = $product->price;
                } 
                $prices = array();
                if ($product->options) {
                    $productOptions = json_decode($product->options);
                    foreach ($productOptions as $option) {
                        foreach ($option->selected as $t => $value) {
                            $prices[$value] = $option->price[$t];
                        }
                    }
                }
                
                if (count((array)$item->options)) {
                    foreach ($item->options as $_option) {
                        $option = \App\Option::where('id', $_option->id)->first();
                        if ($option) {
                            $arrCart[$key]['options'][] = $option; 
                            $price = $prices[$option->id] > 0 ? $prices[$option->id] : $price;
                        }
                    }
                }
                $arrCart[$key]['price'] = $price;
                $arrCart[$key]['qty'] = $item->product_quantity;  
                $arrCart[$key]['unique_key'] = $item->unique_key;
                @$amounts['summary'] += $price*$item->product_quantity; 
                @$amounts['subtotal'] += $price*$item->product_quantity;   
            }
        }
        $amounts['shipping'] = 0;
        $amounts['discount'] = 0;
        $amounts['grand'] = $amounts['summary'] + $amounts['shipping'];
        $request->session()->put('cart', $arrCart);
        if ($request->get('type') == 'checkout') {
            $cart = $arrCart;
            $returnHTML = view('cart.items', compact('cart'))->render();
            return response()->json(array(
                'success' => true,
                'html' => $returnHTML,
                'amounts' => $amounts
            ));
        } else {
            return response()->json(array(
                'success' => true,
            )); 
        }
    }

    public function getCupone(Request $request) {
        $cart = $request->session()->get('cart');

        $cupone = \App\Cupon::where('code', $request->get('code'))->first();
        if ($cupone) {
            $amounts = array();
            foreach ($cart as $item) {
                @$amounts['summary'] += $item['price']*$item['qty']; 
                @$amounts['subtotal'] += $item['price']*$item['qty'];   
            }
            $amounts['shipping'] = 0;
            $amounts['discount'] = $cupone->type == 'persent' ? ceil($amounts['summary']*$cupone->value/100) : $cupone->value;
            $amounts['grand'] = $amounts['summary'] + $amounts['shipping'] - $amounts['discount'];

            return response()->json(array(
                'success' => true,
                'amounts' => $amounts,
            ));
        } else {
            return response()->json(array(
                'success' => false,
            )); 
        }
    }

    public function success(Request $request) {
        dd('success');
    }
    public function cancel(Request $request) {
        dd('cancel');
    }
    public function failure(Request $request) {
        dd('failure');
    }

    public function checkout(Request $request) {
        $config = new MerchantConfiguration();
        //TODO: Enter your API-Key
        $config->setApiKey('6Vvuqo2Dao0kxgm1');
        // TODO: Enter your merchantId
        $config->setMerchantId('merchant_gmlhqu26r4');
        $config->setIsLiveMode(false);

        $lib = new Payengine($config);

        /*$customerRequest = new CustomerRequest();
        $customerRequest->setEmail('somebody@' . time() .'.org');

        $customerResponse = $lib->customer()->post($customerRequest);
        

        $personaRequest = new PersonaRequest();
        $personaRequest->setTitle("Dr.");
        $personaRequest->setGender(\Concardis\Payengine\Lib\Internal\Constants\Gender::MALE);
        $personaRequest->setFirstName("Max");
        $personaRequest->setLastName("Mustermann");
        $personaRequest->setBirthday(time());
        $personaRequest->setFax("0123456789");
        $personaRequest->setMobile("0123456789");
        $personaRequest->setPhone("0123456789");
        $personaResponse = $lib->customer($customerResponse->getCustomerId())->personas()->post($personaRequest);


        $addressRequest = new AddressRequest();
        $addressRequest->setFirstName("Vika");
        $addressRequest->setLastName("Mustermann");
        $addressRequest->setZip("12345");
        $addressRequest->setStreet("Severnoe shosse");
        $addressRequest->setHouseNumber("5a");
        $addressRequest->setCountry("DE");
        $addressRequest->setCity("Musterstadt");
        $addressRequest->setPhone("0123456789");
        $addressRequest->setMobile("0123456789");
        $addressRequest->setFax("0123456789");
        $addressRequest->setState("nrw");
        $addressRequest->setTitle("dr.");
        
        $addressResponse = $lib->customer($customerResponse->getCustomerId())->addresses()->post($addressRequest);
        */
        
        $customerId = 'customer_egj0qjvt6r';
        $personaId = 'persona_tmolebortw';
        $addressId = 'address_vipirusztb';
        

        $payment = new Payment();
        $payment->setPaymentInstrumentId('1'); //TODO Enter PaymentInstrumentId
        $payment->setCardNumber('4012001038443335');
        $payment->setExpiryMonth('12');
        $payment->setExpiryYear('21');
        $payment->setVerification('123');
        $payment->setCardHolder('Test card owner');
        //$paymentResponse = $lib->payment($customerResponse->getCustomerId())->addresses()->post($addressRequest);
        // $payment->set('DE');

        $item = new \Concardis\Payengine\Lib\Models\Request\Orders\Item();
        $item->setQuantity(2);
        $item->setUnitPrice(5);
        $item->setUnitPriceWithTax(6);
        $item->setTotalPrice(10);
        $item->setTotalPriceWithTax(12);
        $item->setArticleNumber("test");
        $item->setName("testName");
        $item->setTax(19);
        
        $async = new \Concardis\Payengine\Lib\Models\Request\Orders\Async();
        $async->setSuccessUrl("https://selectinterior.world/checkout/success");
        $async->setCancelUrl("https://selectinterior.world/checkout/cancel");
        $async->setFailureUrl("https://selectinterior.world/checkout/failure");
        
        $authorizingTransaction = new \Concardis\Payengine\Lib\Models\Request\Orders\AuthorizingTransaction();
        $authorizingTransaction->setCustomer($customerId);
        $authorizingTransaction->setPersona($personaId);
        $authorizingTransaction->setBillingAddress($addressId);
        $authorizingTransaction->setShippingAddress($addressId);
        $authorizingTransaction->setCurrency("EUR");
        $authorizingTransaction->setPayment($payment);
        $authorizingTransaction->setBasket(array(
            $item
        ));
        $authorizingTransaction->setInitialAmount(12);
        $authorizingTransaction->setChannel('ECOM');
        $authorizingTransaction->setSource("basicUsage script");
        $authorizingTransaction->setTerms(time());
        $authorizingTransaction->setPrivacy(time());
        $authorizingTransaction->setAsync($async);
        $authorizingTransaction->setProduct(\Concardis\Payengine\Lib\Internal\Constants\Products::CREDITCARD);
        
        $transactionResponse = $lib->orders()->preauth()->post($authorizingTransaction);        
dd($transactionResponse);
    }
}