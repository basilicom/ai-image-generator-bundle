import AiImageGenerator from '../lib/AiImageGenerator';

document.addEventListener(pimcore.events.postOpenAsset, (e) => {
    const asset = e.detail.asset

    const label = t('Vary image');
    const progressLabel = t('Generating in progress ...');
    asset.toolbar.insert(3, {
        text: label,
        scale: 'medium',
        handler: function (asset, button) {
            const prompt = asset.data.metadata.hasOwnProperty('prompt~') ? asset.data.metadata['prompt~'].data : '';
            const seed = asset.data.metadata.hasOwnProperty('seed~') ? asset.data.metadata['seed~'].data : -1;
            const settingsWindow = new Ext.Window({
                title: t('Settings for image variation'),
                width: 400,
                bodyStyle: 'padding: 10px;',
                closable: false,
                plain: true,
                items: [
                    {
                        xtype: 'textareafield',
                        name: 'prompt',
                        value: prompt,
                        itemId: 'prompt',
                        grow: true,
                        width: '100%',
                        fieldLabel: t('Prompt')
                    },
                    {
                        xtype: 'numberfield',
                        name: 'seed',
                        value: seed,
                        itemId: 'seed',
                        width: '100%',
                        fieldLabel: t('Seed')
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
                            const seed = settingsWindow.getComponent("seed").getValue();

                            AiImageGenerator.varyImage(
                                {
                                    id: asset.id,
                                    prompt: prompt,
                                    seed: seed,
                                },
                                () => {
                                    button.setText(progressLabel);
                                    settingsWindow.close();
                                },
                                (jsonData) => {
                                    asset.reload();
                                },
                                (jsonData) => {
                                    pimcore.helpers.showNotification(t('error'), jsonData.message, 'error');
                                },
                                () => {
                                    button.setText(label);
                                },
                            );
                        }.bind(this)
                    }]
            });

            settingsWindow.show();
        }.bind(this, asset),
    })

    pimcore.layout.refresh()
})
