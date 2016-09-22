$jMaQma(document).ready(function () {
    $jMaQma("a[name^='faq-']").each(function () {
        $jMaQma(this).click(function () {
            $jMaQma("#" + this.name).slideToggle();
            return false;
        });
    });
});