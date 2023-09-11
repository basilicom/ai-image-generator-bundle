import {aspectRatioStore, aspectRatioStoreDefault} from "./AspectRatioStore";
import AiImageGenerator from "../AiImageGenerator";
import FeatureHelper from "../FeatureHelper";
import FeatureEnum from "../FeatureEnum";

export default class SimpleText2ImageWindow {
    id
    context

    constructor(id, context) {
        this.id = id;
        this.context = context;
    }

    getWindow(onRequest, onSuccess, onDone) {
        const prompt = window.localStorage.getItem('prompt') ?? '';

        let items = [
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

        if (FeatureHelper.isAspectRatioSupported(FeatureEnum.TXT2IMG)) {
            aspectRatioStore.load();
            const aspectRatio = window.localStorage.getItem('aspectRatio') ?? aspectRatioStoreDefault;
            items = [
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
                ...items,
            ];
        }

        if (FeatureHelper.isSeedingSupported(FeatureEnum.TXT2IMG)) {
            const seed = window.localStorage.getItem('seed') ?? -1;
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
                        window.localStorage.setItem('prompt', prompt);

                        let payload = {
                            context: this.context,
                            id: this.id,
                            prompt: prompt
                        };


                        if (FeatureHelper.isAspectRatioSupported(FeatureEnum.TXT2IMG)) {
                            const aspectRatio = settingsWindow.getComponent("aspectRatio").getValue();
                            window.localStorage.setItem('aspectRatio', aspectRatio);

                            payload.aspectRatio = aspectRatio;
                        }
                        if (FeatureHelper.isSeedingSupported(FeatureEnum.TXT2IMG)) {
                            const seed = settingsWindow.getComponent("seed") ? settingsWindow.getComponent("seed").getValue() : -1;
                            window.localStorage.setItem('seed', seed);

                            payload.seed = seed;
                        }

                        AiImageGenerator.generateAiImageByContext(
                            payload,
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
