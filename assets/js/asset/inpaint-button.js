import FeatureEnum from "../lib/FeatureEnum";
import FeatureHelper from "../lib/FeatureHelper";
import InpaintingWindow from "../lib/ExtJs/InpaintingWindow";

document.addEventListener(pimcore.events.postOpenAsset, (e) => {
    if(!FeatureHelper.isFeatureEnabled(FeatureEnum.INPAINT)) {
        return;
    }

    const asset = e.detail.asset
    const label = t('Inpaint');
    const progressLabel = t('Generating in progress ...');

    asset.toolbar.insert(3, {
        text: label,
        scale: 'medium',
        handler: function (asset, button) {
            const inpaintingWindow = new InpaintingWindow(asset)
            inpaintingWindow
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
