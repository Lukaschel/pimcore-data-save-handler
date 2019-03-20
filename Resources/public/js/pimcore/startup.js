pimcore.registerNS("pimcore.plugin.PimcoreDataSaveHandlerBundle");

pimcore.plugin.PimcoreDataSaveHandlerBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.PimcoreDataSaveHandlerBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        // alert("PimcoreDataSaveHandlerBundle ready!");
    }
});

var PimcoreDataSaveHandlerBundlePlugin = new pimcore.plugin.PimcoreDataSaveHandlerBundle();

