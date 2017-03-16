package vantinviet.banhangonline88.interfaces;

import android.view.View;

import vantinviet.banhangonline88.entities.product.Product;

public interface CategoryRecyclerInterface {

    void onProductSelected(View view, Product product);
    void onChattingSelected(View view, Product product);

}
