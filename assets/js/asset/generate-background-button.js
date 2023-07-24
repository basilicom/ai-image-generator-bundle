import AiImageGenerator from '../lib/AiImageGenerator';
import ConfigStorage from '../lib/ConfigStorage';
import AdapterEnum from '../lib/AdapterEnum';

document.addEventListener(pimcore.events.postOpenAsset, (e) => {
    const adapter = ConfigStorage.get('adapter', null);
    if(adapter === AdapterEnum.OpenAi || adapter === AdapterEnum.DreamStudio) {
        return;
    }

    const asset = e.detail.asset

    const label = t('Generate Background');
    const progressLabel = t('Generating background ...');
    asset.toolbar.insert(3, {
        text: label,
        scale: 'medium',
        handler: function (asset, button) {
            const previousPrompt = window.localStorage.getItem('prompt') ?? '';
            const prompt = asset.data.metadata.hasOwnProperty('prompt~') ? asset.data.metadata['prompt~'].data : previousPrompt;

            const settingsWindow = new Ext.Window({
                title: t('Settings for background generation'),
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

                            window.localStorage.setItem('prompt', prompt);

                            AiImageGenerator.inpaintBackground(
                                {
                                    id: asset.id,
                                    prompt: prompt,
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
