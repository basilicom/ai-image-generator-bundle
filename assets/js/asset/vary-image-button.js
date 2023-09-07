import SimpleImage2ImageWindow, {IMAGE_VARIATIONS} from "../lib/ExtJs/SimpleImage2ImageWindow";

document.addEventListener(pimcore.events.postOpenAsset, (e) => {
    const asset = e.detail.asset
    const label = t('Vary image');
    const progressLabel = t('Generating in progress ...');

    asset.toolbar.insert(3, {
        text: label,
        scale: 'medium',
        handler: function (asset, button) {
            const settingsWindows = new SimpleImage2ImageWindow(asset, IMAGE_VARIATIONS)
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
