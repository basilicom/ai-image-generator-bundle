import AiImageGenerator from "../lib/AiImageGenerator";

document.addEventListener(pimcore.events.postOpenAsset, (e) => {
    const asset = e.detail.asset

    asset.toolbar.insert(3, {
        text: 'Upscale',
        scale: 'medium',
        handler: function (asset, button) {
            AiImageGenerator.upscaleImage(
                {
                    'id': asset.id,
                },
                () => {
                    button.setText('Upscaling in progress ...');
                },
                (jsonData) => {
                    asset.reload();
                },
                (jsonData) => {
                    pimcore.helpers.showNotification(t("error"), jsonData.message, "error");
                },
                () => {
                    button.setText('Upscale');
                },
            );
        }.bind(this, asset),
    })

    pimcore.layout.refresh()
})
