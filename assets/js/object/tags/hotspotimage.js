import SimpleText2ImageWindow from "../../lib/ExtJs/SimpleText2ImageWindow";

pimcore.registerNS('pimcore.object.tags.hotspotimage');
pimcore.object.tags.hotspotimage = Class.create(pimcore.object.tags.hotspotimage, {
    label: 'Generate Image',
    button: null,

    getLayoutEdit: function ($super) {
        const component = $super();
        const toolbar = component.getDockedItems('toolbar')[0];
        this.button = new Ext.button.Button({
            text: t(this.label),
            handler: this.generateAiImage.bind(this)
        });
        toolbar.add(this.button);

        return component;
    },

    generateAiImage: function () {
        const container = this.component.body.dom;
        const simpleText2ImageWindow = new SimpleText2ImageWindow(
            this.context.objectId,
            'object'
        );

        simpleText2ImageWindow
            .getWindow(
                () => {
                    container.classList.add('ai-image-loader');
                    this.button.innerHTML = t('Loading...');
                },
                (jsonData) => {
                    this.empty(true);

                    if (this.data.id !== jsonData.id) {
                        this.dirty = true;
                    }
                    this.data.id = jsonData.id;

                    this.updateImage();
                },
                () => {
                    container.classList.remove('ai-image-loader');
                    this.button.innerHTML = t(this.label);
                },
            )
            .show();
    }
});
