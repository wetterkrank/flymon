(function( $ ) {
  'use strict';
  
  // Makes a request to the price API for each .flymon-tag element in the page
  $(document).ready(function() {
    
    // TODO: widget sizes, localization; error handling
    function flymonWidget(element, response) {
      const resJSON = JSON.parse(response);
      const success = resJSON.success;
      if (success) {
        const data = resJSON.data;
        const deeplink = buildSearchDeeplink(element.data());
        element.html(`<a href="${deeplink}" target="_blank" rel="nofollow">${data.currency} ${data.price}</a>`);
      } else {
        flymonError(element, resJSON);
      }
    };
    
    function flymonError(element, resJSON) {
      const deeplink = buildSearchDeeplink(element.data());
      element.html(`<a href="${deeplink}" target="_blank" rel="nofollow">...</a>`);
    };

    function buildSearchDeeplink(data) {
      const deeplinkHost = 'https://www.kiwi.com/deep';
      const params = {
        'from': data.fly_from,
        'to': data.fly_to,
        'departure': `${data.date_from}_${data.date_to}`.replaceAll('/', '-'),
        'return': `${data.nights_in_dst_from}_${data.nights_in_dst_to}`,
        'lang': data.locale,
        'currency': data.curr,
        'transport': data.vehicle_type,
        'stopNumber': data.max_stopovers,
        'affilid': data.affilid,
      };
      Object.keys(params).forEach(key => !params[key] && delete params[key]);
      const deeplinkParams = new URLSearchParams(params);
      return `${deeplinkHost}?${deeplinkParams}`;
    }

    $(".flymon-tag").each(function() {
      const element = $(this);
      const data = element.data();
      const query = {'action': 'price'};
      for (const [key, value] of Object.entries(data)) {
        query[key] = value;
      }
      $.post({
        url: FLYMON.ajaxUrl, 
        data: query, 
        success: function(response) { flymonWidget(element, response) },
        error: function(response) { flymonError(element, response.responseJSON) },
      });
      
    });
  });
})( jQuery );
