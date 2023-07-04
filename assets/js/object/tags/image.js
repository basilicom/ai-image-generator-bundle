import AiImageGenerator from "../../lib/AiImageGenerator";

pimcore.registerNS("pimcore.object.tags.image");
pimcore.object.tags.image = Class.create(pimcore.object.tags.image, {
    label: "Generate Image",
    button: null,

    getLayoutEdit: function ($super) {
        const component = $super();
        const toolbar = component.getDockedItems('toolbar')[0];
        this.button = new Ext.button.Button({
            text: this.label, // Button text
            handler: this.generateAiImage.bind(this)
        });
        toolbar.add(this.button);

        return component;
    },

    generateAiImage: function () {
        AiImageGenerator.generateAiImage(
            {
                context: 'object',
                id: this.object.id,
                width: this.component.config.width,
                height: this.component.config.height
            },
            () => {
                this.button.innerHTML = 'Loading...';
            },
            (jsonData) => {
                this.empty(true);

                if (this.data.id !== jsonData.id) {
                    this.dirty = true;
                }
                this.data.id = jsonData.id;

                this.updateImage();
            },
            (jsonData) => {
                pimcore.helpers.showNotification(t("error"), jsonData.message, "error");
            },
            () => {
                this.button.innerHTML = this.label;
            }
        );
    }
});
