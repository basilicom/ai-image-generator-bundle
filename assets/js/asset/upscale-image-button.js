import AiImageGenerator from '../lib/AiImageGenerator';
import FeatureEnum from "../lib/FeatureEnum";
import FeatureHelper from "../lib/FeatureHelper";

document.addEventListener(pimcore.events.postOpenAsset, (e) => {
    if(!FeatureHelper.isFeatureEnabled(FeatureEnum.UPSCALE)) {
        return;
    }

    const asset = e.detail.asset
    const label = t('Upscale');
    const progressLabel = t('Upscaling in progress ...');
    const buttonEnabled = asset.data.customSettings.imageWidth < 4096 && asset.data.customSettings.imageHeight < 4096;
    asset.toolbar.insert(3, {
        text: label,
        scale: 'medium',
        disabled: !buttonEnabled,
        tooltip: (buttonEnabled ? null : t('Upscaling is only possible up to 4096 pixels.')),
        handler: function (asset, button) {
            AiImageGenerator.upscaleImage(
                {
                    id: asset.id
                },
                () => {
                    button.setText(progressLabel);
                    button.setDisabled(true);
                },
                (jsonData) => {
                    asset.reload();
                },
                (jsonData) => {
                    pimcore.helpers.showNotification(t('error'), jsonData.message, 'error');
                },
                () => {
                    button.setDisabled(!buttonEnabled);
                    button.setText(label);
                },
            );
        }.bind(this, asset),
    })

    pimcore.layout.refresh()
})
