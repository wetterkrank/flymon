(function ($) {
  "use strict";

  // Makes a price request for each .flymon-tag element in the page
  $(document).ready(function () {
    $(".flymon-tag").each(function () {
      const element = $(this);
      const data = element.data();
      const query = { action: "price" };
      for (const [key, value] of Object.entries(data)) {
        query[key] = value;
      }
      $.post({
        url: WP_FLYMON.ajaxUrl, // The WP_FLYMON const is set when enqueueing the scripts
        data: query,
        success: function (response) {
          flymonWidget(element, response);
        },
        error: function (response) {
          flymonError(element, response.responseJSON);
        },
      });
    });

    // TODO: use Intl.NumberFormat to format the price?
    function flymonWidget(element, response) {
      const resJSON = JSON.parse(response);
      if (resJSON.success) {
        const params = element.data();
        const data = resJSON.data;
        const outboundDate = data.outboundDate.slice(0, 5).replaceAll("-", ".");
        const inboundDate = data.inboundDate?.slice(0, 5)?.replaceAll("-", ".");
        const tripDates = inboundDate
          ? `🚀 ${outboundDate} - ${inboundDate} 🏁`
          : `🚀 ${outboundDate}`;
        const deeplink =
          params.deeplink_type === "booking"
            ? data.deeplink
            : buildSearchDeeplink(params);
        element.html(
          `<a href="${deeplink}" target="_blank" rel="nofollow">${data.currency} ${data.price}</a><span class="flymon-tag__tooltip">${tripDates}</span>`
        );
      } else {
        flymonError(element, resJSON);
      }
    }

    function flymonError(element, resJSON) {
      const deeplink = buildSearchDeeplink(element.data());
      element.html(
        `<a href="${deeplink}" target="_blank" rel="nofollow">...</a><span class="flymon-tag__tooltip">🤔 No result</span>`
      );
    }

    function buildSearchDeeplink(data) {
      const deeplinkHost = "https://www.kiwi.com/deep";
      const returnParam =
        data.nights_in_dst_from && data.nights_in_dst_to
          ? { return: `${data.nights_in_dst_from}_${data.nights_in_dst_to}` }
          : {};
      const params = {
        from: data.fly_from,
        to: data.fly_to,
        departure: `${data.date_from}_${data.date_to}`.replaceAll("/", "-"),
        ...returnParam,
        lang: data.locale,
        currency: data.curr,
        transport: data.vehicle_type,
        stopNumber: data.max_stopovers,
        affilid: data.affilid,
      };
      Object.keys(params).forEach((key) => params[key] ?? delete params[key]);
      const deeplinkParams = new URLSearchParams(params);
      return `${deeplinkHost}?${deeplinkParams}`;
    }
  }); // $(document).ready()
})(jQuery);
