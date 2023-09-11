import SimpleImage2ImageWindow from "../lib/ExtJs/SimpleImage2ImageWindow";
import FeatureEnum from "../lib/FeatureEnum";
import FeatureHelper from "../lib/FeatureHelper";

document.addEventListener(pimcore.events.postOpenAsset, (e) => {
    if(!FeatureHelper.isFeatureEnabled(FeatureEnum.IMAGE_VARIATIONS)) {
        return;
    }

    const asset = e.detail.asset
    const label = t('Vary image');
    const progressLabel = t('Generating in progress ...');

    asset.toolbar.insert(3, {
        text: label,
        scale: 'medium',
        handler: function (asset, button) {
            const settingsWindows = new SimpleImage2ImageWindow(asset, FeatureEnum.IMAGE_VARIATIONS)
            settingsWindows
                .getWindow(
                    () => { button.setText(progressLabel) },
                    () => { asset.reload() },
                    () => { button.setText(label) }
                )
                .show();
        }.bind(this, asset),
    })

    pimcore.layout.refresh()
})
