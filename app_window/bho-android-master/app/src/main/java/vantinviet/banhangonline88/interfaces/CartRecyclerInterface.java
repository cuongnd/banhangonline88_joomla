package vantinviet.banhangonline88.interfaces;

import vantinviet.banhangonline88.entities.cart.CartDiscountItem;
import vantinviet.banhangonline88.entities.cart.CartProductItem;

public interface CartRecyclerInterface {

    void onProductUpdate(CartProductItem cartProductItem);

    void onProductDelete(CartProductItem cartProductItem);

    void onDiscountDelete(CartDiscountItem cartDiscountItem);

    void onProductSelect(long productId);

}
