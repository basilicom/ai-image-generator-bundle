import SimpleImage2ImageWindow from "../lib/ExtJs/SimpleImage2ImageWindow";
import FeatureEnum from "../lib/FeatureEnum";
import FeatureHelper from "../lib/FeatureHelper";

document.addEventListener(pimcore.events.postOpenAsset, (e) => {
    if(!FeatureHelper.isFeatureEnabled(FeatureEnum.INPAINT_BACKGROUND)) {
        return;
    }

    const asset = e.detail.asset
    const label = t('Generate Background');
    const progressLabel = t('Generating background ...');

    asset.toolbar.insert(3, {
        text: label,
        scale: 'medium',
        handler: function (asset, button) {
            const settingsWindows = new SimpleImage2ImageWindow(asset, FeatureEnum.INPAINT_BACKGROUND)
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
