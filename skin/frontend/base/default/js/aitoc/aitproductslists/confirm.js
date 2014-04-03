
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: n/a
 * Generated:   2013-03-08 08:15:21
 * File path:   skin/frontend/base/default/js/aitoc/aitproductslists/confirm.js
 * Copyright:   (c) 2013 AITOC, Inc.
 */
function ait_confirm(url)
{
    var a = confirm("All discount will be removed. Are you sure you want to add product to your current product list?");
            if (a)
            {
                setLocation(url);
            }
            return false;
}