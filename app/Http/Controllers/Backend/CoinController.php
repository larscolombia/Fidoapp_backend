<?php

namespace App\Http\Controllers\Backend;

use Stripe\Price;
use Stripe\Stripe;
use Stripe\Product;
use App\Models\Coin;
use App\Models\Setting;
use App\Models\CoinPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CoinController extends Controller
{
    public function index()
    {
        // Buscar el registro existente por ID
        $coin = Coin::with('coinPrice')->first();
        return response()->json($coin, 200);
    }


    public function store(Request $request)
    {
        // Validar los datos entrantes
        $data = $request->validate([
            'symbol' => 'required|string|max:10',
            'minimum_recharge' => 'required|numeric|min:1',
            'conversion_rate' => 'required|numeric',
        ]);

        try {
            // Buscar si ya existe una moneda con el mismo símbolo
            $currency = Coin::where('id', $request->id)
                ->first();

            if ($currency) {
                // Actualizar la moneda existente
                $currency->symbol = $request->symbol;
                $currency->minimum_recharge = $request->minimum_recharge;
                $currency->conversion_rate = $request->conversion_rate;
                $currency->save();
            } else {
                // Crear una nueva moneda
                $currency = Coin::create([
                    'symbol' => $request->symbol,
                    'minimum_recharge' => $request->minimum_recharge,
                    'conversion_rate' => $request->conversion_rate,
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $currency,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    //metodos para stripe
    public function linkToStripe()
    {
        try {
            $this->setStripeApiKey();
            $coin = $this->getFirstCoin();

            if (!$coin) {
                return response()->json(['error' => 'No se encontró ninguna moneda.'], 404);
            }

            // Verifica si el producto ya existe
            $productId = $this->getProductId($coin); // Método para obtener el ID del producto
            if ($productId) {
                // Si existe, actualiza el producto
                $product = $this->updateProduct($productId);
            } else {
                // Si no existe, crea un nuevo producto
                $product = $this->createProduct();
            }

            // Verifica si el precio ya existe
            $priceId = $this->getPriceId($coin->id); // Método para obtener el ID del precio
            if ($priceId) {
                // Si existe, lo desactivamos
                $this->deactivatePrice($priceId);
            }
            $price = $this->createPrice($product->id, $coin->conversion_rate);
            // Guarda los datos en la base de datos local
            $this->saveCoinPrice($coin->id, $product->id, $price->id);
            $data = [$product, $price];
            return response()->json(['success' => true, 'data' => $data], 201);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::info($e);
            return response()->json(['error' => 'Error en Stripe: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::info($e);
            return response()->json(['error' => 'Error al crear o actualizar el producto o precio en Stripe: ' . $e->getMessage()], 500);
        }
    }

    private function setStripeApiKey()
    {
        $setting = Setting::where('name', 'stripe_secretkey')->first();
        $stripeSecret = $setting->val ?? env('STRIPE_SECRET');
        Stripe::setApiKey($stripeSecret);
    }

    private function getFirstCoin()
    {
        return Coin::first();
    }

    private function getProductId($coin)
    {
        $coinPrice = CoinPrice::where('coin_id', $coin->id)->first();
        if ($coinPrice) {
            return $coinPrice->stripe_product_id;
        }
        return null;
    }

    private function createProduct()
    {
        return Product::create([
            'name' => 'FidoCoins',
            'description' => 'Moneda FidoCoins',
        ]);
    }

    private function updateProduct($productId)
    {
        return Product::update($productId, [
            'name' => 'FidoCoins',
            'description' => 'Moneda FidoCoins',
        ]);
    }

    private function getPriceId($coinId)
    {
        $coinPrice = CoinPrice::where('coin_id', $coinId)->first();
        if ($coinPrice) {
            return $coinPrice->stripe_product_id;
        }
        return null;
    }

    private function createPrice($productId, $conversionRate)
    {
        return Price::create([
            'unit_amount' => $conversionRate * 100,
            'currency' => 'usd',
            'product' => $productId,
        ]);
    }

    private function deactivatePrice($priceId)
    {
        try {
            // Intentar recuperar el precio
            $price = \Stripe\Price::retrieve($priceId);

            // Si se recupera correctamente, desactivar el precio
            return \Stripe\Price::update($priceId, [
                'active' => false,
            ]);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Manejar el caso donde el precio no existe
            if ($e->getHttpStatus() === 404) {
                // El precio no existe, no hacemos nada
                return null; // O puedes lanzar un mensaje o loguear la información
            }

            // Manejar otros errores
            throw $e;
        }
    }


    private function saveCoinPrice($coinId, $productId, $priceId)
    {
        CoinPrice::updateOrInsert(
            ['coin_id' => $coinId],
            [
                'stripe_product_id' => $productId,
                'stripe_price_id' => $priceId,
            ]
        );
    }
}
