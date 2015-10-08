<?php

namespace Lancms\Kiosk;

use Lancms\Kiosk\Api\KioskSession;
use Lancms\Kiosk\Api\Product;
use Symfony\Component\HttpFoundation\Request;

class KioskGui
{

    /**
     * @var KioskSession
     */
    protected $_kioskSession;

    public function prepare($kioskSession = null)
    {
       $this->_kioskSession = (is_null($kioskSession) ? LanKioskSession::create(null, null) : $kioskSession);
    }

    public function front()
    {
        global $design_head, $content;

        $design_head .= '<script type="text/javascript" src="inc/AJAX/ajax_suggest.js"></script>'."\n";

        $html = new \HtmlElement("div");
        $html->addCssClass("kiosk");

        // Page title
        $html->addElement("div")->addCssClass("page-title")->addElement("h1", "Kiosk");

        // Print the input field for adding new wares.
        $newProductWrapper = $html->addElement("div")->addCssClass("new-product");
        $newProductForm = $newProductWrapper->addElement("form");
        $newProductForm->setAttribute("action", "?module=kiosk")->setAttribute("method", "post");
        $newProductForm->addElement("input")
            ->setAttribute("type", "text")
            ->setAttribute("name", "product_name")
            ->setAttribute("id", "ware")
            ->setAttribute("placeholder", "Produktnavn...")
            ->setAttribute("tabindex", 1)
            ->setAttribute("autocomplete", "off")
            ->setAttribute("onkeyup", "suggest();");

        // Suggest div for autocomplete.
        $newProductWrapper->addElement("div")->setAttribute("id", "suggest");

        // Productlist
        if ($this->_kioskSession->hasProducts()) {
            $productListWrapper = $html->addElement("div")->addCssClass("products");
            $productListWrapper->addElement("h2", "Handlekurv");

            // Table
            $productListTable = $productListWrapper->addElement("div")->addCssClass("table");
            foreach ($this->_kioskSession->getProducts() as $productArrayItem) {
                /** @var Product $product */
                $product = $productArrayItem["object"];
                $amount = $productArrayItem["amount"];

                $row = $productListTable->addElement("div")->addCssClass("row");
                $row->addElement("div", $product->getName())->addCssClass("cell");
                $row->addElement("div", $amount . " stk")->addCssClass("cell");
                $row->addElement("div", $product->getPrice() . " kr")->addCssClass("cell");

                $options = $row->addElement("div")->addCssClass("cell");
                $options->addElement("a")->setAttribute("href", "?module=kiosk&action=addWare&ware=" . $product->getProductID())->addElement("img")->setAttribute("src", "inc/images/plus-15px.png");
                $options->addElement("a")->setAttribute("href", "?module=kiosk&action=removeWare&ware=" . $product->getProductID())->addElement("img")->setAttribute("src", "inc/images/minus-15px.png");
            }
        }

        $html->addElement("a", "Reset")->setAttribute("href", "?module=kiosk&action=endSession");

        $content .= $html;
    }

    public function addWare()
    {
        global $kiosk, $kioskSession;

        $request = Request::createFromGlobals();

        if (!$request->query->has("ware")) {
            header("Location: ?module=kiosk&error=productnotfound1");
            return;
        }

        $ware = $request->query->getInt("ware");
        $wareID = -1;

        $qFindBarcode = db_query("SELECT * FROM ".db_prefix()."_kiosk_barcodes WHERE barcode = '".db_escape($ware)."'");
        if(db_num($qFindBarcode) > 0) {
            $rFindBarcode = db_fetch($qFindBarcode);
            $wareID = $rFindBarcode->wareID;
        } else {
            $wareID = $ware;
        }

        $product = $kiosk->getProductByID($wareID);
        if (!$product instanceof Product) {
            header("Location: ?module=kiosk&error=productnotfound2");
            return;
        }

        $kioskSession->addProduct($product);
        $kioskSession->save();

        header("Location: ?module=kiosk");
    }

    public function removeWare()
    {
        global $kiosk, $kioskSession;

        $request = Request::createFromGlobals();

        if (!$request->query->has("ware")) {
            header("Location: ?module=kiosk&error=productnotfound1");
            return;
        }

        $ware = $request->query->getInt("ware");
        $wareID = -1;

        $qFindBarcode = db_query("SELECT * FROM ".db_prefix()."_kiosk_barcodes WHERE barcode = '".db_escape($ware)."'");
        if(db_num($qFindBarcode) > 0) {
            $rFindBarcode = db_fetch($qFindBarcode);
            $wareID = $rFindBarcode->wareID;
        } else {
            $wareID = $ware;
        }

        $product = $kiosk->getProductByID($wareID);
        if (!$product instanceof Product) {
            header("Location: ?module=kiosk&error=productnotfound2");
            return;
        }

        $kioskSession->removeProduct($product);
        $kioskSession->save();

        header("Location: ?module=kiosk");
    }

    public function endSession()
    {
        global $kiosk, $kioskSession;

        $kioskSession->end();
        header("Location: ?module=kiosk");
    }
    
}
