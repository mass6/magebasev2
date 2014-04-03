
/**
 * Product:     Recurring Purchase Assistant
 * Package:     Aitoc_Aitproductslists_1.0.4_1.0.0_520324
 * Purchase ID: n/a
 * Generated:   2013-03-08 08:15:21
 * File path:   skin/adminhtml/default/default/aitproductslists/js/sidebar.js
 * Copyright:   (c) 2013 AITOC, Inc.
 */
function loadListItems(list,url)
    {
        new Ajax.Updater(
            'order-sidebar_aitproductslists_sidebar_items',
            url,
            {
                method:'get',
                parameters: {list_id: list},
                onSuccess: function(transport)
                {
                    var response = transport.responseText || "no response text";
                },
                onFailure: function()
                { 
                    alert('Something went wrong...') 
                }
            }
        );
      
  }