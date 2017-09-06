<?php
foreach ($this->rows as &$product) {
    $this->row = $product;
    ob_start();
    echo $this->loadTemplate('price');
    $html = ob_get_clean();
    $product->html_price=$html;
}
?>
