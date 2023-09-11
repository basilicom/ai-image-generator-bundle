import AiImageGenerator from "../AiImageGenerator";

// todo => refactor!

export default class InpaintingWindow {
    asset

    constructor(asset) {
        this.asset = asset;
    }

    getWindow(onRequest, onSuccess, onDone) {
        const onError = () => {

        };

        return Ext.create('AiImageGeneratorBundle.view.CanvasWindow', {
            asset: this.asset,
            onRequest,
            onSuccess,
            onError,
            onDone,
        });
    }
}

let asset;
let backgroundCanvas;
let backgroundImage;
let backgroundImageNewWidth;
let backgroundImageNewHeight;
let backgroundImageX;
let backgroundImageY;
let canvas;
let isDrawing = false;
let lineWidth = 5;
let x;
let y;

const drawLine = function (startX, startY, endX, endY) {
    const ctx = canvas.getContext('2d');

    ctx.beginPath();
    ctx.strokeStyle = '#000000';
    ctx.lineWidth = lineWidth;
    ctx.lineCap = 'round';
    ctx.moveTo(startX, startY);
    ctx.lineTo(endX, endY);
    ctx.stroke();
    ctx.closePath();
}

const receiveMaskImage = function () {
    const tempCanvas = document.createElement('canvas');
    tempCanvas.width = backgroundImageNewWidth;
    tempCanvas.height = backgroundImageNewHeight;
    tempCanvas.getContext('2d').fillStyle = "white";
    tempCanvas.getContext('2d').fillRect(0, 0, backgroundImageNewWidth, backgroundImageNewHeight);
    tempCanvas.getContext('2d').drawImage(
        canvas,
        backgroundImageX,
        backgroundImageY,
        backgroundImageNewWidth,
        backgroundImageNewHeight,
        0,
        0,
        backgroundImageNewWidth,
        backgroundImageNewHeight
    );

    return getImage(tempCanvas);
}

const getImage = function (canvas) {
    const dataUrl = canvas.toDataURL('image/jpeg', 1);
    const byteString = atob(dataUrl.split(',')[1]);
    const mimeString = dataUrl.split(',')[0].split(':')[1].split(';')[0];
    const ab = new ArrayBuffer(byteString.length);
    const ia = new Uint8Array(ab);

    for (let i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }

    return new Blob([ab], {type: mimeString});
}

const drawBackground = function (img) {
    const aspectRatio = img.width / img.height;
    const canvasAspectRatio = canvas.width / canvas.height;
    const scaleFactor = (aspectRatio > canvasAspectRatio)
        ? canvas.width / img.width
        : canvas.height / img.height;
    const x = (canvas.width - (img.width * scaleFactor)) / 2;
    const y = (canvas.height - (img.height * scaleFactor)) / 2;

    backgroundImageNewWidth = img.width * scaleFactor;
    backgroundImageNewHeight = img.height * scaleFactor;
    backgroundImageX = x;
    backgroundImageY = y;

    backgroundCanvas.getContext('2d').drawImage(img, x, y, img.width * scaleFactor, img.height * scaleFactor);
    backgroundImage = img;
}

