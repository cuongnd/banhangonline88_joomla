package vantinviet.banhangonline88.interfaces;

import android.view.View;

import vantinviet.banhangonline88.entities.product.Product;

public interface RelatedProductsRecyclerInterface {

    void onRelatedProductSelected(View v, int position, Product product);
}
