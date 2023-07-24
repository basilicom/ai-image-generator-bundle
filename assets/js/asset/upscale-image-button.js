import AiImageGenerator from '../lib/AiImageGenerator';
import AdapterEnum from "../lib/AdapterEnum";
import ConfigStorage from "../lib/ConfigStorage";

document.addEventListener(pimcore.events.postOpenAsset, (e) => {
    const adapter = ConfigStorage.get('adapter', null);
    if (adapter === AdapterEnum.OpenAi) {
        return;
    }

    const asset = e.detail.asset

    const label = t('Upscale');
    const progressLabel = t('Upscaling in progress ...');
    asset.toolbar.insert(3, {
        text: label,
        scale: 'medium',
        handler: function (asset, button) {
            AiImageGenerator.upscaleImage(
                {
                    id: asset.id
                },
                () => {
                    button.setText(progressLabel);
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
        }.bind(this, asset),
    })

    pimcore.layout.refresh()
})