Ext.define('AiImageGeneratorBundle.view.CanvasWindow', {
    extend: 'Ext.Window',
    xtype: 'canvaswindow', // This is the xtype to use in your code

    title: 'Inpainting',
    width: 1024,
    height: 1024 + 220,
    layout: 'fit',
    modal: true,
    closable: true, // Allow closing the window

    config: {
        asset: null,
        onRequest: () => {
        },
        onSuccess: () => {
        },
        onError: () => {
        },
        onDone: () => {
        }
    },

    initComponent: function () {
        asset = this.getAsset();

        const prompt = window.localStorage.getItem('prompt') ?? '';
        const onRequest = this.getOnRequest();
        const onSuccess = this.getOnSuccess();
        const onError = this.getOnError();
        const onDone = this.getOnDone();

        const img = new Image();
        img.src = asset.data.url;

        this.items = [
            {
                xtype: 'container',
                items: [
                    {
                        xtype: 'component',
                        itemId: 'canvasContainer',
                        width: '100%',
                        height: 1024,
                        listeners: {
                            afterrender: function () {
                                canvas = document.createElement('canvas');
                                canvas.width = 1024;
                                canvas.height = 1024;
                                canvas.style.position = 'relative';
                                canvas.style.opacity = .7;
                                canvas.style.zIndex = 10;

                                backgroundCanvas = canvas.cloneNode(true);
                                backgroundCanvas.style.position = 'absolute';
                                backgroundCanvas.style.top = 0;
                                backgroundCanvas.style.left = 0;
                                backgroundCanvas.style.zIndex = 5;
                                backgroundCanvas.style.pointerEvents = 'none';
                                backgroundCanvas.style.borderTop = '1px solid #000';
                                backgroundCanvas.style.borderBottom = '1px solid #000';
                                backgroundCanvas.style.opacity = 1;
                                backgroundCanvas.style.background = '#fff';

                                this.getEl().dom.appendChild(backgroundCanvas);
                                this.getEl().dom.appendChild(canvas);

                                canvas.addEventListener('mousedown', function (event) {
                                    isDrawing = true;
                                    lineWidth = Ext.getCmp('ai_image_generator_bundle_line_width_slider').getValue();

                                    x = event.offsetX;
                                    y = event.offsetY;
                                });

                                canvas.addEventListener('mousemove', function (event) {
                                    if (!isDrawing) return;

                                    let xM = event.offsetX;
                                    let yM = event.offsetY;
                                    drawLine(x, y, xM, yM);
                                    x = xM;
                                    y = yM;
                                });

                                canvas.addEventListener('mouseup', function () {
                                    const ctx = canvas.getContext('2d');

                                    isDrawing = false;
                                    ctx.beginPath();
                                });

                                img.onload = function () {
                                    drawBackground(img);
                                };
                            }
                        }
                    },
                    {
                        xtype: 'textareafield',
                        itemId: 'prompt',
                        name: 'prompt',
                        value: prompt,
                        grow: true,
                        width: 'calc(100% - 20px)',
                        fieldLabel: t('Prompt'),
                        padding: '10'
                    }
                ]
            }
        ];

        this.buttons = [
            {
                text: t('cancel'),
                iconCls: 'pimcore_icon_cancel',
                handler: function () {
                    this.up('window').close();
                }
            },
            {
                text: t('Generate'),
                handler: function () {
                    // Save or process the content of the temporary canvas as needed
                    const prompt = this.up('window').down('#prompt').getValue();
                    window.localStorage.setItem('prompt', prompt);

                    const confirmButton = this.up('window').down('#confirmButton');
                    const generateButton = this;

                    // Create a FormData object to send the image data as a file
                    AiImageGenerator.inpaintImage(
                        {
                            id: asset.id,
                            prompt: prompt,
                            mask: receiveMaskImage(),
                            draft: true
                        },
                        () => {
                            confirmButton.setDisabled(true);
                            generateButton.setDisabled(true);
                        },
                        (response) => {
                            confirmButton.setDisabled(false);

                            const img = new Image();
                            img.src = 'data:image/png;base64,' + response.image;
                            img.onload = function () {
                                drawBackground(img);
                            };
                        },
                        () => {
                        },
                        () => {
                            generateButton.setDisabled(false);
                        }
                    );
                }
            },
            {
                text: t('apply'),
                iconCls: 'pimcore_icon_apply',
                id: 'confirmButton',
                disabled: true,
                handler: function () {
                    // Save or process the content of the temporary canvas as needed
                    const prompt = this.up('window').down('#prompt').getValue();
                    window.localStorage.setItem('prompt', prompt);

                    const tempCanvas = document.createElement('canvas');
                    tempCanvas.width = backgroundImageNewWidth;
                    tempCanvas.height = backgroundImageNewHeight;
                    tempCanvas.getContext('2d').drawImage(
                        backgroundCanvas,
                        backgroundImageX,
                        backgroundImageY,
                        backgroundImageNewWidth,
                        backgroundImageNewHeight,
                        0,
                        0,
                        backgroundImageNewWidth,
                        backgroundImageNewHeight
                    );

                    const finalAsset = getImage(tempCanvas);

                    // Create a FormData object to send the image data as a file
                    AiImageGenerator.save(
                        {
                            id: asset.id,
                            data: finalAsset,
                        },
                        onRequest,
                        () => {
                            asset.data.url = finalAsset;
                            asset.reload();
                            this.up('window').close();
                        },
                        onError,
                        onDone
                    );
                }
            }
        ];

        this.tbar = [
            {
                text: 'Clear',
                handler: function () {
                    const ctx = canvas.getContext('2d');
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    drawBackground(backgroundImage);
                }
            },
            {
                xtype: 'slider',
                fieldLabel: 'Line Width',
                width: 500,
                minValue: 5,
                maxValue: 50,
                value: 20,
                id: 'ai_image_generator_bundle_line_width_slider'
            }
        ]

        this.callParent(arguments);
    }
});
