define([
   'uiComponent',
   'Magento_Checkout/js/model/payment/renderer-list'
   ], function (Component, rendererList) { 
      'use strict';
      rendererList.push({ type: 'wire_transfer', component: 'Magneto_WireTransfer/js/view/payment/method-renderer/payment' });
      return Component.extend({});
  });