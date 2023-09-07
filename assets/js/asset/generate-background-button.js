import ConfigStorage from '../lib/ConfigStorage';
import AdapterEnum from '../lib/AdapterEnum';
import SimpleImage2ImageWindow, {IMAGE_BACKGROUND_GENERATION} from "../lib/ExtJs/SimpleImage2ImageWindow";

document.addEventListener(pimcore.events.postOpenAsset, (e) => {
    const adapter = ConfigStorage.get('adapter', null);
    if (adapter === AdapterEnum.OpenAi || adapter === AdapterEnum.DreamStudio) {
        return;
    }

    const asset = e.detail.asset
    const label = t('Generate Background');
    const progressLabel = t('Generating background ...');

    asset.toolbar.insert(3, {
        text: label,
        scale: 'medium',
        handler: function (asset, button) {
            const settingsWindows = new SimpleImage2ImageWindow(asset, IMAGE_BACKGROUND_GENERATION)
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
