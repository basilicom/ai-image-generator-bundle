import AiImageGenerator from '../AiImageGenerator';
import FeatureEnum from '../FeatureEnum';
import FeatureHelper from '../FeatureHelper';

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
        const branding = window.localStorage.getItem('branding') ?? false;

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

        if (FeatureHelper.isSeedingSupported(this.context)) {
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

        if (this.context === FeatureEnum.INPAINT_BACKGROUND) {
            items = [
                ...items,
                {
                    xtype: 'checkbox',
                    itemId: 'branding',
                    name: 'branding',
                    checked: branding,
                    fieldLabel: t('Branding'),
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
                        const prompt = settingsWindow.getComponent('prompt').getValue();
                        const seed = settingsWindow.getComponent('seed') ? settingsWindow.getComponent('seed').getValue() : -1;
                        window.localStorage.setItem('prompt', prompt);
                        window.localStorage.setItem('seed', seed);

                        const payload = {
                            id: this.asset.id,
                            prompt: prompt,
                            seed: seed
                        };

                        if (settingsWindow.getComponent('branding')) {
                            const branding = settingsWindow.getComponent('branding').getValue();
                            window.localStorage.setItem('branding', branding);
                            payload.brand = branding;
                        }

                        const extendedOnRequest = () => {
                            onRequest();
                            settingsWindow.close();
                        };

                        const onError = (jsonData) => {
                            pimcore.helpers.showNotification(t('error'), jsonData.message, 'error');
                        };

                        if (this.context === FeatureEnum.IMAGE_VARIATIONS) {
                            AiImageGenerator.varyImage(payload, extendedOnRequest, onSuccess, onError, onDone);
                        } else if (this.context === FeatureEnum.INPAINT_BACKGROUND) {
                            AiImageGenerator.inpaintBackground(payload, extendedOnRequest, onSuccess, onError, onDone);
                        }
                    }.bind(this)
                }]
        });

        return settingsWindow;
    }
}
