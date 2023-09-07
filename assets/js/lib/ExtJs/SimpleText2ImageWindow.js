import {aspectRatioStore, aspectRatioStoreDefault} from "./AspectRatioStore";
import AiImageGenerator from "../AiImageGenerator";

export default class SimpleText2ImageWindow {
    id
    context

    constructor(id, context) {
        this.id = id;
        this.context = context;
    }

    getWindow(onRequest, onSuccess, onDone) {
        const prompt = window.localStorage.getItem('prompt') ?? '';
        const aspectRatio = window.localStorage.getItem('aspectRatio') ?? aspectRatioStoreDefault;
        aspectRatioStore.load();

        const settingsWindow = new Ext.Window({
            title: t('Generate image'),
            width: 400,
            bodyStyle: 'padding: 10px;',
            closable: false,
            plain: true,
            items: [
                {
                    xtype: 'combobox',
                    itemId: 'aspectRatio',
                    name: 'aspectRatio',
                    triggerAction: 'all',
                    selectOnFocus: true,
                    fieldLabel: t('Aspect Ratio'),
                    store: aspectRatioStore,
                    value: aspectRatio,
                    displayField: 'key',
                    valueField: 'value',
                    width: '100%',
                },
                {
                    xtype: 'textareafield',
                    itemId: 'prompt',
                    name: 'prompt',
                    value: prompt,
                    grow: true,
                    width: '100%',
                    fieldLabel: t('Prompt')
                },
                {
                    xtype: 'tbtext',
                    text: '<i>' + t('Leave empty to create prompt from various properties.') + '</i>',
                    scale: "medium"
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
                        const prompt = settingsWindow.getComponent("prompt").getValue();
                        const aspectRatio = settingsWindow.getComponent("aspectRatio").getValue();
                        window.localStorage.setItem('prompt', prompt);
                        window.localStorage.setItem('aspectRatio', aspectRatio);

                        AiImageGenerator.generateAiImageByContext(
                            {
                                context: this.context,
                                id: this.id,
                                prompt: prompt,
                                aspectRatio: aspectRatio
                            },
                            () => {
                                onRequest();
                                settingsWindow.close();
                            },
                            (jsonData) => {
                                onSuccess(jsonData);
                            },
                            (jsonData) => {
                                pimcore.helpers.showNotification(t('error'), jsonData.message, 'error');
                            },
                            () => {
                                onDone();
                            }
                        );
                    }.bind(this)
                }]
        });

        return settingsWindow;
    }
}
