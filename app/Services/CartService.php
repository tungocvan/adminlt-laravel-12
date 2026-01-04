<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\WpProduct;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Get cart identifier (user_id or session_id)
     */
    protected function getCartIdentifier(): array
    {
        if (auth()->check()) {
            return ['user_id' => auth()->id()];
        }

        $sessionId = Session::getId();
        if (!$sessionId) {
            Session::start();
            $sessionId = Session::getId();
        }

        return ['session_id' => $sessionId];
    }

    /**
     * Add product to cart
     */
    public function addToCart(int $productId, int $quantity = 1): bool
    {
        $product = WpProduct::find($productId);

        if (!$product) {
            return false;
        }

        $identifier = $this->getCartIdentifier();
        $price = $product->final_price ?? 0;

        $cartItem = Cart::where($identifier)
            ->where('wp_product_id', $productId)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->price = $price; // Update price in case it changed
            $cartItem->save();
        } else {
            Cart::create(array_merge($identifier, [
                'wp_product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
            ]));
        }

        return true;
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(int $cartItemId, int $quantity): bool
    {
        $identifier = $this->getCartIdentifier();

        $cartItem = Cart::where($identifier)
            ->where('id', $cartItemId)
            ->first();

        if (!$cartItem) {
            return false;
        }

        if ($quantity <= 0) {
            $cartItem->delete();
        } else {
            $cartItem->quantity = $quantity;
            $cartItem->save();
        }

        return true;
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(int $cartItemId): bool
    {
        $identifier = $this->getCartIdentifier();

        return Cart::where($identifier)
            ->where('id', $cartItemId)
            ->delete() > 0;
    }

    /**
     * Get all cart items
     */
    public function getCartItems()
    {
        $identifier = $this->getCartIdentifier();

        return Cart::where($identifier)
            ->with('product')
            ->get();
    }

    /**
     * Get cart total
     */
    public function getCartTotal(): float
    {
        $items = $this->getCartItems();
        return $items->sum('subtotal');
    }

    /**
     * Get cart item count
     */
    public function getCartCount(): int
    {
        $identifier = $this->getCartIdentifier();

        return Cart::where($identifier)->sum('quantity');
    }

    /**
     * Clear cart
     */
    public function clearCart(): bool
    {
        $identifier = $this->getCartIdentifier();

        return Cart::where($identifier)->delete() > 0;
    }

    /**
     * Merge guest cart to user cart on login
     */
    public function mergeGuestCart(string $sessionId, int $userId): void
    {
        $guestCarts = Cart::where('session_id', $sessionId)->get();

        foreach ($guestCarts as $guestCart) {
            $userCart = Cart::where('user_id', $userId)
                ->where('wp_product_id', $guestCart->wp_product_id)
                ->first();

            if ($userCart) {
                $userCart->quantity += $guestCart->quantity;
                $userCart->save();
                $guestCart->delete();
            } else {
                $guestCart->update([
                    'user_id' => $userId,
                    'session_id' => null,
                ]);
            }
        }
    }
}
