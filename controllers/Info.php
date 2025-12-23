<?php

class Info extends Controller {
    
    /**
     * Trang khuyến mãi & tin tức
     */
    public function promotions() {
        $this->view("homePage", ["page" => "PromotionsView"]);
    }
}
