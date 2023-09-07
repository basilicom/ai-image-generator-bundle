import SimpleText2ImageWindow from "../lib/ExtJs/SimpleText2ImageWindow";

pimcore.registerNS("pimcore.document.editables.image");
pimcore.document.editables.image = Class.create(pimcore.document.editables.image, {
    label: "Generate Image",
    button: null,

    initialize: function ($super, id, name, config, data, inherited) {
        $super(id, name, config, data, inherited);

        this.element = Ext.get(this.id);
        this.element.insertHtml("beforeEnd", "<div class=\"ai-image-generator-button\"><button>" + t(this.label) + "</button></div>");

        this.button = this.element.dom.querySelector(".ai-image-generator-button button");
        this.button.onclick = this.generateAiImage.bind(this)
    },

    generateAiImage: function () {
        const simpleText2ImageWindow = new SimpleText2ImageWindow(
            window.editWindow.document.id,
            'document'
        );

        simpleText2ImageWindow
            .getWindow(
                () => {
                    this.button.innerHTML = t('Loading...');
                },
                (jsonData) => {
                    this.resetData();
                    this.datax.id = jsonData.id;

                    this.updateImage();
                    this.checkValue(true);
                },
                () => {
                    this.button.innerHTML = t(this.label);
                },
            )
            .show();
    }
});
