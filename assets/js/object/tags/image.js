pimcore.registerNS("pimcore.object.tags.image");
pimcore.object.tags.image = Class.create(pimcore.object.tags.image, {
    type: "image",
    dirty: false,

    initialize: function (data, fieldConfig) {
        if (data) {
            this.data = data;
        } else {
            this.data = {};
        }

        this.fieldConfig = fieldConfig;

        console.log("whuhuhU");
    }
});
