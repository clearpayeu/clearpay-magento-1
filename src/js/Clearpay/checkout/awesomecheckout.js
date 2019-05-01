(function() {
    if (typeof window.Review !== "undefined") {
        /**
         * Override save on order review function
         */
        var reviewSave = window.Review.prototype.save;
        window.Review.prototype.save = function() {
            // check payment method
            if (payment.currentMethod == 'clearpaypayovertime') {
                if (checkout.loadWaiting != false) return;
                checkout.setLoadWaiting('review');
                /**
                 * Override on complete to do clearpay payment
                 *
                 * @param transport
                 */
                // Run ajax to process Clearpay payment using Protorype
                var request = new Ajax.Request(
                    window.Clearpay.saveUrl, // use Clearpay controller
                    {
                        method: 'post',
                        onSuccess: function (transport) {
                            var response = {};

                            // Parse the response - lifted from original method
                            try {
                                response = eval('(' + transport.responseText + ')');
                            }
                            catch (e) {
                                response = {};
                            }

                            // if the order has been successfully placed
                            if (response.success) {

                                //modified to suit API V1
                                if( window.clearpayReturnUrl === false ) {
                                    if (typeof AfterPay.initialize === "function") {
                                        AfterPay.initialize(window.clearpayCountryCode);
                                    } else {
                                        AfterPay.init();
                                    }
                                }
                                else {
                                    AfterPay.init({
                                        relativeCallbackURL: window.clearpayReturnUrl
                                    });
                                }

                                switch (window.Clearpay.redirectMode) {
                                    case 'lightbox':
                                        AfterPay.display({
                                            token: response.token
                                        });

                                        break;

                                    case 'redirect':
                                        AfterPay.redirect({
                                            token: response.token
                                        });

                                        break;
                                }
                            } else {
                                if (response.redirect) {
                                    this.isSuccess = false;
                                    location.href = response.redirect;
                                } else {
                                    alert(response.message);
                                }
                            }

                        }.bind(this),
                        onFailure: function () {
                            alert('Clearpay Gateway is not available.');
                        }
                    }
                );
            } else {
                /**
                 * Call original function
                 */
                reviewSave.apply(this, arguments);
            }
        };
    }
})();