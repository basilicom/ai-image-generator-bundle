import {aspectRatioStore, aspectRatioStoreDefault} from "./AspectRatioStore";
import AiImageGenerator from "../AiImageGenerator";
import ConfigStorage from "../ConfigStorage";
import AdapterEnum from "../AdapterEnum";

export default class SimpleText2ImageWindow {
    id
    context

    constructor(id, context) {
        this.id = id;
        this.context = context;
    }

    getWindow(onRequest, onSuccess, onDone) {
        const prompt = window.localStorage.getItem('prompt') ?? '';
        const seed = window.localStorage.getItem('seed') ?? -1;
        const aspectRatio = window.localStorage.getItem('aspectRatio') ?? aspectRatioStoreDefault;
        aspectRatioStore.load();

        let items = [
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
                fieldLabel: t('Prompt'),
                tooltip: '<i>' + t('Leave empty to create prompt from various properties.') + '</i>',
            },
            {
                xtype: 'tbtext',
                fieldLabel: '',
                text: '<i>' + t('Leave empty to create prompt automatically.') + '</i>',
                scale: 'small',
                padding: '0 0 20 105',
                width: '100%',
            }
        ];

        const adapter = ConfigStorage.get('adapter', null);
        if (adapter === AdapterEnum.Automatic1111 || adapter === AdapterEnum.DreamStudio) {
            items = [
                ...items,
                {
                    xtype: 'numberfield',
                    name: 'seed',
                    value: seed,
                    itemId: 'seed',
                    width: '100%',
                    fieldLabel: t('Seed')
                }
            ];
        }

        const settingsWindow = new Ext.Window({
            title: t('Generate image'),
            width: 400,
            bodyStyle: 'padding: 10px;',
            closable: false,
            plain: true,
            items: items,
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
                        const seed = settingsWindow.getComponent("seed") ? settingsWindow.getComponent("seed").getValue() : -1;
                        window.localStorage.setItem('prompt', prompt);
                        window.localStorage.setItem('aspectRatio', aspectRatio);
                        window.localStorage.setItem('seed', seed);

                        AiImageGenerator.generateAiImageByContext(
                            {
                                context: this.context,
                                id: this.id,
                                prompt: prompt,
                                aspectRatio: aspectRatio,
                                seed: seed
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
