/**
 *
 * @see app/design/frontend/base/default/template/clearpay/form/payovertime_custom.phtml
 * @see app/design/frontend/base/default/template/clearpay/checkout/title_custom.phtml
 */
;(function (Prototype, Element) {

    if (! Prototype || ! Element) {
        return;
    }

    var Clearpay = window.Clearpay = window.Clearpay || {};
    Clearpay.CheckoutForm = Clearpay.CheckoutForm || {};

    var renderCheckoutTemplate = function (template, config) {

        return template.gsub(config.orderAmountSubstitution, config.orderAmount)
            .gsub(config.regionSpecificSubstitution, config.regionText)
            .gsub(config.installmentAmountSubstitution, config.installmentAmount)
            .gsub(config.installmentAmountSubstitutionLast, config.installmentAmountLast)
            .gsub(config.imageCircleOneSubstitution, config.imageCircleOne)
            .gsub(config.imageCircleTwoSubstitution, config.imageCircleTwo)
            .gsub(config.imageCircleThreeSubstitution, config.imageCircleThree)
            .gsub(config.imageCircleFourSubstitution, config.imageCircleFour)
            .gsub(config.clearpayLogoSubstitution, config.clearpayLogo);
    };

    Clearpay.CheckoutForm.detailsConfiguration = null;

    Clearpay.CheckoutForm.detailsRender = function () {

        var configuration = this.detailsConfiguration;
        if (! configuration instanceof Object) {
            console.warn("Clearpay: checkout details configuration not initialized.");
            return;
        }
        try {
            var payOverTimeForms = Prototype.Selector.select(configuration.cssSelector);
            Element.insert(payOverTimeForms[0], {
                after: renderCheckoutTemplate(configuration.template, configuration)
            });
        } catch (e) {
        }
    };

    Clearpay.CheckoutForm.titleConfiguration = null;

    Clearpay.CheckoutForm.titleRender = function () {

        var configuration = this.titleConfiguration;
        if (! configuration instanceof Object) {
            console.warn("Clearpay: checkout headline configuration not initialized.");
            return;
        }
        try {
            var payOverTimeForms = Prototype.Selector.select(configuration.cssSelector);
            Element.insert(payOverTimeForms[0], {
                before: renderCheckoutTemplate(configuration.template, configuration)
            });
        } catch (e) {
        }
    };

})(window.Prototype, window.Element);
