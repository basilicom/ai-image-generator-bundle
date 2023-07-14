import AiImageGenerator from "../lib/AiImageGenerator";

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
        AiImageGenerator.generateAiImage(
            {
                context: 'document',
                id: window.editWindow.document.id,
                width: this.element.getWidth(),
                height: this.element.getHeight(),
                prompt: this.datax.alt,
            },
            () => {
                this.button.innerHTML = t('Loading...');
            },
            (jsonData) => {
                this.resetData();
                this.datax.id = jsonData.id;

                this.updateImage();
                this.checkValue(true);
            },
            (jsonData) => {
                pimcore.helpers.showNotification(t("error"), jsonData.message, "error");
            },
            () => {
                this.button.innerHTML = t(this.label);
            }
        );
    }
});
