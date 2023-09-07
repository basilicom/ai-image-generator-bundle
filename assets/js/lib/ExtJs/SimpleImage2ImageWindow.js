import AiImageGenerator from "../AiImageGenerator";
import AdapterEnum from "../AdapterEnum";
import ConfigStorage from "../ConfigStorage";

export const IMAGE_VARIATIONS = 'image_variations';
export const IMAGE_BACKGROUND_GENERATION = 'image_background_generation';

export default class SimpleImage2ImageWindow {
    asset
    context

    constructor(asset, context) {
        this.asset = asset;
        this.context = context;
    }

    getWindow(onRequest, onSuccess, onDone) {
        const previousPrompt = window.localStorage.getItem('prompt') ?? '';
        const prompt = this.asset.data.metadata.hasOwnProperty('prompt~') ? this.asset.data.metadata['prompt~'].data : previousPrompt;
        const seed = this.asset.data.metadata.hasOwnProperty('seed~') ? this.asset.data.metadata['seed~'].data : -1;

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
                        const seed = settingsWindow.getComponent("seed") ? settingsWindow.getComponent("seed").getValue() : -1;
                        window.localStorage.setItem('prompt', prompt);
                        window.localStorage.setItem('seed', seed);

                        const payload = {
                            id: this.asset.id,
                            prompt: prompt,
                            seed: seed
                        };

                        const extendedOnRequest = () => {
                            onRequest();
                            settingsWindow.close();
                        };

                        const onError = (jsonData) => {
                            pimcore.helpers.showNotification(t('error'), jsonData.message, 'error');
                        };

                        if (this.context === IMAGE_VARIATIONS) {
                            AiImageGenerator.varyImage(payload, extendedOnRequest, onSuccess, onError, onDone);
                        } else if (this.context === IMAGE_BACKGROUND_GENERATION) {
                            AiImageGenerator.inpaintBackground(payload, extendedOnRequest, onSuccess, onError, onDone);
                        }
                    }.bind(this)
                }]
        });

        return settingsWindow;
    }
}
