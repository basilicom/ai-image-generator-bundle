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
        const documentId = window.editWindow.document.id;
        const width = this.element.getWidth();
        const height = this.element.getHeight();

        const settingsWindow = new Ext.Window({
            title: t('Generate image'),
            width: 400,
            bodyStyle: 'padding: 10px;',
            closable: false,
            plain: true,
            items: [
                {
                    xtype: "displayfield",
                    name: "promptInfo",
                    itemId: "info",
                    fieldLabel: "",
                    value: t("Leave empty if context should be generated dynamically.")
                },
                {
                    xtype: 'textareafield',
                    name: 'prompt',
                    value: "",
                    itemId: 'prompt',
                    grow: true,
                    width: '100%',
                    fieldLabel: t('Prompt')
                }
            ],
            buttons: [
                {
                    text: t('cancel'),
                    iconCls: 'pimcore_icon_cancel',
                    handler: function () {
                        settingsWindow.close();
                    }
                },
                {
                    text: t('apply'),
                    iconCls: 'pimcore_icon_apply',
                    handler: function () {
                        const payload = {
                            context: 'document',
                            id: documentId,
                            width: width,
                            height: height
                        };

                        const onRequest = () => {
                            settingsWindow.close();
                            this.button.innerHTML = t('Loading...');
                        }

                        const onSuccess = (jsonData) => {
                            this.resetData();
                            this.datax.id = jsonData.id;

                            this.updateImage();
                            this.checkValue(true);
                        };

                        const onError = (jsonData) => {
                            pimcore.helpers.showNotification(t("error"), jsonData.message, "error");
                        };

                        const onDone = () => {
                            this.button.innerHTML = t(this.label);
                        };

                        const prompt = settingsWindow.getComponent("prompt").getValue();
                        if (prompt.length === 0) {
                            AiImageGenerator.generateAiImageByContext(payload, onRequest, onSuccess, onError, onDone);
                        } else {
                            AiImageGenerator.generateAiImageByPrompt({...payload, prompt}, onRequest, onSuccess, onError, onDone);
                        }
                    }.bind(this)
                }]
        });

        settingsWindow.show();
    }
});
